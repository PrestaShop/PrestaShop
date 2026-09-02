<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources;

use PrestaShop\PrestaShop\Core\Search\ShopFilters;

class SampleShopFilters extends ShopFilters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 13,
            'offset' => 69,
            'orderBy' => 'id_shop_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
