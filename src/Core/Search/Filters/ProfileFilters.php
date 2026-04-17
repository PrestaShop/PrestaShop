<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProfileGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class ProfilesFilters is responsible for providing default filter values for Profiles grid.
 */
final class ProfileFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = ProfileGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_profile',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
