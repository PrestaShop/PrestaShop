<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

/**
 * Gets product grid filters.
 */
final class ProductFilters extends ShopFilters
{
    protected $filterId = ProductGridDefinitionFactory::GRID_ID;

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
