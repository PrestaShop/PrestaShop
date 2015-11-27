<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

interface ProductSearchProviderInterface
{
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    );

    public function addFacetsToQuery(
        ProductSearchContext $context,
        $encodedFacets,
        ProductSearchQuery $query
    );
}
