<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\RequestSqlGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class RequestSqlFilters extends Filters
{
    /** @var string */
    protected $filterId = RequestSqlGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_request_sql',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
