<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class BulkDeleteCustomerGroupCommand
{
    /** @var GroupId[] */
    private array $customerGroupIds;

    /**
     * @param int[] $customerGroupIds
     *
     * @throws GroupException
     */
    public function __construct(array $customerGroupIds)
    {
        $this->customerGroupIds = array_map(
            static fn (int $id) => new GroupId($id),
            $customerGroupIds
        );
    }

    /** @return GroupId[] */
    public function getCustomerGroupIds(): array
    {
        return $this->customerGroupIds;
    }
}
