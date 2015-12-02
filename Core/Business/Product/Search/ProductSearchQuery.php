<?php
namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Business\Product\Search\Facet;

class ProductSearchQuery
{
    private $id_category;
    private $id_manufacturer;
    private $id_supplier;
    private $search_string;
    private $search_tag;
    private $encodedFacets;

    // A default that is multiple of 2, 3 and 4 should be OK for
    // many layouts. 12 is the best number ever.
    private $resultsPerPage = 12;

    private $page = 1;

    private $sortOrder;

    public function __construct()
    {
        $this->setSortOrder(new SortOrder('product', 'name', 'ASC'));
    }

    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;
        return $this;
    }

    public function getIdCategory()
    {
        return $this->id_category;
    }

    public function setIdManufacturer($id_manufacturer)
    {
        $this->id_manufacturer = $id_manufacturer;
        return $this;
    }

    public function getIdManufacturer()
    {
        return $this->id_manufacturer;
    }

    public function setIdSupplier($id_supplier)
    {
        $this->id_supplier = $id_supplier;
        return $this;
    }

    public function getIdSupplier()
    {
        return $this->id_supplier;
    }

    public function setSortOption(SortOption $option)
    {
        $this->sortOption = $sortOption;
        return $this;
    }

    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = (int)$resultsPerPage;
        return $this;
    }

    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    public function setPage($page)
    {
        $this->page = (int)$page;
        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setSortOrder(SortOrder $sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setSearchString($search_string)
    {
        $this->search_string = $search_string;
        return $this;
    }

    public function getSearchString()
    {
        return $this->search_string;
    }

    public function setSearchTag($search_tag)
    {
        $this->search_tag = $search_tag;
        return $this;
    }

    public function getSearchTag()
    {
        return $this->search_tag;
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
