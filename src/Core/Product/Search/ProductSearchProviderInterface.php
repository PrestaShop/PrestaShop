<?php

namespace PrestaShop\PrestaShop\Core\Product\Search;

interface ProductSearchProviderInterface
{
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    );
}
