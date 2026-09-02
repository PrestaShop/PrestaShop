<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ShipmentGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class ShipmentFilters extends Filters
{
    /** @var string */
    protected $filterId = ShipmentGridDefinitionFactory::GRID_ID;

    /**
     * @return array<string, mixed>
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'shipment_number',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
