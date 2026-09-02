<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\TaxRulesGroupGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Provides default filters for tax rule groups grid.
 */
final class TaxRulesGroupFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = TaxRulesGroupGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_tax_rules_group',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
