<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use DateTime;
use Language;
use Link;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
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

    /**
     * @var HookManager
     */
    private $hookManager;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        ProductPresentationSettings $settings,
        array $product,
        Language $language,
        ImageRetriever $imageRetriever,
        Link $link,
        PriceFormatter $priceFormatter,
        ProductColorsRetriever $productColorsRetriever,
        TranslatorInterface $translator,
        HookManager $hookManager = null,
        Configuration $configuration = null
    ) {
        $this->settings = $settings;
        $this->product = $product;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->priceFormatter = $priceFormatter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator = $translator;
        $this->hookManager = $hookManager ?? new HookManager();
        $this->configuration = $configuration ?? new Configuration();

        $this->fillImages(
            $product,
            $language
        );

        $this->addPriceInformation(
            $settings,
            $product
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

        return [];
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
        return $this->configuration->get('PS_WEIGHT_UNIT');
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
                return [
                    'type' => 'new',
                    'label' => $this->translator->trans('New', [], 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/NewCondition',
                ];
            case 'used':
                return [
                    'type' => 'used',
                    'label' => $this->translator->trans('Used', [], 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/UsedCondition',
                ];
            case 'refurbished':
                return [
                    'type' => 'refurbished',
                    'label' => $this->translator->trans('Refurbished', [], 'Shop.Theme.Catalog'),
                    'schema_url' => 'https://schema.org/RefurbishedCondition',
                ];
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
            $config = $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE');

            return $config[$this->language->id] ?? null;
        } elseif ($this->product['allow_oosp']) {
            $config = $this->configuration->get('PS_LABEL_DELIVERY_TIME_OOSBOA', []);

            return $config[$this->language->id] ?? null;
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
        $embeddedProductAttributes = [];
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
        foreach ($this->product['attachments'] as $attachment) {
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
        return (isset($this->product['quantity_discounts'])) ? $this->product['quantity_discounts'] : [];
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
        return [
            'tax_short' => ($this->settings->include_taxes)
                ? $this->translator->trans('(tax incl.)', [], 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', [], 'Shop.Theme.Global'),
            'tax_long' => ($this->settings->include_taxes)
                ? $this->translator->trans('Tax included', [], 'Shop.Theme.Global')
                : $this->translator->trans('Tax excluded', [], 'Shop.Theme.Global'),
        ];
    }

    /**
     * @arrayAccess
     *
     * @return array|null
     */
    public function getEcotax()
    {
        if (isset($this->product['ecotax'])) {
            return [
                'value' => $this->priceFormatter->format($this->product['ecotax']),
                'amount' => $this->product['ecotax'],
                'rate' => $this->product['ecotax_rate'],
            ];
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
        $flags = [];

        $show_price = $this->shouldShowPrice($this->settings, $this->product);

        if ($show_price && $this->product['online_only']) {
            $flags['online-only'] = [
                'type' => 'online-only',
                'label' => $this->translator->trans('Online only', [], 'Shop.Theme.Catalog'),
            ];
        }

        if ($show_price && $this->product['on_sale'] && !$this->settings->catalog_mode) {
            $flags['on-sale'] = [
                'type' => 'on-sale',
                'label' => $this->translator->trans('On sale!', [], 'Shop.Theme.Catalog'),
            ];
        }

        if ($show_price && $this->product['reduction']) {
            if ($this->product['discount_type'] === 'percentage') {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->product['discount_percentage'],
                ];
            } elseif ($this->product['discount_type'] === 'amount') {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->product['discount_amount_to_display'],
                ];
            } else {
                $flags['discount'] = [
                    'type' => 'discount',
                    'label' => $this->translator->trans('Reduced price', [], 'Shop.Theme.Catalog'),
                ];
            }
        }

        if ($this->product['new']) {
            $flags['new'] = [
                'type' => 'new',
                'label' => $this->translator->trans('New product', [], 'Shop.Theme.Catalog'),
            ];
        }

        if ($this->product['pack']) {
            $flags['pack'] = [
                'type' => 'pack',
                'label' => $this->translator->trans('Pack', [], 'Shop.Theme.Catalog'),
            ];
        }

        if ($this->shouldShowOutOfStockLabel($this->settings, $this->product)) {
            $config = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD');
            $flags['out_of_stock'] = [
                'type' => 'out_of_stock',
                'label' => $config[$this->language->getId()] ?? null,
            ];
        }

        $this->hookManager->exec('actionProductFlagsModifier', [
            'flags' => &$flags,
            'product' => $this->product,
        ]);

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
            return [];
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
     * @return array|null
     */
    public function getSpecificReferences()
    {
        if (isset($this->product['cart_quantity'])) {
            return null;
        }

        // If the product has no combinations then the `specific_references` must be filled in
        if (isset($this->product['attributes'])) {
            $specificReferences = array_slice($this->product['attributes'], 0)[0];
        } else {
            $specificReferences = [
                'isbn' => $this->product['isbn'] ?? false,
                'upc' => $this->product['upc'] ?? false,
                'ean13' => $this->product['ean13'] ?? false,
                'mpn' => $this->product['mpn'] ?? false,
            ];
        }
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
    ): bool {
        return $settings->shouldShowPrice() && (bool) $product['show_price'];
    }

    /**
     * The "Add to cart" button should be shown for products available for order.
     *
     * @param array $product
     *
     * @return bool
     */
    private function shouldShowAddToCartButton(array $product): bool
    {
        return (bool) $product['available_for_order'];
    }

    /**
     * @param array $product
     *
     * @return bool
     */
    private function shouldShowOutOfStockLabel(ProductPresentationSettings $settings, array $product): bool
    {
        if (!$settings->showLabelOOSListingPages) {
            return false;
        }

        // Displayed only if the order of out of stock product is denied.
        if ($product['out_of_stock'] == OutOfStockType::OUT_OF_STOCK_AVAILABLE
            || (
                $product['out_of_stock'] == OutOfStockType::OUT_OF_STOCK_DEFAULT
                && $this->configuration->getBoolean('PS_ORDER_OUT_OF_STOCK')
            )) {
            return false;
        }

        if ($product['id_product_attribute']) {
            // Displayed only if all combinations are out of stock (stock is <= 0)
            $product = new Product((int) $product['id_product']);
            if (empty($product->id)) {
                return false;
            }

            foreach ($product->getAttributesResume($this->language->getId()) as $combination) {
                if ($combination['quantity'] > 0) {
                    return false;
                }
            }
        } elseif ($product['quantity'] > 0) {
            // Displayed only if the product stock is <= 0
            return false;
        }

        return true;
    }

    /**
     * @param array $product
     * @param Language $language
     */
    private function fillImages(array $product, Language $language): void
    {
        // Get all product images, including potential cover
        $productImages = $this->imageRetriever->getAllProductImages(
            $product,
            $language
        );

        // Get filtered product images matching the specified id_product_attribute
        $this->product['images'] = $this->filterImagesForCombination($productImages, $product['id_product_attribute']);

        // Get default image for selected combination (used for product page, cart details, ...)
        $this->product['default_image'] = reset($this->product['images']);
        foreach ($this->product['images'] as $image) {
            // If one of the image is a cover it is used as such
            if (isset($image['cover']) && null !== $image['cover']) {
                $this->product['default_image'] = $image;

                break;
            }
        }

        // Get generic product image, used for product listing
        if (isset($product['cover_image_id'])) {
            // First try to find cover in product images
            foreach ($productImages as $productImage) {
                if ($productImage['id_image'] == $product['cover_image_id']) {
                    $this->product['cover'] = $productImage;
                    break;
                }
            }

            // If the cover is not associated to the product images it is fetched manually
            if (!isset($this->product['cover'])) {
                $coverImage = $this->imageRetriever->getImage(new Product($product['id_product'], false, $language->getId()), $product['cover_image_id']);
                $this->product['cover'] = array_merge($coverImage, [
                    'legend' => $coverImage['legend'],
                ]);
            }
        }

        // If no cover fallback on default image
        if (!isset($this->product['cover'])) {
            $this->product['cover'] = $this->product['default_image'];
        }
    }

    /**
     * @param array $images
     * @param int $productAttributeId
     *
     * @return array
     */
    private function filterImagesForCombination(array $images, int $productAttributeId)
    {
        $filteredImages = [];

        foreach ($images as $image) {
            if (in_array($productAttributeId, $image['associatedVariants'])) {
                $filteredImages[] = $image;
            }
        }

        return (0 === count($filteredImages)) ? $images : $filteredImages;
    }

    /**
     * @param ProductPresentationSettings $settings
     * @param array $product
     */
    private function addPriceInformation(ProductPresentationSettings $settings, array $product): void
    {
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

            $absoluteReduction = new DecimalNumber($product['specific_prices']['reduction']);
            $absoluteReduction = $absoluteReduction->times(new DecimalNumber('100'));
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
            || $product['quantity'] - $this->getQuantityWanted() < 0
            || $product['quantity'] - $this->getMinimalQuantity() < 0)
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
     * @return int Minimal quantity of product requested by the customer
     */
    private function getMinimalQuantity()
    {
        return (int) $this->product['minimal_quantity'];
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
            !$canonical && $product['id_product_attribute'] > 0 ? $product['id_product_attribute'] : null,
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

        if (isset($product['available_date'])) {
            $date = new DateTime($product['available_date']);
            if ($date < new DateTime()) {
                $product['available_date'] = null;
            }
        }

        if ($show_availability) {
            $availableQuantity = $product['quantity'] - $product['quantity_wanted'];
            if (isset($product['stock_quantity'])) {
                $availableQuantity = $product['stock_quantity'] - $product['quantity_wanted'];
            }
            if ($availableQuantity >= 0) {
                $this->product['availability_date'] = $product['available_date'];

                if ($product['quantity'] < $settings->lastRemainingItems) {
                    $this->applyLastItemsInStockDisplayRule();
                } else {
                    $config = $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS');
                    $this->product['availability_message'] = $product['available_now'] ? $product['available_now']
                        : ($config[$language->id] ?? null);
                    $this->product['availability'] = 'available';
                }
            } elseif ($product['allow_oosp']) {
                $config = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA');
                $this->product['availability_message'] = $product['available_later'] ? $product['available_later']
                    : ($config[$language->id] ?? null);
                $this->product['availability_date'] = $product['available_date'];
                $this->product['availability'] = 'available';
            } elseif ($product['quantity_wanted'] > 0 && $product['quantity'] > 0) {
                $this->product['availability_message'] = $this->translator->trans(
                    'There are not enough products in stock',
                    [],
                    'Shop.Notifications.Error'
                );
                $this->product['availability'] = 'unavailable';
                $this->product['availability_date'] = null;
            } elseif (!empty($product['quantity_all_versions']) && $product['quantity_all_versions'] > 0) {
                $this->product['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    [],
                    'Shop.Theme.Catalog'
                );
                $this->product['availability_date'] = $product['available_date'];
                $this->product['availability'] = 'unavailable';
            } else {
                $config = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD');
                $this->product['availability_message'] = $config[$language->id] ?? null;
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
            [],
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
                return $this->translator->trans('ean13', [], 'Shop.Theme.Catalog');
            case 'isbn':
                return $this->translator->trans('isbn', [], 'Shop.Theme.Catalog');
            case 'upc':
                return $this->translator->trans('upc', [], 'Shop.Theme.Catalog');
            case 'mpn':
                return $this->translator->trans('MPN', [], 'Shop.Theme.Catalog');
        }

        return $key;
    }

    /**
     * @return array
     */
    protected function getProductAttributeWhitelist()
    {
        return [
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
        ];
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
        $valuesByFeatureName = [];
        $groupedFeatures = [];

        // features can either be "raw" (id_feature, id_product_id_feature_value)
        // or "full" (id_feature, name, value)
        // grouping can only be performed if they are "full"
        if (empty($productFeatures) || !array_key_exists('name', reset($productFeatures))) {
            return [];
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
