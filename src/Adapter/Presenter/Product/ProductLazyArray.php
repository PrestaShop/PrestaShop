<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use Configuration;
use Hook;
use Language;
use Link;
use PrestaShop\Decimal\Number;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;

class ProductLazyArray extends AbstractLazyArray
{
    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @var ProductColorsRetriever
     */
    private $productColorsRetriever;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProductPresentationSettings
     */
    protected $settings;

    /**
     * @var array
     */
    protected $product;

    /**
     * @var Language
     */
    private $language;

    public function __construct(
        ProductPresentationSettings $settings,
        array $product,
        Language $language,
        ImageRetriever $imageRetriever,
        Link $link,
        PriceFormatter $priceFormatter,
        ProductColorsRetriever $productColorsRetriever,
        TranslatorInterface $translator
    ) {
        $this->settings = $settings;
        $this->product = $product;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->priceFormatter = $priceFormatter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator = $translator;

        $this->fillImages(
            $settings,
            $product,
            $language
        );

        $this->addPriceInformation(
            $settings,
            $product,
            $language
        );

        $this->addQuantityInformation(
            $settings,
            $product,
            $language
        );

        parent::__construct();
        $this->appendArray($this->product);
    }

    /**
     * @arrayAccess
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->product['id_product'];
    }

    /**
     * @arrayAccess
     *
     * @return array|mixed
     */
    public function getAttributes()
    {
        if (isset($this->product['attributes'])) {
            return $this->product['attributes'];
        }

        return array();
    }

    /**
     * @arrayAccess
     *
     * @return bool
     */
    public function getShowPrice()
    {
        return $this->shouldShowPrice($this->settings, $this->product);
    }

    /**
     * @arrayAccess
     *
     * @return string
     */
    public function getWeightUnit()
    {
        return Configuration::get('PS_WEIGHT_UNIT');
    }

    /**
     * @arrayAccess
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getProductURL($this->product, $this->language);
    }

    /**
     * @arrayAccess
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        return $this->getProductURL($this->product, $this->language, true);
    }

    /**
     * @arrayAccess
     *
     * @return string|null
     */
    public function getAddToCartUrl()
    {
        if ($this->shouldEnableAddToCartButton($this->product, $this->settings)) {
            return $this->link->getAddToCartURL(
                $this->product['id_product'],
                $this->product['id_product_attribute']
            );
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return array|bool
     *
     * @throws InvalidArgumentException
     */
    public function getCondition()
    {
        if (empty($this->product['show_condition'])) {
            return false;
        }

        switch ($this->product['condition']) {
            case 'new':
                return array(
                    'type' => 'new',
                    'label' => $this->translator->trans('New product', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/NewCondition',
                );
            case 'used':
                return array(
                    'type' => 'used',
                    'label' => $this->translator->trans('Used', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/UsedCondition',
                );

                break;
            case 'refurbished':
                return array(
                    'type' => 'refurbished',
                    'label' => $this->translator->trans('Refurbished', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/RefurbishedCondition',
                );

                break;
            default:
                return false;
        }
    }

    /**
     * @arrayAccess
     *
     * @return string|null
     */
    public function getDeliveryInformation()
    {
        if ($this->product['quantity'] > 0) {
            return Configuration::get('PS_LABEL_DELIVERY_TIME_AVAILABLE', $this->language->id);
        } elseif ($this->product['allow_oosp']) {
            return Configuration::get('PS_LABEL_DELIVERY_TIME_OOSBOA', $this->language->id);
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getEmbeddedAttributes()
    {
        $whitelist = $this->getProductAttributeWhitelist();
        $embeddedProductAttributes = array();
        foreach ($this->product as $attribute => $value) {
            if (in_array($attribute, $whitelist)) {
                $embeddedProductAttributes[$attribute] = $value;
            }
        }

        return $embeddedProductAttributes;
    }

    /**
     * @arrayAccess
     *
     * @return string|null
     */
    public function getFileSizeFormatted()
    {
        if (!isset($this->product['attachments'])) {
            return null;
        }
        foreach ($this->product['attachments'] as &$attachment) {
            return Tools::formatBytes($attachment['file_size'], 2);
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getAttachments()
    {
        foreach ($this->product['attachments'] as &$attachment) {
            if (!isset($attachment['file_size_formatted'])) {
                $attachment['file_size_formatted'] = Tools::formatBytes($attachment['file_size'], 2);
            }
        }

        return $this->product['attachments'];
    }

    /**
     * @arrayAccess
     *
     * @return array|mixed
     */
    public function getQuantityDiscounts()
    {
        return (isset($this->product['quantity_discounts'])) ? $this->product['quantity_discounts'] : array();
    }

    /**
     * @arrayAccess
     *
     * @return mixed|null
     */
    public function getReferenceToDisplay()
    {
        if (isset($this->product['attributes'])) {
            foreach ($this->product['attributes'] as $attribute) {
                if (isset($attribute['reference']) && $attribute['reference'] != null) {
                    return $attribute['reference'];
                }
            }
        }

        if ('' !== $this->product['reference']) {
            return $this->product['reference'];
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return array|null
     */
    public function getGroupedFeatures()
    {
        if ($this->product['features']) {
            return $this->buildGroupedFeatures($this->product['features']);
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return string
     */
    public function getSeoAvailability()
    {
        $seoAvailability = 'https://schema.org/';
        if ($this->product['quantity'] > 0) {
            $seoAvailability .= 'InStock';
        } elseif ($this->product['quantity'] <= 0 && $this->product['allow_oosp']) {
            $seoAvailability .= 'PreOrder';
        } else {
            $seoAvailability .= 'OutOfStock';
        }

        return $seoAvailability;
    }

    /**
     * @arrayAccess
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getLabels()
    {
        return array(
            'tax_short' => ($this->settings->include_taxes)
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme.Global'),
            'tax_long' => ($this->settings->include_taxes)
                ? $this->translator->trans('Tax included', array(), 'Shop.Theme.Global')
                : $this->translator->trans('Tax excluded', array(), 'Shop.Theme.Global'),
        );
    }

    /**
     * @arrayAccess
     *
     * @return array|null
     */
    public function getEcotax()
    {
        if (isset($this->product['ecotax'])) {
            return array(
                'value' => $this->priceFormatter->format($this->product['ecotax']),
                'amount' => $this->product['ecotax'],
                'rate' => $this->product['ecotax_rate'],
            );
        }

        return null;
    }

    /**
     * @arrayAccess
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getFlags()
    {
        $flags = array();

        $show_price = $this->shouldShowPrice($this->settings, $this->product);

        if ($show_price && $this->product['online_only']) {
            $flags['online-only'] = array(
                'type' => 'online-only',
                'label' => $this->translator->trans('Online only', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($show_price && $this->product['on_sale'] && !$this->settings->catalog_mode) {
            $flags['on-sale'] = array(
                'type' => 'on-sale',
                'label' => $this->translator->trans('On sale!', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($show_price
            && $this->product['reduction']
            && !$this->settings->catalog_mode
            && !$this->product['on_sale']) {
            $flags['discount'] = array(
                'type' => 'discount',
                'label' => $this->translator->trans('Reduced price', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($this->product['new']) {
            $flags['new'] = array(
                'type' => 'new',
                'label' => $this->translator->trans('New', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($this->product['pack']) {
            $flags['pack'] = array(
                'type' => 'pack',
                'label' => $this->translator->trans('Pack', array(), 'Shop.Theme.Catalog'),
            );
        }

        Hook::exec('actionProductFlagsModifier', array(
            'flags' => &$flags,
            'product' => $this->product,
        ));

        return $flags;
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getMainVariants()
    {
        $colors = $this->productColorsRetriever->getColoredVariants($this->product['id_product']);

        if (!is_array($colors)) {
            return array();
        }

        return array_map(function (array $color) {
            $color['add_to_cart_url'] = $this->link->getAddToCartURL(
                $color['id_product'],
                $color['id_product_attribute']
            );
            $color['url'] = $this->getProductURL($color, $this->language);
            $color['type'] = 'color';
            $color['html_color_code'] = $color['color'];
            unset($color['color']);

            return $color;
        }, $colors);
    }

    /**
     * @arrayAccess
     *
     * @return 0|null
     */
    public function getSpecificReferences()
    {
        if (isset($this->product['attributes']) && !isset($this->product['cart_quantity'])) {
            $specificReferences = array_slice($this->product['attributes'], 0)[0];
            //this attributes should not be displayed in FO
            unset(
                $specificReferences['id_attribute'],
                $specificReferences['id_attribute_group'],
                $specificReferences['name'],
                $specificReferences['group'],
                $specificReferences['reference']
            );

            //if the attribute's references doesn't exist then get the product's references or unset it
            foreach ($specificReferences as $key => $value) {
                if (empty($value)) {
                    $translatedKey = $this->getTranslatedKey($key);
                    unset($specificReferences[$key]);
                    if (!empty($this->product[$key])) {
                        $specificReferences[$translatedKey] = $this->product[$key];
                    }
                }
            }

            if (empty($specificReferences)) {
                $specificReferences = null;
            }

            return $specificReferences;
        }

        return null;
    }

    /**
     * Prices should be shown for products with active "Show price" option
     * and customer groups with active "Show price" option.
     *
     * @param ProductPresentationSettings $settings
     * @param array $product
     *
     * @return bool
     */
    private function shouldShowPrice(
        ProductPresentationSettings $settings,
        array $product
    ) {
        return $settings->showPrices && (bool) $product['show_price'];
    }

    /**
     * The "Add to cart" button should be shown for products available for order.
     *
     * @param $product
     *
     * @return mixed
     */
    private function shouldShowAddToCartButton($product)
    {
        return (bool) $product['available_for_order'];
    }

    private function fillImages(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $this->product['images'] = $this->imageRetriever->getProductImages(
            $product,
            $language
        );

        if (isset($product['id_product_attribute'])) {
            foreach ($this->product['images'] as $image) {
                if (isset($image['cover']) && null !== $image['cover']) {
                    $this->product['cover'] = $image;

                    break;
                }
            }
        }

        if (!isset($this->product['cover'])) {
            if (count($this->product['images']) > 0) {
                $this->product['cover'] = array_values($this->product['images'])[0];
            } else {
                $this->product['cover'] = null;
            }
        }
    }

    private function addPriceInformation(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $this->product['has_discount'] = false;
        $this->product['discount_type'] = null;
        $this->product['discount_percentage'] = null;
        $this->product['discount_percentage_absolute'] = null;
        $this->product['discount_amount'] = null;
        $this->product['discount_amount_to_display'] = null;

        if ($settings->include_taxes) {
            $price = $regular_price = $product['price'];
        } else {
            $price = $regular_price = $product['price_tax_exc'];
        }

        if ($product['specific_prices']) {
            $this->product['has_discount'] = (0 != $product['reduction']);
            $this->product['discount_type'] = $product['specific_prices']['reduction_type'];

            $absoluteReduction = new Number($product['specific_prices']['reduction']);
            $absoluteReduction = $absoluteReduction->times(new Number('100'));
            $negativeReduction = $absoluteReduction->toNegative();
            $presAbsoluteReduction = $absoluteReduction->round(2, Rounding::ROUND_HALF_UP);
            $presNegativeReduction = $negativeReduction->round(2, Rounding::ROUND_HALF_UP);

            // TODO: add percent sign according to locale preferences
            $this->product['discount_percentage'] = Tools::displayNumber($presNegativeReduction) . '%';
            $this->product['discount_percentage_absolute'] = Tools::displayNumber($presAbsoluteReduction) . '%';
            if ($settings->include_taxes) {
                $regular_price = $product['price_without_reduction'];
                $this->product['discount_amount'] = $this->priceFormatter->format(
                    $product['reduction']
                );
            } else {
                $regular_price = $product['price_without_reduction_without_tax'];
                $this->product['discount_amount'] = $this->priceFormatter->format(
                    $product['reduction_without_tax']
                );
            }
            $this->product['discount_amount_to_display'] = '-' . $this->product['discount_amount'];
        }

        $this->product['price_amount'] = $price;
        $this->product['price'] = $this->priceFormatter->format($price);
        $this->product['regular_price_amount'] = $regular_price;
        $this->product['regular_price'] = $this->priceFormatter->format($regular_price);

        if ($product['reduction'] < $product['price_without_reduction']) {
            $this->product['discount_to_display'] = $this->product['discount_amount'];
        } else {
            $this->product['discount_to_display'] = $this->product['regular_price'];
        }

        if (isset($product['unit_price']) && $product['unit_price']) {
            $this->product['unit_price'] = $this->priceFormatter->format($product['unit_price']);
            $this->product['unit_price_full'] = $this->priceFormatter->format($product['unit_price'])
                . ' ' . $product['unity'];
        } else {
            $this->product['unit_price'] = $this->product['unit_price_full'] = '';
        }
    }

    /**
     * @param array $product
     * @param ProductPresentationSettings $settings
     *
     * @return bool
     */
    protected function shouldEnableAddToCartButton(array $product, ProductPresentationSettings $settings)
    {
        if (($product['customizable'] == 2 || !empty($product['customization_required']))) {
            $shouldEnable = false;

            if (isset($product['customizations'])) {
                $shouldEnable = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && !$field['is_customized']) {
                        $shouldEnable = false;
                    }
                }
            }
        } else {
            $shouldEnable = true;
        }

        $shouldEnable = $shouldEnable && $this->shouldShowAddToCartButton($product);

        if ($settings->stock_management_enabled
            && !$product['allow_oosp']
            && ($product['quantity'] <= 0
            || $product['quantity'] - $this->getQuantityWanted() < 0)
        ) {
            $shouldEnable = false;
        }

        return $shouldEnable;
    }

    /**
     * @return int Quantity of product requested by the customer
     */
    private function getQuantityWanted()
    {
        return (int) Tools::getValue('quantity_wanted', 1);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $product
     * @param Language $language
     * @param bool $canonical
     *
     * @return string
     */
    private function getProductURL(
        array $product,
        Language $language,
        $canonical = false
    ) {
        $linkRewrite = isset($product['link_rewrite']) ? $product['link_rewrite'] : null;
        $category = isset($product['category']) ? $product['category'] : null;
        $ean13 = isset($product['ean13']) ? $product['ean13'] : null;

        return $this->link->getProductLink(
            $product['id_product'],
            $linkRewrite,
            $category,
            $ean13,
            $language->id,
            null,
            $canonical ? null : $product['id_product_attribute'],
            false,
            false,
            true
        );
    }

    /**
     * @param ProductPresentationSettings $settings
     * @param array $product
     * @param Language $language
     */
    public function addQuantityInformation(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $show_price = $this->shouldShowPrice($settings, $product);
        $show_availability = $show_price && $settings->stock_management_enabled;
        $this->product['show_availability'] = $show_availability;
        $product['quantity_wanted'] = $this->getQuantityWanted();

        if (isset($product['available_date']) && '0000-00-00' == $product['available_date']) {
            $product['available_date'] = null;
        }

        if ($show_availability) {
            if ($product['quantity'] - $product['quantity_wanted'] >= 0) {
                $this->product['availability_date'] = $product['available_date'];

                if ($product['quantity'] < $settings->lastRemainingItems) {
                    $this->applyLastItemsInStockDisplayRule();
                } else {
                    $this->product['availability_message'] = $product['available_now'] ? $product['available_now']
                        : Configuration::get('PS_LABEL_IN_STOCK_PRODUCTS', $language->id);
                    $this->product['availability'] = 'available';
                }
            } elseif ($product['allow_oosp']) {
                $this->product['availability_message'] = $product['available_later'] ? $product['available_later']
                    : Configuration::get('PS_LABEL_OOS_PRODUCTS_BOA', $language->id);
                $this->product['availability_date'] = $product['available_date'];
                $this->product['availability'] = 'available';
            } elseif ($product['quantity_wanted'] > 0 && $product['quantity'] > 0) {
                $this->product['availability_message'] = $this->translator->trans(
                    'There are not enough products in stock',
                    array(),
                    'Shop.Notifications.Error'
                );
                $this->product['availability'] = 'unavailable';
                $this->product['availability_date'] = null;
            } elseif (!empty($product['quantity_all_versions']) && $product['quantity_all_versions'] > 0) {
                $this->product['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    array(),
                    'Shop.Theme.Catalog'
                );
                $this->product['availability_date'] = $product['available_date'];
                $this->product['availability'] = 'unavailable';
            } else {
                $this->product['availability_message'] =
                    Configuration::get('PS_LABEL_OOS_PRODUCTS_BOD', $language->id);
                $this->product['availability_date'] = $product['available_date'];
                $this->product['availability'] = 'unavailable';
            }
        } else {
            $this->product['availability_message'] = null;
            $this->product['availability_date'] = null;
            $this->product['availability'] = null;
        }
    }

    /**
     * Override availability message.
     */
    protected function applyLastItemsInStockDisplayRule()
    {
        $this->product['availability_message'] = $this->translator->trans(
            'Last items in stock',
            array(),
            'Shop.Theme.Catalog'
        );
        $this->product['availability'] = 'last_remaining_items';
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getTranslatedKey($key)
    {
        switch ($key) {
            case 'ean13':
                return $this->translator->trans('ean13', array(), 'Shop.Theme.Catalog');
            case 'isbn':
                return $this->translator->trans('isbn', array(), 'Shop.Theme.Catalog');
            case 'upc':
                return $this->translator->trans('upc', array(), 'Shop.Theme.Catalog');
        }

        return $key;
    }

    /**
     * @return array
     */
    protected function getProductAttributeWhitelist()
    {
        return array(
            'add_to_cart_url',
            'additional_shipping_cost',
            'advanced_stock_management',
            'allow_oosp',
            'attachments',
            'attribute_price',
            'attributes',
            'availability',
            'availability_date',
            'availability_message',
            'available_date',
            'available_for_order',
            'available_later',
            'available_now',
            'cache_default_attribute',
            'canonical_url',
            'category',
            'category_name',
            'condition',
            'cover',
            'customer_group_discount',
            'customizable',
            'customization_required',
            'customizations',
            'date_add',
            'date_upd',
            'delivery_in_stock',
            'delivery_out_stock',
            'description',
            'description_short',
            'discount_amount',
            'discount_amount_to_display',
            'discount_percentage',
            'discount_percentage_absolute',
            'discount_type',
            'ecotax',
            'ecotax_rate',
            'extraContent',
            'features',
            'flags',
            'has_discount',
            'id',
            'id_category_default',
            'id_customization',
            'id_image',
            'id_manufacturer',
            'id_product',
            'id_product_attribute',
            'id_shop_default',
            'id_supplier',
            'id_type_redirected',
            'images',
            'indexed',
            'is_customizable',
            'is_virtual',
            'labels',
            'link',
            'link_rewrite',
            'low_stock_alert',
            'low_stock_threshold',
            'main_variants',
            'meta_description',
            'meta_keywords',
            'meta_title',
            'minimal_quantity',
            'name',
            'new',
            'nopackprice',
            'on_sale',
            'online_only',
            'out_of_stock',
            'pack',
            'pack_stock_type',
            'packItems',
            'price',
            'price_amount',
            'price_tax_exc',
            'price_without_reduction',
            'quantity',
            'quantity_all_versions',
            'quantity_discounts',
            'quantity_label',
            'quantity_wanted',
            'rate',
            'redirect_type',
            'reduction',
            'reference',
            'reference_to_display',
            'show_availability',
            'show_condition',
            'show_price',
            'show_quantities',
            'specific_prices',
            'tax_name',
            'text_fields',
            'unit_price',
            'unit_price_full',
            'unit_price_ratio',
            'unity',
            'uploadable_files',
            'url',
            'virtual',
            'visibility',
            'weight_unit',
        );
    }

    /**
     * Assemble the same features in one array.
     *
     * @param array $productFeatures
     *
     * @return array
     */
    protected function buildGroupedFeatures(array $productFeatures)
    {
        $valuesByFeatureName = array();
        $groupedFeatures = array();

        // features can either be "raw" (id_feature, id_product_id_feature_value)
        // or "full" (id_feature, name, value)
        // grouping can only be performed if they are "full"
        if (empty($productFeatures) || !array_key_exists('name', reset($productFeatures))) {
            return array();
        }

        foreach ($productFeatures as $feature) {
            $featureName = $feature['name'];
            // build an array of unique features
            $groupedFeatures[$featureName] = $feature;
            // aggregate feature values separately
            $valuesByFeatureName[$featureName][] = $feature['value'];
        }

        // replace value from features that have multiple values with the ones we aggregated earlier
        foreach ($valuesByFeatureName as $featureName => $values) {
            if (count($values) > 1) {
                sort($values, SORT_NATURAL);
                $groupedFeatures[$featureName]['value'] = implode("\n", $values);
            }
        }

        return $groupedFeatures;
    }
}
