<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\StoreGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

class StoreFilters extends ShopFilters
{
    /** @var string */
    protected $filterId = StoreGridDefinitionFactory::GRID_ID;

    /**
     * @return array<string, mixed>
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_store',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
