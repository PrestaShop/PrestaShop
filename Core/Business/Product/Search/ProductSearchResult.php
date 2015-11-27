<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchResult
{
    private $products = [];
    private $nextQuery;
    private $encodedFacets;
    private $paginationResult;
    private $availableSortOrders = [];

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

    public function setPaginationResult(PaginationResult $paginationResult)
    {
        $this->paginationResult = $paginationResult;
        return $this;
    }

    public function getPaginationResult()
    {
        return $this->paginationResult;
    }

    public function addAvailableSortOrder(SortOrder $sortOrder)
    {
        $this->availableSortOrders[] = $sortOrder;
        return $this;
    }

    public function getAvailableSortOrders()
    {
        return $this->availableSortOrders;
    }

    public function setAvailableSortOrders(array $sortOrders)
    {
        $this->availableSortOrders = [];

        foreach ($sortOrders as $sortOrder) {
            $this->addAvailableSortOrder($sortOrder);
        }

        return $this;
    }
}
