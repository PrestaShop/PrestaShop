<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters\Monitoring;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\ProductWithoutImageGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Defines default filters for product without image grid.
 */
final class ProductWithoutImageFilters extends Filters
{
    /** @var string */
    protected $filterId = ProductWithoutImageGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'name',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
