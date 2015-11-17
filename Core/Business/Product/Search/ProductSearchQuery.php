<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchQuery
{
    private $id_category;

    // A default that is multiple of 2, 3 and 4 should be OK for
    // many layouts. 12 is the best number ever.
    private $resultsPerPage = 12;

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
}
