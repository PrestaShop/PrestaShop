<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchResult
{
    private $products = [];
    private $nextQuery;

    public function setProducts(array $products)
    {
        $this->products = $products;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setNextQuery(ProductSearchQuery $nextQuery)
    {
        $this->nextQuery = $nextQuery;
        return $this;
    }

    public function getNextQuery()
    {
        return $this->nextQuery;
    }
}
