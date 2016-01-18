<?php

use PrestaShop\PrestaShop\Core\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class Adapter_CartPresenter
{
    private $pricePresenter;
    private $link;
    private $translator;
    private $imageRetriever;

    public function __construct()
    {
        $this->pricePresenter = new Adapter_PricePresenter;
        $this->link = Context::getContext()->link;
        $this->translator = new Adapter_Translator;
        $this->imageRetriever = new Adapter_ImageRetriever($this->link);
    }

    private function includeTaxes()
    {
        return !Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
    }

    private function shouldShowTaxLine()
    {
        return Configuration::get('PS_TAX_DISPLAY');
    }

    protected function presentProduct(array $rawProduct)
    {
        $presenter = new ProductPresenter(
            $this->imageRetriever,
            $this->link,
            $this->pricePresenter,
            new Adapter_ProductColorsRetriever,
            $this->translator
        );

        $settings = new ProductPresentationSettings;

        $settings->catalog_mode = Configuration::get('PS_CATALOG_MODE');
        $settings->include_taxes = $this->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing =  (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');

        $rawProduct['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['up_quantity_url'] = $this->link->getUpQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['down_quantity_url'] = $this->link->getDownQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['specific_prices'] = '';
        $rawProduct['customizable'] = '';
        $rawProduct['online_only'] = '';
        $rawProduct['reduction'] = '';
        $rawProduct['new'] = '';

        $rawProduct['price'] = $this->pricePresenter->convertAndFormat(
            $this->includeTaxes() ?
            $rawProduct['price_wt'] :
            $rawProduct['price']
        );

        $rawProduct['total'] = $this->pricePresenter->convertAndFormat(
            $this->includeTaxes() ?
            $rawProduct['total_wt'] :
            $rawProduct['total']
        );

        $rawProduct['quantity_wanted'] = $rawProduct['cart_quantity'];

        return $presenter->presentForListing(
            $settings,
            $rawProduct,
            Context::getContext()->language
        );
    }

    protected function addCustomizedData(array $products, Cart $cart)
    {
        $data = Product::getAllCustomizedDatas($cart->id);

        if (!$data) {
            $data = [];
        }

        return array_map(function (array $product) use ($data) {

            $product['customizations'] = [];

            $id_product = (int)$product['id_product'];
            $id_product_attribute = (int)$product['id_product_attribute'];
            if (array_key_exists($id_product, $data)) {
                if (array_key_exists($id_product_attribute, $data[$id_product])) {
                    foreach ($data[$id_product] as $byAddress) {
                        foreach ($byAddress as $customizations) {
                            foreach ($customizations as $customization) {
                                $presentedCustomization = [
                                    'quantity'              => $customization['quantity'],
                                    'fields'                => [],
                                    'id_customization'      => null
                                ];
                                $product['up_quantity_url'] = [];
                                $product['down_quantity_url'] = [];
                                foreach ($customization['datas'] as $byType) {
                                    $field = [];
                                    foreach ($byType as $data) {
                                        switch ($data['type']) {
                                            case Product::CUSTOMIZE_FILE:
                                                $field['type'] = 'image';
                                                $field['image'] = $this->imageRetriever->getCustomizationImage(
                                                    $data['value']
                                                );
                                                break;
                                            case Product::CUSTOMIZE_TEXTFIELD:
                                                $field['type'] = 'text';
                                                $field['text'] = $data['value'];
                                                break;
                                            default:
                                                $field['type'] = null;
                                        }
                                        $field['label'] = $data['name'];
                                        $presentedCustomization['id_customization'] = $data['id_customization'];
                                    }
                                    $presentedCustomization['fields'][] = $field;
                                }

                                $presentedCustomization['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $presentedCustomization['up_quantity_url'] = $this->link->getUpQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $presentedCustomization['down_quantity_url'] = $this->link->getDownQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $product['customizations'][] = $presentedCustomization;
                            }
                        }
                    }
                }
            }

            usort($product['customizations'], function (array $a, array $b) {
                if (
                    $a['quantity'] > $b['quantity']
                    || count($a['fields']) > count($b['fields'])
                    || $a['id_customization'] > $b['id_customization']
                ) {
                    return -1;
                } else {
                    return 1;
                }
            });

            return $product;
        }, $products);
    }

    public function present(Cart $cart)
    {
        $rawProducts = $cart->getProducts(true);

        $products = array_map([$this, 'presentProduct'], $rawProducts);
        $products = $this->addCustomizedData($products, $cart);
        $subtotals = [];

        $total_excluding_tax = $cart->getOrderTotal(false);
        $total_including_tax = $cart->getOrderTotal(true);

        if ($this->shouldShowTaxLine()) {
            $subtotals['tax'] = [
                'type'   => 'tax',
                'label'  => $this->translator->l('Tax', 'Cart'),
                'amount' => $this->pricePresenter->convertAndFormat(
                    $total_including_tax - $total_excluding_tax
                )
            ];
        }

        if ($cart->gift) {
            $subtotals['gift_wrapping'] = [
                'type'   => 'gift_wrapping',
                'label'  => $this->translator->l('Gift wrapping', 'Cart'),
                'amount' => $cart->getGiftWrappingPrice($this->includeTaxes()) != 0 ? $this->pricePresenter->convertAndFormat($cart->getGiftWrappingPrice($this->includeTaxes())) : $this->translator->l('Free', 'Cart')
            ];
        }

        $shipping_cost = $cart->getTotalShippingCost(null, $this->includeTaxes());
        $subtotals['shipping'] = [
            'type'   => 'shipping',
            'label'  => $this->translator->l('Shipping', 'Cart'),
            'amount' => $shipping_cost != 0 ? $this->pricePresenter->convertAndFormat($shipping_cost) : $this->translator->l('Free', 'Cart')
        ];

        $total = [
            'type'   => 'total',
            'label'  => $this->translator->l('Total', 'Cart'),
            'amount' => $this->pricePresenter->convertAndFormat($this->includeTaxes() ? $total_including_tax : $total_excluding_tax)
        ];

        $products_count = array_reduce($products, function ($count, $product) {
            return $count + $product['quantity'];
        }, 0);

        $summary_string = $products_count === 1 ?
                          $this->translator->l('1 product', 'Cart') :
                          sprintf($this->translator->l('%d products', 'Cart'), $products_count)
        ;

        return [
            'products' => $products,
            'total'   => $total,
            'subtotals'   => $subtotals,
            'products_count' => $products_count,
            'summary_string' => $summary_string,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_address_invoice' => $cart->id_address_invoice,
        ];
    }
}
