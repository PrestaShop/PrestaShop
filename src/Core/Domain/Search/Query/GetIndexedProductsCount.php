<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Counts the searchable products (active, visible in search) and how many of them are indexed.
 * For a single shop the shop's product settings are used, otherwise each product counts once with its default shop settings.
 */
final class GetIndexedProductsCount
{
    public function __construct(
        private readonly ShopConstraint $shopConstraint,
    ) {
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
