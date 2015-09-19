<?php

class Adapter_CartPresenter
{
    private $pricePresenter;
    private $link;
    private $translator;

    public function __construct()
    {
        $this->pricePresenter = new Adapter_PricePresenter;
        $this->link = Context::getContext()->link;
        $this->translator = new Adapter_Translator;
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

        $product['remove_from_cart_url'] = $this->getAddToCartURL($rawProduct);

        $product['quantity'] = $rawProduct['quantity'];

        return $product;
    }

    public function present(Cart $cart)
    {
        $rawProducts = $cart->getProducts(true);

        $products = array_map([$this, 'presentProduct'], $rawProducts);

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

        return [
            'products' => $products,
            'totals'   => $totals
        ];
    }
}
