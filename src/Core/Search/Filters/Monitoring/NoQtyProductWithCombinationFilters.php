<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters\Monitoring;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\NoQtyProductWithCombinationGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Defines default filters for product with combination grid.
 */
final class NoQtyProductWithCombinationFilters extends Filters
{
    /** @var string */
    protected $filterId = NoQtyProductWithCombinationGridDefinitionFactory::GRID_ID;

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
