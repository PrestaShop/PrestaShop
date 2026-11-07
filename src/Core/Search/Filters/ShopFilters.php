<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ShopGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class ShopFilters extends Filters
{
    protected $filterId = ShopGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_shop',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
