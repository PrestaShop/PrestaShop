<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

interface ProductSearchProviderInterface
{
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    );
}
