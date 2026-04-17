<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\MerchandiseReturnGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Provides default filters for merchandise returns grid.
 */
final class MerchandiseReturnFilters extends Filters
{
    protected $filterId = MerchandiseReturnGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_order_return',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
