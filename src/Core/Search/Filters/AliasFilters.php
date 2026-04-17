<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AliasGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default search alias list filters
 */
class AliasFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = AliasGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_alias',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
