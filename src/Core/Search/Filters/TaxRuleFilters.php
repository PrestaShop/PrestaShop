<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\TaxRuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Provides default filters for tax rule grid.
 */
class TaxRuleFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = TaxRuleGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_tax_rule',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
