<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\LogGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This class manage the defaults values for user request filters
 * of page Configure > Advanced Parameters > Logs.
 */
final class LogsFilters extends Filters
{
    /** @var string */
    protected $filterId = LogGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_log',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
