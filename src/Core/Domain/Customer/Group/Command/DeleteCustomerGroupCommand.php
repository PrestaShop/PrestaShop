<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;

class DeleteCustomerGroupCommand
{
    private CustomerGroupId $customerGroupId;

    public function __construct(int $customerGroupId)
    {
        $this->customerGroupId = new CustomerGroupId($customerGroupId);
    }

    public function getCustomerGroupId(): CustomerGroupId
    {
        return $this->customerGroupId;
    }
}
