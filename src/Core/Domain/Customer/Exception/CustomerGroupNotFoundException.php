<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Exception is thrown when a customer is assigned to a group that does not exist.
 *
 * Nothing constrains ps_customer_group at database level, so an unknown group id is accepted
 * silently and only surfaces later, when something tries to load the group.
 */
class CustomerGroupNotFoundException extends CustomerException
{
    /**
     * @param int[] $groupIds
     */
    public static function fromGroupIds(array $groupIds): self
    {
        return new self(sprintf(
            'Customer cannot be assigned to non-existing group(s) "%s"',
            implode('", "', $groupIds)
        ));
    }
}
