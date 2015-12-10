<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchResult
{
    private $products = [];
    private $facetCollection;
    private $encodedFacets;
    private $pagination;
    private $availableSortOrders = [];
    private $currentSortOrder;

    public function setProducts(array $products)
    {
        $this->products = $products;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setFacetCollection(FacetCollection $facetCollection)
    {
        $this->facetCollection = $facetCollection;
        return $this;
    }

    public function getFacetCollection()
    {
        return $this->facetCollection;
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

    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function getPagination()
    {
        return $this->pagination;
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

    public function setCurrentSortOrder(SortOrder $currentSortOrder)
    {
        $this->currentSortOrder = $currentSortOrder;
        return $this;
    }

    public function getCurrentSortOrder()
    {
        return $this->currentSortOrder;
    }
}
