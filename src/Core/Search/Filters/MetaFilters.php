<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\MetaGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class MetaFilters is responsible for providing default filter values for Seo & urls list.
 */
final class MetaFilters extends Filters
{
    /** @var string */
    protected $filterId = MetaGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_meta',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
