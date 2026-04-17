<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderMessageGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Defines filters for order message grid
 */
final class OrderMessageFilters extends Filters
{
    /** @var string */
    protected $filterId = OrderMessageGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_order_message',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
