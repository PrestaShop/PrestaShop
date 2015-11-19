<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class FacetsURLSerializer
{
    public function serialize(array $facets)
    {
        $facetFilters = [];

        $urlSerializer = new URLFragmentSerializer;

        foreach ($facets as $facet) {
            foreach ($facet->getFilters() as $facetFilter) {
                if ($facetFilter->isActive()) {
                    $facetFilters[$facet->getLabel()][] = $facetFilter->getLabel();
                }
            }
        }

        return $urlSerializer->serialize($facetFilters);
    }
}
