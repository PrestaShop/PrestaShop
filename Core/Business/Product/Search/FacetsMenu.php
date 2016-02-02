<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class FacetsMenu
{
    private $facets = [];

    public function addFacet(Facet $facet)
    {
        $this->facets[] = $facet;
        return $this;
    }

    public function setFacets(array $facets)
    {
        $this->facets = [];
        foreach ($facets as $facet) {
            $this->addFacet($facet);
        }

        return $this;
    }

    public function getFacets()
    {
        return $this->facets;
    }
}
