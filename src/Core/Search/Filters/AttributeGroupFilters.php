<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AttributeGroupGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Responsible for providing filter values for attribute groups list
 */
final class AttributeGroupFilters extends Filters
{
    /** @var string */
    protected $filterId = AttributeGroupGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
