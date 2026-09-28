<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CspLogGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

/** Default filters for the CSP violation log grid; extends ShopFilters so rows are scoped to the shops in context. */
final class CspLogFilters extends ShopFilters
{
    /**
     * @var string
     */
    protected $filterId = CspLogGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_csp_log',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
