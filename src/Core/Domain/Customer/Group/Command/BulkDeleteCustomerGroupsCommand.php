<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

final class BulkDeleteCustomerGroupsCommand
{
    /**
     * @var GroupId[]
     */
    private readonly array $customerGroupIds;

    /**
     * @param int[] $customerGroupIds
     *
     * @throws GroupConstraintException
     */
    public function __construct(array $customerGroupIds)
    {
        if (empty($customerGroupIds)) {
            throw new GroupConstraintException('No customer group provided for bulk deletion', GroupConstraintException::INVALID_ID);
        }

        $this->customerGroupIds = array_map(
            static fn (int $customerGroupId): GroupId => new GroupId($customerGroupId),
            array_values($customerGroupIds)
        );
    }

    /**
     * @return GroupId[]
     */
    public function getCustomerGroupIds(): array
    {
        return $this->customerGroupIds;
    }
}
