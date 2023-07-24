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

use Context;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * @property string $availability_message
 */
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
        $productQuantity = $this->product['stock_quantity'] ?? $this->product['quantity'];

        if ($productQuantity >= $this->getQuantityWanted()) {
            $config = $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE');

            return $config[$this->language->id] ?? null;
        } elseif ($this->shouldEnableAddToCartButton($this->product, $this->settings)) {
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
        $combinationData = $this->getCombinationSpecificData();
        if (isset($combinationData['reference']) && !empty($combinationData['reference'])) {
            return $combinationData['reference'];
        }

        if ('' !== $this->product['reference']) {
            return $this->product['reference'];
        }

        return null;
    }

    /**
     * Returns all product features, not grouped yet for performance reasons.
     *
     * @arrayAccess
     *
     * @return array
     */
    public function getFeatures()
    {
        /*
         * If features were not loaded yet, we will ask for them if needed - usually on product page.
         * However, if really hunting performance and you know you will need features in listing for bunch of products,
         * fetch them with one query (in more performant way) and pass them here when constructing this object.
         */
        if (!isset($this->product['features'])) {
            $this->product['features'] = Product::getFrontFeaturesStatic((int) $this->language->id, $this->product['id_product']);
        }

        return $this->product['features'];
    }

    /**
     * Returns all product feature values nicely grouped by feature name.
     *
     * @arrayAccess
     *
     * @return array
     */
    public function getGroupedFeatures()
    {
        return $this->buildGroupedFeatures($this->getFeatures());
    }

    /**
     * See following resources for up-to-date information
     * https://support.google.com/merchants/answer/6324448
     * https://schema.org/ItemAvailability
     *
     * @arrayAccess
     *
     * @return string
     */
    public function getSeoAvailability()
    {
        // Availability for displaying discontinued products, if enabled
        if ($this->product['active'] != 1) {
            return 'https://schema.org/Discontinued';
        // If product is in stock or stock management is disabled (= we have everything in stock)
        } elseif ($this->product['quantity'] > 0 || !$this->configuration->get('PS_STOCK_MANAGEMENT')) {
            return 'https://schema.org/InStock';
        // If it's not in stock, but available for order
        } elseif ($this->product['quantity'] <= 0 && $this->product['allow_oosp']) {
            return 'https://schema.org/BackOrder';
        // If it's not in stock and not available for order
        } else {
            return 'https://schema.org/OutOfStock';
        }
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
                'label' => $this->translator->trans('New', [], 'Shop.Theme.Catalog'),
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
     * Returns combination specific data, if assigned. This function should be rewritten because it
     * loads the data from the first attribute found. See ProductController for more info.
     *
     * Also, on product page, $this->product['attributes'] contains a list of combinations, while in cart
     * it contains only attribute pairs like Color-Black etc.
     *
     * @arrayAccess
     *
     * @return array|null
     */
    public function getCombinationSpecificData()
    {
        if (!isset($this->product['attributes']) || empty($this->product['attributes'])) {
            return null;
        }

        return reset($this->product['attributes']);
    }

    /**
     * This function returns current combination references, if set.
     * Otherwise, it returns the base product references.
     *
     * @arrayAccess
     *
     * @return array|null
     */
    public function getSpecificReferences()
    {
        if (isset($this->product['cart_quantity'])) {
            return null;
        }

        $specificReferences = null;

        // Get data of this combination, it contains other stuff, we will extract only what we need
        $combinationData = $this->getCombinationSpecificData();

        // Keys we want to extract from the combination data
        $referenceTypes = ['isbn', 'upc', 'ean13', 'mpn'];

        foreach ($referenceTypes as $type) {
            // First, we try to get the references of combination.
            if (!empty($combinationData[$type])) {
                $specificReference = $combinationData[$type];
            // Otherwise, we check if something is set on the product itself
            } elseif (!empty($this->product[$type])) {
                $specificReference = $this->product[$type];
            } else {
                continue;
            }

            // Get a nice readable label for this reference and save it
            $specificReferences[$this->getTranslatedKey($type)] = $specificReference;
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
     * @param array $product
     *
     * @return bool
     */
    private function shouldShowOutOfStockLabel(ProductPresentationSettings $settings, array $product): bool
    {
        if (!$settings->showLabelOOSListingPages) {
            return false;
        }

        if (!$this->configuration->getBoolean('PS_STOCK_MANAGEMENT')) {
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
            $this->product['discount_percentage'] = Context::getContext()->getCurrentLocale()->formatNumber($presNegativeReduction) . '%';
            $this->product['discount_percentage_absolute'] = Context::getContext()->getCurrentLocale()->formatNumber($presAbsoluteReduction) . '%';
            if ($settings->include_taxes) {
                $regular_price = $product['price_without_reduction'];
            } else {
                $regular_price = $product['price_without_reduction_without_tax'];
            }
            // We must calculate the real amount of discount.
            // see @https://github.com/PrestaShop/PrestaShop/issues/32924
            $product['reduction'] = $regular_price - $price;
            $this->product['discount_amount'] = $this->priceFormatter->format($product['reduction']);
            $this->product['discount_amount_to_display'] = '-' . $this->priceFormatter->format($product['reduction']);
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
        // If the product is disabled, we disable add to cart button
        if ($product['active'] != 1) {
            return false;
        }

        // Disable because of catalog mode enabled in Prestashop settings
        if ($this->settings->catalog_mode) {
            return false;
        }

        // Disable because of "Available for order" checkbox unchecked in product settings
        if ((bool) $product['available_for_order'] === false) {
            return false;
        }

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

        // Disable because of stock management
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
        return (int) Tools::getValue('quantity_wanted', $this->product['quantity_wanted'] ?? 1);
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

        if (!isset($product['quantity_wanted'])) {
            $product['quantity_wanted'] = $this->getQuantityWanted();
        }

        // If availability date already passed, we don't want to show it
        if (isset($product['available_date'])) {
            $date = new DateTime($product['available_date']);
            if ($date < new DateTime()) {
                $product['available_date'] = null;
            }
        }

        // Default data
        $this->product['availability_message'] = null;
        $this->product['availability_submessage'] = null;
        $this->product['availability_date'] = null;
        $this->product['availability'] = null;

        // If we don't want to show availability, we return immediately
        if (!$show_availability) {
            return;
        }

        // If the product is disabled, but still displayed, we display a proper message
        if ($this->product['active'] != 1) {
            $this->product['availability_message'] = $this->translator->trans(
                'This product is no longer available for sale.',
                [],
                'Shop.Notifications.Error'
            );
            $this->product['availability'] = 'discontinued';

            return;
        }

        // Quantity available we will display is reduced by amount we want to add to cart
        $availableQuantity = $product['quantity'] - $product['quantity_wanted'];
        if (isset($product['stock_quantity'])) {
            $availableQuantity = $product['stock_quantity'] - $product['quantity_wanted'];
        }

        // Combination labels
        $combinationData = $this->getCombinationSpecificData();

        // Now, let's generate a nice availability information. We will have 4 cases to go through.
        // Case 1 - Product in stock
        if ($availableQuantity >= 0) {
            // If the products are the last items remaining, we show different message and exclamation mark
            if ($availableQuantity < $settings->lastRemainingItems) {
                $this->product['availability'] = 'last_remaining_items';
                $this->product['availability_message'] = $this->translator->trans(
                    'Last items in stock',
                    [],
                    'Shop.Theme.Catalog'
                );
            } else {
                $this->product['availability'] = 'available';

                // We will primarily use label from combination if set, then label on product, then the default label from PS settings
                if (!empty($combinationData['available_now'])) {
                    $this->product['availability_message'] = $combinationData['available_now'];
                } elseif (!empty($product['available_now'])) {
                    $this->product['availability_message'] = $product['available_now'];
                } else {
                    $config = $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS');
                    $this->product['availability_message'] = $config[$language->id] ?? null;
                }
            }

            // Case 2 - Product not in stock, available for order
        } elseif ($product['allow_oosp']) {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'available';

            // We will primarily use label from combination if set, then label on product, then the default label from PS settings
            if (!empty($combinationData['available_later'])) {
                $this->product['availability_message'] = $combinationData['available_later'];
            } elseif (!empty($product['available_later'])) {
                $this->product['availability_message'] = $product['available_later'];
            } else {
                $config = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA');
                $this->product['availability_message'] = $config[$language->id] ?? null;
            }

            // Case 3 - OOSP disabled and customer wants to add more items to cart than are in stock
        } elseif ($product['quantity'] > 0) {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'unavailable';

            $this->product['availability_message'] = $this->translator->trans(
                'There are not enough products in stock',
                [],
                'Shop.Notifications.Error'
            );

        // Case 4 - Product not in stock, not available for order
        } else {
            $this->product['availability_date'] = $product['available_date'];
            $this->product['availability'] = 'unavailable';

            // If the product has combinations and other combination is in stock, we show a small hint about it
            if ($product['cache_default_attribute'] && $product['quantity_all_versions'] > 0) {
                $this->product['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    [],
                    'Shop.Theme.Catalog'
                );
            } else {
                // We use label set in PS configuration - label is not customizable per product
                $config = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD');
                $this->product['availability_message'] = $config[$language->id] ?? null;
            }
        }
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
            'active',
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
            'manufacturer_name',
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
