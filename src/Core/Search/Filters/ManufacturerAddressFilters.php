<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerAddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class ManufacturerAddressFilters is responsible for providing filter values for manufacturer address grid.
 */
final class ManufacturerAddressFilters extends Filters
{
    /** @var string */
    protected $filterId = ManufacturerAddressGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_address',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
