<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\EmployeeGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class EmployeeFilters holds search criteria for Employee grid.
 */
final class EmployeeFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = EmployeeGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_employee',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
