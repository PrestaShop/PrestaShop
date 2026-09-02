<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default addresses list filters
 */
final class AddressFilters extends Filters
{
    /** @var string */
    protected $filterId = AddressGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_address',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
