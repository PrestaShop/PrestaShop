<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FeatureGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

/**
 * Class ManufacturerFilters is responsible for providing filter values for manufacturer grid.
 */
final class FeatureFilters extends ShopFilters
{
    /** @var string */
    protected $filterId = FeatureGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'name',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
