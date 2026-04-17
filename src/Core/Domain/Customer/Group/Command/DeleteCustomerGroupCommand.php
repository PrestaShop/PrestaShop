<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class DeleteCustomerGroupCommand
{
    private GroupId $customerGroupId;

    public function __construct(int $groupId)
    {
        $this->customerGroupId = new GroupId($groupId);
    }

    public function getCustomerGroupId(): GroupId
    {
        return $this->customerGroupId;
    }
}
