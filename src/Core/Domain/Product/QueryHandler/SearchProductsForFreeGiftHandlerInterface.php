<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForFreeGift;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForFreeGift;

/**
 * Search products eligible for free gift discounts, returning disabled state
 * for products that cannot be used as gifts.
 */
interface SearchProductsForFreeGiftHandlerInterface
{
    /**
     * @param SearchProductsForFreeGift $query
     *
     * @return ProductForFreeGift[]
     */
    public function handle(SearchProductsForFreeGift $query): array;
}
