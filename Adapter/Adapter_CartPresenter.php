<?php

class Adapter_CartPresenter
{
    private $pricePresenter;
    private $link;

    public function __construct()
    {
        $this->pricePresenter = new Adapter_PricePresenter;
        $this->link = Context::getContext()->link;
    }

    private function includeTaxes()
    {
        return !Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
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

        return [
            'products' => $products,
            'total'    => $this->pricePresenter->convertAndFormat(
                $cart->getOrderTotal($this->includeTaxes())
            )
        ];
    }
}
