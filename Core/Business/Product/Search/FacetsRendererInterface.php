<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

interface FacetsRendererInterface
{
    public function renderFacets(
        ProductSearchContext $context,
        array $facets
    );
}
