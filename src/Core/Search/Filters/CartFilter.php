<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CartGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

/**
 * Default Cart list filters
 */
class CartFilter extends ShopFilters
{
    /**
     * @var string
     */
    protected $filterId = CartGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_cart',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
