<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class GetDefaultCustomerGroups
{
    public function __construct(
        private readonly ShopConstraint $shopConstraint
    ) {
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
