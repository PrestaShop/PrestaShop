<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CurrencyGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class CurrencyFilters is responsible for providing default filters for currency grid.
 */
final class CurrencyFilters extends Filters
{
    /** @var string */
    protected $filterId = CurrencyGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_currency',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
