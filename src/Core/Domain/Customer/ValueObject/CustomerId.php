<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Defines Customer ID with it's constraints
 */
class CustomerId implements CustomerIdInterface
{
    /**
     * @var int
     */
    private $customerId;

    /**
     * @param int $customerId
     */
    public function __construct(int $customerId)
    {
        $this->assertIntegerIsGreaterThanZero($customerId);
        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    private function assertIntegerIsGreaterThanZero(int $customerId): void
    {
        if (0 >= $customerId) {
            throw new CustomerConstraintException(
                sprintf('Customer id %s is invalid.', $customerId),
                CustomerConstraintException::INVALID_ID
            );
        }
    }
}
