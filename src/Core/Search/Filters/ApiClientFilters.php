<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ApiClientGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class ApiClientFilters extends Filters
{
    /** @var string */
    protected $filterId = ApiClientGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_api_client',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
