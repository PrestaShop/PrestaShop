<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters\Security\Session;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Security\Session\CustomerGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class CustomerFilters is responsible for providing default filter values for Customer sessions grid.
 */
class CustomerFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    protected $filterId = CustomerGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_customer_session',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
