<?php

namespace PrestaShop\PrestaShop\Core\Product;

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
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme'),
            'tax_long' => ($settings->include_taxes)
                ? $this->translator->trans('Tax included', array(), 'Shop.Theme')
                : $this->translator->trans('Tax excluded', array(), 'Shop.Theme'),
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

        if ($settings->include_taxes) {
            $price = $regular_price = $product['price'];
        } else {
            $price = $regular_price = $product['price_tax_exc'];
        }

        if ($product['specific_prices']) {
            $presentedProduct['has_discount'] = (0 != $product['reduction']);
            $presentedProduct['discount_type'] = $product['specific_prices']['reduction_type'];
            // TODO: format according to locale preferences
            $presentedProduct['discount_percentage'] = -round(100 * $product['specific_prices']['reduction']).'%';
            $presentedProduct['discount_percentage_absolute'] = round(100 * $product['specific_prices']['reduction']).'%';
            // TODO: Fix issue with tax calculation
            $presentedProduct['discount_amount'] = $this->priceFormatter->format(
                $product['reduction']
            );
            $regular_price = $product['price_without_reduction'];
        }

        $presentedProduct['price_amount'] = $price;
        $presentedProduct['price'] = $this->priceFormatter->format($price);
        $presentedProduct['regular_price_amount'] = $regular_price;
        $presentedProduct['regular_price'] = $this->priceFormatter->format($regular_price);

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

    private function shouldEnableAddToCartButton(array $product)
    {
        if (($product['customizable'] == 2 || !empty($product['customization_required']))) {
            $shouldShowButton = false;

            if (isset($product['customizations'])) {
                $shouldShowButton = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && !$field['is_customized']) {
                        $shouldShowButton = false;
                    }
                }
            }
        } else {
            $shouldShowButton = true;
        }

        $shouldShowButton = $shouldShowButton && $this->shouldShowAddToCartButton($product);

        if ($product['quantity'] <= 0 && !$product['allow_oosp']) {
            $shouldShowButton = false;
        }

        return $shouldShowButton;
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
        return $this->link->getProductLink(
            $product['id_product'],
            null,
            null,
            null,
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

    public function addQuantityInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        $show_price = $this->shouldShowPrice($settings, $product);

        $show_availability = $show_price && $settings->stock_management_enabled;

        $presentedProduct['show_availability'] = $show_availability;

        if (isset($product['available_date']) && '0000-00-00' == $product['available_date']) {
            $product['available_date'] = null;
        }

        if ($show_availability) {
            if ($product['quantity'] > 0) {
                $presentedProduct['availability_date'] = $product['available_date'];
                if ($product['quantity'] < $settings->lastRemainingItems) {
                    $presentedProduct = $this->applyLastItemsInStockDisplayRule($product, $settings, $presentedProduct);
                } else {
                    $presentedProduct['availability_message'] = $product['available_now'];
                    $presentedProduct['availability'] = 'available';
                }
            } elseif ($product['allow_oosp']) {
                if ($product['available_later']) {
                    $presentedProduct['availability_message'] = $product['available_later'];
                    $presentedProduct['availability_date'] = $product['available_date'];
                    $presentedProduct['availability'] = 'available';
                } else {
                    // no default message when allow_oosp (out of stock) is enabled & available_later is empty
                    $presentedProduct['availability_message'] = null;
                    $presentedProduct['availability_date'] = $product['available_date'];
                    $presentedProduct['availability'] = 'unavailable';
                }
            } elseif ($product['quantity_all_versions']) {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    array(),
                    'Shop.Theme.Catalog'
                );
                $presentedProduct['availability_date'] = $product['available_date'];
                $presentedProduct['availability'] = 'unavailable';
            } else {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'Out of stock',
                    array(),
                    'Shop.Theme.Catalog'
                );
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
    )
    {
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
        foreach ($product['attributes'] as $attribute) {
            if (isset($attribute['reference']) && $attribute['reference'] != null) {
                $presentedProduct['reference_to_display'] = $attribute['reference'];
            } else {
                $presentedProduct['reference_to_display'] = $product['reference'];
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
                if (!empty($product[$key])) {
                    $presentedProduct['specific_references'][$key] = $product[$key];
                } else {
                    unset($presentedProduct['specific_references'][$key]);
                }
            }
        }
        if(empty($presentedProduct['specific_references'])){
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

        if ($this->shouldEnableAddToCartButton($product)) {
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
            $product
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

        // If product has attributes and it's no added to card
        if (isset($product['attributes']) && !isset($product['cart_quantity'])) {
            $presentedProduct = $this->addReferenceToDisplay(
                $product,
                $presentedProduct
            );

            $presentedProduct = $this->addAttributesSpecificReferences(
                $product,
                $presentedProduct
            );
        }
        return $presentedProduct;
    }
}
