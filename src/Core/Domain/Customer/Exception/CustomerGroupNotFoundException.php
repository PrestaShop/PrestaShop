<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Exception is thrown when a customer is assigned to a group that does not exist.
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
