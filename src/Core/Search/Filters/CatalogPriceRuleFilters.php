<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CatalogPriceRuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Responsible for providing default filters for catalog price rule grid.
 */
final class CatalogPriceRuleFilters extends Filters
{
    /** @var string */
    protected $filterId = CatalogPriceRuleGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_specific_price_rule',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
