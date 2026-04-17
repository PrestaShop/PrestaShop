<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderReturnStatesGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class OrderReturnStatesFilters provides default filters for order return states grid.
 */
final class OrderReturnStatesFilters extends Filters
{
    public const LIST_LIMIT = 50;

    /** @var string */
    protected $filterId = OrderReturnStatesGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_order_return_state',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
