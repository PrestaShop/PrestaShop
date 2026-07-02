<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Aggregates the counters displayed above the customer thread listing:
 * total threads, threads by status, and message totals split by author type.
 */
class GetCustomerServiceListingStatistics
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
