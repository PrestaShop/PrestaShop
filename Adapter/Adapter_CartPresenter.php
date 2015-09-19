<?php

class Adapter_CartPresenter
{
    private $pricePresenter;

    public function __construct()
    {
        $this->pricePresenter = new Adapter_PricePresenter;
    }

    private function includeTaxes()
    {
        return !Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
    }

    protected function presentProduct(array $rawProduct)
    {
        $product['name'] = $rawProduct['name'];
        $product['price'] = $this->pricePresenter->convertAndFormat(
            $this->includeTaxes() ?
            $rawProduct['total_wt'] :
            $rawProduct['total']
        );

        $product['quantity'] = $rawProduct['quantity'];

        return $product;
    }

    public function present(Cart $cart)
    {
        $rawProducts = $cart->getProducts(true);

        $products = array_map([$this, 'presentProduct'], $rawProducts);

        return [
            'products' => $products,
            'total'    => $this->pricePresenter->convertAndFormat(
                $cart->getOrderTotal($this->includeTaxes())
            )
        ];
    }
}
