<?php

namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Symfony\Component\Translation\TranslatorInterface;
use Configuration;
use Language;
use Link;

abstract class ProductPresenterAbstract
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
        $this->imageRetriever         = $imageRetriever;
        $this->link                   = $link;
        $this->priceFormatter         = $priceFormatter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator             = $translator;
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

        if (isset($product['id_product_attribute'])) {
            foreach ($presentedProduct['images'] as $image) {
                foreach ($image['associatedVariants'] as $id) {
                    if ((int)$id === (int)$product['id_product_attribute']) {
                        $presentedProduct['cover'] = $image;
                        break 2;
                    }
                }
            }
        }

        if (!isset($presentedProduct['cover'])) {
            if (isset($presentedProduct['images'][0])) {
                $presentedProduct['cover'] = $presentedProduct['images'][0];
            } else {
                $presentedProduct['cover'] = null;
            }
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

        $presentedProduct['price_amount'] = $this->priceFormatter->format($price);
        $presentedProduct['price'] = $this->priceFormatter->format($price);
        $presentedProduct['regular_price'] = $this->priceFormatter->format($regular_price);

        return $presentedProduct;
    }

    private function addEcotaxInformation(
        array $presentedProduct,
        array $product
    ) {
        $presentedProduct['ecotax'] = [
            'value' => $this->priceFormatter->format($product['ecotax']),
            'amount' => $product['ecotax'],
            'rate' => $product['ecotax_rate'],
        ];

        return $presentedProduct;
    }

    private function addQuantityDiscountInformation(
        array $presentedProduct,
        array $product
    ) {
        $presentedProduct['quantity_discounts'] =
            (isset($product['quantity_discounts'])) ? $product['quantity_discounts'] : [];

        return $presentedProduct;
    }

    private function shouldShowAddToCartButton(
        ProductPresentationSettings $settings,
        array $product
    ) {
        $can_add_to_cart = $this->shouldShowPrice($settings, $product);

        if (($product['customizable'] == 2 || !empty($product['customization_required']))) {
            $can_add_to_cart = false;

            if (isset($product['customizations'])) {
                $can_add_to_cart = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && !$field['is_customized']) {
                        $can_add_to_cart = false;
                    }
                }
            }
        }

        if ($product['quantity'] <= 0 && !$product['allow_oosp']) {
            $can_add_to_cart = false;
        }

        return $can_add_to_cart;
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
            $presentedProduct['main_variants'] = [];
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
            $labels['online-only'] = [
                'type' => 'online-only',
                'label' => $this->translator->trans('Online only', [], 'Product')
            ];
        }

        if ($show_price && $product['on_sale'] && !$settings->catalog_mode) {
            $labels['on-sale'] = [
                'type' => 'on-sale',
                'label' => $this->translator->trans('On sale!', [], 'Product')
            ];
        }

        if ($show_price && $product['reduction'] && !$settings->catalog_mode && !$product['on_sale']) {
            $labels['discount'] = [
                'type' => 'discount',
                'label' => $this->translator->trans('Reduced price', [], 'Product')
            ];
        }

        if ($product['new']) {
            $labels['new'] = [
                'type' => 'new',
                'label' => $this->translator->trans('New', [], 'Product')
            ];
        }

        if ($product['pack']) {
            $labels['pack'] = [
                'type' => 'pack',
                'label' => $this->translator->trans('Pack', [], 'Product')
            ];
        }

        $presentedProduct['labels'] = $labels;

        return $presentedProduct;
    }

    private function addConditionInformation(
        array $presentedProduct,
        ProductPresentationSettings $settings,
        array $product
    ) {
        switch ($product['condition']) {
            case 'new':
                $presentedProduct['condition'] = [
                    'type' => 'new',
                    'label' => $this->translator->trans('New product', [], 'Product'),
                    'schema_url' => 'https://schema.org/NewCondition',
                ];
                break;
            case 'used':
                $presentedProduct['condition'] = [
                    'type' => 'used',
                    'label' => $this->translator->trans('Used', [], 'Product'),
                    'schema_url' => 'https://schema.org/UsedCondition',
                ];
                break;
            case 'refurbished':
                $presentedProduct['condition'] = [
                    'type' => 'refurbished',
                    'label' => $this->translator->trans('Refurbished', [], 'Product'),
                    'schema_url' => 'https://schema.org/RefurbishedCondition',
                ];
                break;
            default:
                $presentedProduct['condition'] = false;
        }

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

        if (isset($product['available_date']) && '0000-00-00' == $product['available_date']) {
            $product['available_date'] = null;
        }

        if ($show_availability) {
            if ($product['quantity'] > 0) {
                $presentedProduct['availability_message'] = $product['available_now'];
                $presentedProduct['availability'] = 'available';
                $presentedProduct['availability_date'] = null;
            } elseif ($product['allow_oosp']) {
                if ($product['available_later']) {
                    $presentedProduct['availability_message'] = $product['available_later'];
                    $presentedProduct['availability_date'] = $product['available_date'];
                    $presentedProduct['availability'] = 'available';
                } else {
                    $presentedProduct['availability_message'] = $this->translator->trans(
                        'Out Of Stock',
                        [],
                        'Product'
                    );
                    $presentedProduct['availability_date'] = $product['available_date'];
                    $presentedProduct['availability'] = 'unavailable';
                }
            } elseif ($product['quantity_all_versions']) {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'Product available with different options',
                    [],
                    'Product'
                );
                $presentedProduct['availability_date'] = $product['available_date'];
                $presentedProduct['availability'] = 'unavailable';
            } else {
                $presentedProduct['availability_message'] = $this->translator->trans(
                    'Out Of Stock',
                    [],
                    'Product'
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

        $show_price = $this->shouldShowPrice(
            $settings,
            $product
        );

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

        return $presentedProduct;
    }
}
