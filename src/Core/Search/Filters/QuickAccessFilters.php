<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\QuickAccessGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class QuickAccessFilters extends Filters
{
    protected $filterId = QuickAccessGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'name',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
