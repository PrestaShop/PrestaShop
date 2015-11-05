<?php

namespace PrestaShop\PrestaShop\Core\Business\Product;

use Adapter_ObjectSerializer;
use Adapter_ImageRetriever;
use Adapter_PricePresenter;
use Adapter_ProductPriceCalculator;
use Adapter_ProductColorsRetriever;
use Adapter_Translator;
use PrestaShop\PrestaShop\Core\Business\Price\PricePresenterInterface;
use Product;
use Language;
use Link;

class ProductPresenter
{
    private $imageRetriever;
    private $link;
    private $pricePresenter;
    private $productPriceCalculator;
    private $productColorsRetriever;
    private $translator;

    public function __construct(
        Adapter_ImageRetriever $imageRetriever,
        Link $link,
        Adapter_PricePresenter $pricePresenter,
        Adapter_ProductColorsRetriever $productColorsRetriever,
        Adapter_Translator $translator
    ) {
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->pricePresenter = $pricePresenter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator = $translator;
    }

    private function shouldShowPrice(
        ProductPresentationSettings $settings,
        array $product
    ) {
        return  !$settings->catalog_mode &&
                !$settings->restricted_country_mode &&
                $product['available_for_order']
        ;
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

        if (!isset($presentedProduct['cover'])) {
            $presentedProduct['cover'] = $presentedProduct['images'][0];
        }

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

        if ($settings->include_taxes) {
            $price = $regular_price = $product['price'];
        } else {
            $price = $regular_price = $product['price_tax_exc'];
        }

        if ($product['specific_prices']) {
            $presentedProduct['has_discount'] = true;
            $presentedProduct['discount_type'] = $product['specific_prices']['reduction_type'];
            // TODO: format according to locale preferences
            $presentedProduct['discount_percentage'] = -round(100 * $product['specific_prices']['reduction'])."%";
            $regular_price = $product['price_without_reduction'];
        }

        $presentedProduct['price'] = $this->pricePresenter->format(
            $this->pricePresenter->convertAmount($price)
        );

        $presentedProduct['regular_price'] = $this->pricePresenter->format(
            $this->pricePresenter->convertAmount($regular_price)
        );

        return $presentedProduct;
    }

    private function shouldShowAddToCartButton(
        ProductPresentationSettings $settings,
        array $product
    ) {
        $can_add_to_cart = $this->shouldShowPrice($settings, $product);

        if ($product['customizable'] == 2) {
            $can_add_to_cart = false;
        }

        if (!empty($product['customization_required'])) {
            $can_add_to_cart = false;
        }

        if ($product['quantity'] <= 0 && !$product['allow_oosp']) {
            $can_add_to_cart = false;
        }

        return $can_add_to_cart;
    }

    private function getAddToCartURL(array $product)
    {
        return $this->link->getPageLink(
            'cart',
            true,
            null,
            'add=1&id_product=' . $product['id_product'] . '&id_product_attribute=' . $product['id_product_attribute'],
            false
        );
    }

    private function getProductURL(array $product, Language $language)
    {
        return $this->link->getProductLink(
            $product['id_product'],
            null, null, null,
            $language->id,
            null,
            $product['id_product_attribute']
        );
    }

    private function addMainVariantsInformation(
        array $presentedProduct,
        array $product,
        Language $language
    ) {
        $colors = $this->productColorsRetriever->getColoredVariants($product['id_product']);

        if (!is_array($colors)) {
            return $presentedProduct;
        }

        $presentedProduct['main_variants'] = array_map(function (array $color) use ($language) {
            $color['add_to_cart_url']   = $this->getAddToCartURL($color);
            $color['url']               = $this->getProductURL($color, $language);
            $color['type']              = 'color';
            $color['html_color_code']   = $color['color'];
            unset($color['color']);
            unset($color['id_attribute']); // because what is a template supposed to do with it?

            return $color;
        }, $colors);

        return $presentedProduct;
    }

    private function addLabels(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        $labels = [];

        $show_price = $this->shouldShowPrice($settings, $product);

        if ($show_price && $product['online_only']) {
            $labels['online_only'] = [
                'type' => 'online_only',
                'label' => $this->translator->l('Online only', 'Product')
            ];
        }

        if ($show_price && $product['on_sale'] && !$settings->catalog_mode) {
            $labels['on_sale'] = [
                'type' => 'on_sale',
                'label' => $this->translator->l('On sale!', 'Product')
            ];
        }

        if ($show_price && $product['reduction'] && !$settings->catalog_mode && !$product['on_sale']) {
            $labels['discount'] = [
                'type' => 'discount',
                'label' => $this->translator->l('Reduced price', 'Product')
            ];
        }

        if ($product['new']) {
            $labels['new'] = [
                'type' => 'new',
                'label' => $this->translator->l('New', 'Product')
            ];
        }

        $presentedProduct['labels'] = $labels;

        return $presentedProduct;
    }

    public function addQuantityInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        $show_price = $this->shouldShowPrice(
            $settings,
            $product
        );

        $show_availability = $show_price && $settings->stock_management_enabled;

        $presentedProduct['show_availability'] = $show_availability;

        if ($show_availability) {

            if ($product['quantity'] > 0) {
                $presentedProduct['availability_message'] = $this->translator->l(
                    'In Stock',
                    'Product'
                );
                $presentedProduct['availability'] = 'available';
            } else if ($product['allow_oosp']) {
                if ($product['available_later']) {
                    $presentedProduct['availability_message'] = $product['available_later'];
                    $presentedProduct['availability'] = 'available';
                } else {
                    $presentedProduct['availability_message'] = $this->translator->l(
                        'Out Of Stock',
                        'Product'
                    );
                    $presentedProduct['availability'] = 'unavailable';
                }
            } else if ($product['quantity_all_versions']) {
                $presentedProduct['availability_message'] = $this->translator->l(
                    'Product available with different options',
                    'Product'
                );
                $presentedProduct['availability'] = 'unavailable';
            } else {
                $presentedProduct['availability_message'] = $this->translator->l(
                    'Out Of Stock',
                    'Product'
                );
                $presentedProduct['availability'] = 'unavailable';
            }
        } else {
            $presentedProduct['availability_message'] = null;
            $presentedProduct['availability'] = null;
        }

        return $presentedProduct;
    }

    public function present(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $presentedProduct = $product;
        $show_price = $this->shouldShowPrice(
            $settings,
            $product
        );

        $presentedProduct['show_price'] = $show_price;

        $presentedProduct = $this->fillImages(
            $presentedProduct,
            $settings,
            $product,
            $language
        );

        $presentedProduct['url'] = $this->getProductURL($product, $language);

        $presentedProduct = $this->addPriceInformation(
            $presentedProduct,
            $settings,
            $product,
            $language
        );

        if ($this->shouldShowAddToCartButton($settings, $product)) {
            $presentedProduct['add_to_cart_url'] = $this->getAddToCartURL($product);
        } else {
            $presentedProduct['add_to_cart_url'] = null;
        }

        $presentedProduct = $this->addMainVariantsInformation(
            $presentedProduct,
            $product,
            $language
        );

        $presentedProduct = $this->addLabels(
            $presentedProduct,
            $settings,
            $product
        );

        $presentedProduct = $this->addQuantityInformation(
            $presentedProduct,
            $settings,
            $product
        );

        return $presentedProduct;
    }

    public function presentForListing(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $presentedProduct = $this->present(
            $settings,
            $product,
            $language
        );

        if ($product['id_product_attribute'] != 0 && !$settings->allow_add_variant_to_cart_from_listing) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        return $presentedProduct;
    }
}
