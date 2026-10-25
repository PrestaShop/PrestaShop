<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CategoryProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class CategoryProductFilters extends Filters
{
    /** @var string */
    protected $filterId = CategoryProductGridDefinitionFactory::GRID_ID;

    // The same filters must not follow the employee from one category to another
    protected $needsToBePersisted = false;

    public static function getDefaults(): array
    {
        return [
            'limit' => static::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_product',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
