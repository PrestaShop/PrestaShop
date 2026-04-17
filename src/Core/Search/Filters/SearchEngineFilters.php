<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\SearchEngineGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class SearchEngineFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = SearchEngineGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_search_engine',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
