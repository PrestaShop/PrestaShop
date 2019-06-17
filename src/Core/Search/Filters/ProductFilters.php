<?php

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Gets product grid filters.
 */
final class ProductFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_product',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
