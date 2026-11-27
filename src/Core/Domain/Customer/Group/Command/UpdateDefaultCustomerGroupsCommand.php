<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class UpdateDefaultCustomerGroupsCommand
{
    private readonly GroupId $visitorsGroupId;

    private readonly GroupId $guestsGroupId;

    private readonly GroupId $customersGroupId;

    public function __construct(
        int $visitorsGroupId,
        int $guestsGroupId,
        int $customersGroupId,
        private readonly ShopConstraint $shopConstraint
    ) {
        $this->visitorsGroupId = new GroupId($visitorsGroupId);
        $this->guestsGroupId = new GroupId($guestsGroupId);
        $this->customersGroupId = new GroupId($customersGroupId);
    }

    public function getVisitorsGroupId(): GroupId
    {
        return $this->visitorsGroupId;
    }

    public function getGuestsGroupId(): GroupId
    {
        return $this->guestsGroupId;
    }

    public function getCustomersGroupId(): GroupId
    {
        return $this->customersGroupId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
