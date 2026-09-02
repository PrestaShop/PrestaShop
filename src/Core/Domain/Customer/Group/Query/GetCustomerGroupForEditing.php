<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class GetCustomerGroupForEditing
{
    /**
     * @var GroupId
     */
    private $customerGroupId;

    public function __construct(int $customerGroupId)
    {
        $this->customerGroupId = new GroupId($customerGroupId);
    }

    /**
     * @return GroupId
     */
    public function getCustomerGroupId(): GroupId
    {
        return $this->customerGroupId;
    }
}
