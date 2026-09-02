<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ExtraPropertyDefinitionGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default filters for the extra property definition grid.
 */
final class ExtraPropertyDefinitionFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = ExtraPropertyDefinitionGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_extra_property_definition',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
