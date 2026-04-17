<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters\Security\Session;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Security\Session\EmployeeGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class EmployeeFilters is responsible for providing default filter values for Employees sessions grid.
 */
class EmployeeFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    protected $filterId = EmployeeGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_employee_session',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
