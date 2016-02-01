<?php

namespace PrestaShop\PrestaShop\Core\Product\Search;

interface FacetsRendererInterface
{
    public function renderFacets(
        ProductSearchContext $context,
        ProductSearchResult $result
    );

    public function renderActiveFilters(
        ProductSearchContext $context,
        ProductSearchResult $result
    );
}
