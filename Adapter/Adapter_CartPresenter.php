<?php

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

    private function getAddToCartURL(array $product)
    {
        return $this->link->getPageLink(
            'cart',
            true,
            null,
            'delete=1&id_product=' . $product['id_product'] . '&id_product_attribute=' . $product['id_product_attribute'],
            false
        );
    }

    protected function presentProduct(array $rawProduct)
    {
        $product['name'] = $rawProduct['name'];
        $product['price'] = $this->pricePresenter->convertAndFormat(
            $this->includeTaxes() ?
            $rawProduct['total_wt'] :
            $rawProduct['total']
        );

        $product['id_product_attribute'] = $rawProduct['id_product_attribute'];
        $product['id_product'] = $rawProduct['id_product'];

        $product['remove_from_cart_url'] = $this->getAddToCartURL($rawProduct);

        $product['quantity'] = $rawProduct['quantity'];

        return $product;
    }

    protected function addCustomizedData(array $products, Cart $cart)
    {
        $data = Product::getAllCustomizedDatas($cart->id);

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
                                    'quantity' => $customization['quantity'],
                                    'fields'   => []
                                ];
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
                                    }
                                    $presentedCustomization['fields'][] = $field;
                                }
                                $product['customizations'][] = $presentedCustomization;
                            }
                        }
                    }
                }
            }

            return $product;
        }, $products);
    }

    public function present(Cart $cart)
    {
        $rawProducts = $cart->getProducts(true);

        $products = array_map([$this, 'presentProduct'], $rawProducts);

        $products = $this->addCustomizedData($products, $cart);
        $totals = [];

        $total_excluding_tax = $cart->getOrderTotal(false);
        $total_including_tax = $cart->getOrderTotal(true);

        $total = $this->includeTaxes() ? $total_including_tax : $total_excluding_tax;

        if ($this->shouldShowTaxLine()) {
            $totals['tax'] = [
                'type'   => 'tax',
                'label'  => $this->translator->l('Tax', 'Cart'),
                'amount' => $this->pricePresenter->convertAndFormat(
                    $total_including_tax - $total_excluding_tax
                )
            ];
        }

        $totals['total'] = [
            'type'   => 'total',
            'label'  => $this->translator->l('Total', 'Cart'),
            'amount' => $this->pricePresenter->convertAndFormat($total)
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
            'totals'   => $totals,
            'products_count' => $products_count,
            'summary_string' => $summary_string
        ];
    }
}
