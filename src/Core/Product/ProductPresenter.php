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

    private function shouldShowPrice(
        ProductPresentationSettings $settings,
        array $product
    ) {
        return  $settings->showPrices && $product['available_for_order'];
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

    private function shouldEnableAddToCartButton(
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
                        'Out of stock',
                        array(),
                        'Shop.Theme.Catalog'
                    );
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

        if ($this->shouldEnableAddToCartButton($settings, $product)) {
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

        return $presentedProduct;
    }
}
