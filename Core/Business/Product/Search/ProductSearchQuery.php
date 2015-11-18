<?php
namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Business\Product\Search\Facet;

class ProductSearchQuery
{
    private $id_category;

    // A default that is multiple of 2, 3 and 4 should be OK for
    // many layouts. 12 is the best number ever.
    private $resultsPerPage = 12;

    private $page = 1;

    private $sortOrder;

    private $facets = [];

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

    public function addFacet(Facet $facet)
    {
        $this->facets[] = $facet;
        return $this;
    }

    public function getFacets()
    {
        return $this->facets;
    }

    public function setFacets(array $facets)
    {
        $this->facets = [];
        // We're not directly replacing the $this->facets
        // array because we want to ensure that $facet
        // is of type Facet.
        // Performance impact negligible because $facets
        // is always a small array.
        foreach ($facets as $facet) {
            $this->addFacet($facet);
        }
        return $this;
    }
}
