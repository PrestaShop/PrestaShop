<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CustomerThreadGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default Customer thread list filters
 */
class CustomerThreadFilter extends Filters
{
    /**
     * @var string
     */
    protected $filterId = CustomerThreadGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_customer_thread',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
