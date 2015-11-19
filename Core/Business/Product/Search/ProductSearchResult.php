<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchResult
{
    private $products = [];
    private $nextQuery;
    private $encodedFacets;

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

    public function setEncodedFacets($encodedFacets)
    {
        $this->encodedFacets = $encodedFacets;
        return $this;
    }

    public function getEncodedFacets()
    {
        return $this->encodedFacets;
    }
}
