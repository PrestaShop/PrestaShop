<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShop\Decimal\Number;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Symfony\Component\Translation\TranslatorInterface;
use Configuration;
use Language;
use Link;
use Tools;

class ProductPresenter
{
    private $imageRetriever;
    private $link;
    private $priceFormatter;
    private $productColorsRetriever;
    private $translator;

    public function __construct(
        ImageRetriever $imageRetriever,
        Link $link,
        PriceFormatter $priceFormatter,
        ProductColorsRetriever $productColorsRetriever,
        TranslatorInterface $translator
    ) {
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->priceFormatter = $priceFormatter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator = $translator;
    }

    /**
     * Prices should be shown for products with active "Show price" option
     * and customer groups with active "Show price" option.
     *
     * @param ProductPresentationSettings $settings
     * @param array $product
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
     * @return mixed
     */
    private function shouldShowAddToCartButton($product)
    {
        return (bool) $product['available_for_order'];
    }

    private function fillImages(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $presentedProduct['images'] = $this->imageRetriever->getProductImages(
            $product,
            $language
        );

        if (isset($product['id_product_attribute'])) {
            foreach ($presentedProduct['images'] as $image) {
                if (isset($image['cover']) && null !== $image['cover']) {
                    $presentedProduct['cover'] = $image;

                    break;
                }
            }
        }

        if (!isset($presentedProduct['cover'])) {
            if (count($presentedProduct['images']) > 0) {
                $presentedProduct['cover'] = array_values($presentedProduct['images'])[0];
            } else {
                $presentedProduct['cover'] = null;
            }
        }

        return $presentedProduct;
    }

    private function addLabels(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        // TODO: move it to a common parent, since it's copied in OrderPresenter and CartPresenter
        $presentedProduct['labels'] = array(
            'tax_short' => ($settings->include_taxes)
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme.Global'),
            'tax_long' => ($settings->include_taxes)
                ? $this->translator->trans('Tax included', array(), 'Shop.Theme.Global')
                : $this->translator->trans('Tax excluded', array(), 'Shop.Theme.Global'),
        );

        return $presentedProduct;
    }

    private function addPriceInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $presentedProduct['has_discount'] = false;
        $presentedProduct['discount_type'] = null;
        $presentedProduct['discount_percentage'] = null;
        $presentedProduct['discount_percentage_absolute'] = null;
        $presentedProduct['discount_amount'] = null;
        $presentedProduct['discount_amount_to_display'] = null;

        if ($settings->include_taxes) {
            $price = $regular_price = $product['price'];
        } else {
            $price = $regular_price = $product['price_tax_exc'];
        }

        if ($product['specific_prices']) {
            $presentedProduct['has_discount']  = (0 != $product['reduction']);
            $presentedProduct['discount_type'] = $product['specific_prices']['reduction_type'];

            $absoluteReduction     = new Number($product['specific_prices']['reduction']);
            $absoluteReduction     = $absoluteReduction->times(new Number('100'));
            $negativeReduction     = $absoluteReduction->toNegative();
            $presAbsoluteReduction = $absoluteReduction->round(2, Rounding::ROUND_HALF_UP);
            $presNegativeReduction = $negativeReduction->round(2, Rounding::ROUND_HALF_UP);

            // TODO: add percent sign according to locale preferences
            $presentedProduct['discount_percentage'] = Tools::displayNumber($presNegativeReduction) . '%';
            $presentedProduct['discount_percentage_absolute'] = Tools::displayNumber($presAbsoluteReduction) . '%';
            // TODO: Fix issue with tax calculation
            $presentedProduct['discount_amount'] = $this->priceFormatter->format(
                $product['reduction']
            );
            $presentedProduct['discount_amount_to_display'] = '-' . $presentedProduct['discount_amount'];
            $regular_price = $product['price_without_reduction'];
        }

        $presentedProduct['price_amount'] = $price;
        $presentedProduct['price'] = $this->priceFormatter->format($price);
        $presentedProduct['regular_price_amount'] = $regular_price;
        $presentedProduct['regular_price'] = $this->priceFormatter->format($regular_price);

        if ($product['reduction'] < $product['price_without_reduction']) {
            $presentedProduct['discount_to_display'] = $presentedProduct['discount_amount'];
        } else {
            $presentedProduct['discount_to_display'] = $presentedProduct['regular_price'];
        }

        if (isset($product['unit_price']) && $product['unit_price']) {
            $presentedProduct['unit_price'] = $this->priceFormatter->format($product['unit_price']);
            $presentedProduct['unit_price_full'] = $this->priceFormatter->format($product['unit_price'])
                .' '.$product['unity'];
        } else {
            $presentedProduct['unit_price'] = $presentedProduct['unit_price_full'] = '';
        }

        return $presentedProduct;
    }

    private function addEcotaxInformation(
        array $presentedProduct,
        array $product
    ) {
        $presentedProduct['ecotax'] = array(
            'value' => $this->priceFormatter->format($product['ecotax']),
            'amount' => $product['ecotax'],
            'rate' => $product['ecotax_rate'],
        );

        return $presentedProduct;
    }

    private function addQuantityDiscountInformation(
        array $presentedProduct,
        array $product
    ) {
        $presentedProduct['quantity_discounts'] =
            (isset($product['quantity_discounts'])) ? $product['quantity_discounts'] : array();

        return $presentedProduct;
    }

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
            && $product['quantity'] <= 0
        ) {
            $shouldEnable = false;
        }

        return $shouldEnable;
    }

    private function getAddToCartURL(array $product)
    {
        return $this->link->getAddToCartURL(
            $product['id_product'],
            $product['id_product_attribute']
        );
    }

    private function getProductURL(
        array $product,
        Language $language,
        $canonical = false
    ) {
        $linkRewrite = isset($product['link_rewrite'])?$product['link_rewrite']:null;
        $category = isset($product['category'])?$product['category']:null;
        $ean13 = isset($product['ean13'])?$product['ean13']:null;

        return $this->link->getProductLink(
            $product['id_product'],
            $linkRewrite,
            $category,
            $ean13,
            $language->id,
            null,
            (!$canonical) ? $product['id_product_attribute'] : null,
            false,
            false,
            true
        );
    }

    private function addMainVariantsInformation(
        array $presentedProduct,
        array $product,
        Language $language
    ) {
        $colors = $this->productColorsRetriever->getColoredVariants($product['id_product']);

        if (!is_array($colors)) {
            $presentedProduct['main_variants'] = array();

            return $presentedProduct;
        }

        $presentedProduct['main_variants'] = array_map(function (array $color) use ($language) {
            $color['add_to_cart_url'] = $this->getAddToCartURL($color);
            $color['url'] = $this->getProductURL($color, $language);
            $color['type'] = 'color';
            $color['html_color_code'] = $color['color'];
            unset($color['color']);
            unset($color['id_attribute']); // because what is a template supposed to do with it?

            return $color;
        }, $colors);

        return $presentedProduct;
    }

    private function addFlags(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        $flags = array();

        $show_price = $this->shouldShowPrice($settings, $product);

        if ($show_price && $product['online_only']) {
            $flags['online-only'] = array(
                'type' => 'online-only',
                'label' => $this->translator->trans('Online only', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($show_price && $product['on_sale'] && !$settings->catalog_mode) {
            $flags['on-sale'] = array(
                'type' => 'on-sale',
                'label' => $this->translator->trans('On sale!', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($show_price && $product['reduction'] && !$settings->catalog_mode && !$product['on_sale']) {
            $flags['discount'] = array(
                'type' => 'discount',
                'label' => $this->translator->trans('Reduced price', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($product['new']) {
            $flags['new'] = array(
                'type' => 'new',
                'label' => $this->translator->trans('New', array(), 'Shop.Theme.Catalog'),
            );
        }

        if ($product['pack']) {
            $flags['pack'] = array(
                'type' => 'pack',
                'label' => $this->translator->trans('Pack', array(), 'Shop.Theme.Catalog'),
            );
        }

        $presentedProduct['flags'] = $flags;

        return $presentedProduct;
    }

    private function addConditionInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        switch ($product['condition']) {
            case 'new':
                $presentedProduct['condition'] = array(
                    'type' => 'new',
                    'label' => $this->translator->trans('New product', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/NewCondition',
                );
                break;
            case 'used':
                $presentedProduct['condition'] = array(
                    'type' => 'used',
                    'label' => $this->translator->trans('Used', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/UsedCondition',
                );
                break;
            case 'refurbished':
                $presentedProduct['condition'] = array(
                    'type' => 'refurbished',
                    'label' => $this->translator->trans('Refurbished', array(), 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/RefurbishedCondition',
                );
                break;
            default:
                $presentedProduct['condition'] = false;
        }

        return $presentedProduct;
    }

    private function addAttachmentsInformation($presentedProduct)
    {
        if (!isset($presentedProduct['attachments'])) {
            return $presentedProduct;
        }
        foreach ($presentedProduct['attachments'] as &$attachment) {
            $attachment['file_size_formatted'] = Tools::formatBytes($attachment['file_size'], 2);
        }

        return $presentedProduct;
    }

    /**
     * @param array $presentedProduct
     * @param array $product
     * @param Language $language
     * @return array
     */
    private function addDeliveryInformation(
        array $presentedProduct,
        array $product,
        Language $language
    ) {
        $presentedProduct['delivery_information'] = null;

        if ($product['quantity'] > 0) {
            $presentedProduct['delivery_information'] = Configuration::get('PS_LABEL_DELIVERY_TIME_AVAILABLE', $language->id);
        } elseif ($product['allow_oosp']) {
            $presentedProduct['delivery_information'] = Configuration::get('PS_LABEL_DELIVERY_TIME_OOSBOA', $language->id);
        }

        return $presentedProduct;
    }

    /**
     * @param array $presentedProduct
     * @param ProductPresentationSettings $settings
     * @param array $product
     * @param Language $language
     * @return array
     */
    public function addQuantityInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $show_price = $this->shouldShowPrice($settings, $product);
        $show_availability = $show_price && $settings->stock_management_enabled;
        $presentedProduct['show_availability'] = $show_availability;
        $product['quantity_wanted'] = (int) Tools::getValue('quantity_wanted', 1);

        if (isset($product['available_date']) && '0000-00-00' == $product['available_date']) {
            $product['available_date'] = null;
        }

        if ($show_availability) {
            if ($product['quantity'] - $product['quantity_wanted'] >= 0) {
                $presentedProduct['availability_date'] = $product['available_date'];

                if ($product['quantity'] < $settings->lastRemainingItems) {
                    $presentedProduct = $this->applyLastItemsInStockDisplayRule($product, $settings, $presentedProduct);
                } else {
                    $presentedProduct['availability_message'] = $product['available_now'] ? $product['available_now'] : Configuration::get('PS_LABEL_IN_STOCK_PRODUCTS', $language->id);
                    $presentedProduct['availability'] = 'available';
                }
            } elseif ($product['allow_oosp']) {
                $presentedProduct['availability_message'] = $product['available_later'] ? $product['available_later'] : Configuration::get('PS_LABEL_OOS_PRODUCTS_BOA', $language->id);
                $presentedProduct['availability_date'] = $product['available_date'];
                $presentedProduct['availability'] = 'available';
            } elseif ($product['quantity_wanted'] > 0 && $product['quantity'] >= 0) {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'There are not enough products in stock',
                    array(),
                    'Shop.Notifications.Error'
                );
                $presentedProduct['availability'] = 'unavailable';
                $presentedProduct['availability_date'] = null;
            } elseif (!empty($product['quantity_all_versions']) && $product['quantity_all_versions'] > 0) {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    array(),
                    'Shop.Theme.Catalog'
                );
                $presentedProduct['availability_date'] = $product['available_date'];
                $presentedProduct['availability'] = 'unavailable';
            } else {
                $presentedProduct['availability_message'] = Configuration::get('PS_LABEL_OOS_PRODUCTS_BOD', $language->id);
                $presentedProduct['availability_date'] = $product['available_date'];
                $presentedProduct['availability'] = 'unavailable';
            }
        } else {
            $presentedProduct['availability_message'] = null;
            $presentedProduct['availability_date'] = null;
            $presentedProduct['availability'] = null;
        }

        return $presentedProduct;
    }

    /**
     * Override availability message when quantity of products in stock is less than what has been defined
     * in Shop Parameters > Product Settings
     *
     * @param array $product
     * @param ProductPresentationSettings $settings
     * @param array $presentedProduct
     * @return array
     */
    protected function applyLastItemsInStockDisplayRule(
        array $product,
        ProductPresentationSettings $settings,
        array $presentedProduct
    ) {
        $presentedProduct['availability_message'] = $this->translator->trans(
            'Last items in stock',
            array(),
            'Shop.Theme.Catalog'
        );
        $presentedProduct['availability'] = 'last_remaining_items';

        return $presentedProduct;
    }

    /**
     * Add new attribute reference_to_display if the product reference or the selected combinations reference is set
     * @param array $product
     * @param array $presentedProduct
     * @return array
     */
    public function addReferenceToDisplay(array $product, array $presentedProduct)
    {
        if ('' !== $product['reference']) {
            $presentedProduct['reference_to_display'] = $product['reference'];
        }

        if (isset($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                if (isset($attribute['reference']) && $attribute['reference'] != null) {
                    $presentedProduct['reference_to_display'] = $attribute['reference'];
                }
            }
        }

        return $presentedProduct;
    }

    /**
     * Add all specific references to product
     * @param array $product
     * @param array $presentedProduct
     * @return array
     */
    public function addAttributesSpecificReferences(array $product, array $presentedProduct)
    {
        $presentedProduct['specific_references'] = array_slice($product['attributes'], 0)[0];
        //this attributes should not be displayed in FO
        unset(
            $presentedProduct['specific_references']['id_attribute'],
            $presentedProduct['specific_references']['id_attribute_group'],
            $presentedProduct['specific_references']['name'],
            $presentedProduct['specific_references']['group'],
            $presentedProduct['specific_references']['reference']
        );

        //if the attribute's references doesn't exist then get the product's references or unset it
        foreach ($presentedProduct['specific_references'] as $key => $value) {
            if (empty($value)) {
                $translatedKey = $this->getTranslatedKey($key);
                unset($presentedProduct['specific_references'][$key]);
                if (!empty($product[$key])) {
                    $presentedProduct['specific_references'][$translatedKey] = $product[$key];
                }
            }
        }
        if (empty($presentedProduct['specific_references'])) {
            unset($presentedProduct['specific_references']);
        }

        return $presentedProduct;
    }

    public function present(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $presentedProduct = $product;
        $presentedProduct['id'] = $presentedProduct['id_product'];

        if (!isset($presentedProduct['attributes'])) {
            $presentedProduct['attributes'] = array();
        }

        $show_price = $this->shouldShowPrice($settings, $product);

        $presentedProduct['show_price'] = $show_price;

        $presentedProduct['weight_unit'] = Configuration::get('PS_WEIGHT_UNIT');

        $presentedProduct = $this->fillImages(
            $presentedProduct,
            $settings,
            $product,
            $language
        );

        $presentedProduct['url'] = $this->getProductURL($product, $language);
        $presentedProduct['canonical_url'] = $this->getProductURL($product, $language, true);

        $presentedProduct = $this->addPriceInformation(
            $presentedProduct,
            $settings,
            $product,
            $language
        );

        if ($this->shouldEnableAddToCartButton($product, $settings)) {
            $presentedProduct['add_to_cart_url'] = $this->getAddToCartURL($product);
        } else {
            $presentedProduct['add_to_cart_url'] = null;
        }

        $presentedProduct = $this->addAttachmentsInformation($presentedProduct);

        $presentedProduct = $this->addMainVariantsInformation(
            $presentedProduct,
            $product,
            $language
        );

        $presentedProduct = $this->addFlags(
            $presentedProduct,
            $settings,
            $product
        );

        $presentedProduct = $this->addLabels(
            $presentedProduct,
            $settings,
            $product
        );

        if (isset($product['show_condition']) && $product['show_condition']) {
            $presentedProduct = $this->addConditionInformation(
                $presentedProduct,
                $settings,
                $product
            );
        } else {
            $presentedProduct['condition'] = false;
        }

        $presentedProduct = $this->addQuantityInformation(
            $presentedProduct,
            $settings,
            $product,
            $language
        );

        $presentedProduct = $this->addDeliveryInformation(
            $presentedProduct,
            $product,
            $language
        );

        if (isset($product['ecotax'])) {
            $presentedProduct = $this->addEcotaxInformation(
                $presentedProduct,
                $product
            );
        }

        $presentedProduct = $this->addQuantityDiscountInformation(
            $presentedProduct,
            $product
        );

        $presentedProduct = $this->addReferenceToDisplay(
            $product,
            $presentedProduct
        );

        // If product has attributes and it's no added to card
        if (isset($product['attributes']) && !isset($product['cart_quantity'])) {
            $presentedProduct = $this->addAttributesSpecificReferences(
                $product,
                $presentedProduct
            );
        }

        $presentedProduct['embedded_attributes'] = $this->getProductEmbeddedAttributes($product);

        // if product has features
        if (isset($presentedProduct['features'])) {
            $presentedProduct['grouped_features'] = $this->buildGroupedFeatures($presentedProduct['features']);
        }

        //microdata availability
        $presentedProduct['seo_availability'] = 'https://schema.org/';
        if ($product['quantity'] > 0) {
            $presentedProduct['seo_availability'] .= 'InStock';
        } elseif ($product['quantity'] <= 0 && $product['allow_oosp']) {
            $presentedProduct['seo_availability'] .= 'PreOrder';
        } else {
            $presentedProduct['seo_availability'] .= 'OutOfStock';
        }

        return $presentedProduct;
    }

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
            'id_shop_default',
            'id_manufacturer',
            'id_supplier',
            'reference',
            'is_virtual',
            'id_category_default',
            'id_product_attribute',
            'id_product',
            'id_customization',
            'price',
            'pack_stock_type',
            'meta_description',
            'meta_keywords',
            'meta_title',
            'link_rewrite',
            'name',
            'description',
            'description_short',
            "on_sale",
            "online_only",
            "ecotax",
            "minimal_quantity",
            "low_stock_threshold",
            "low_stock_alert",
            "price",
            "unity",
            "unit_price_ratio",
            "additional_shipping_cost",
            "customizable",
            "text_fields",
            "uploadable_files",
            "redirect_type",
            "id_type_redirected",
            "available_for_order",
            "available_date",
            "show_condition",
            "condition",
            "show_price",
            "indexed",
            "visibility",
            "cache_default_attribute",
            "advanced_stock_management",
            "date_add",
            "date_upd",
            "pack_stock_type",
            "meta_description",
            "meta_keywords",
            "meta_title",
            "link_rewrite",
            "name",
            "description",
            "description_short",
            "available_now",
            "available_later",
            "id",
            "out_of_stock",
            "new",
            "quantity_wanted",
            "extraContent",
            "allow_oosp",
            "category",
            "category_name",
            "link",
            "attribute_price",
            "price_tax_exc",
            "price_without_reduction",
            "reduction",
            "specific_prices",
            "quantity",
            "quantity_all_versions",
            "id_image",
            "features",
            "attachments",
            "virtual",
            "pack",
            "packItems",
            "nopackprice",
            "customization_required",
            "attributes",
            "rate",
            "tax_name",
            "ecotax_rate",
            "unit_price",
            "customizations",
            "is_customizable",
            "show_quantities",
            "quantity_label",
            "quantity_discounts",
            "customer_group_discount",
            "weight_unit",
            "images",
            "cover",
            "url",
            "canonical_url",
            "has_discount",
            "discount_type",
            "discount_percentage",
            "discount_percentage_absolute",
            "discount_amount",
            "discount_amount_to_display",
            "price_amount",
            "unit_price_full",
            "add_to_cart_url",
            "main_variants",
            "flags",
            "labels",
            "show_availability",
            "availability_date",
            "availability_message",
            "availability",
            "reference_to_display",
            "delivery_in_stock",
            "delivery_out_stock",
        );
    }

    /**
     * @param array $product
     * @return string
     */
    protected function getProductEmbeddedAttributes(array $product)
    {
        $whitelist = $this->getProductAttributeWhitelist();
        $embeddedProductAttributes = array();
        foreach ($product as $attribute => $value) {
            if (in_array($attribute, $whitelist)) {
                $embeddedProductAttributes[$attribute] = $value;
            }
        }

        return $embeddedProductAttributes;
    }

    /**
     * Assemble the same features in one array
     *
     * @param  array $productFeatures
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
