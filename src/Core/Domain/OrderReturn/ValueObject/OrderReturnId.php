<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;

/**
 * Provides order return id
 */
class OrderReturnId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $id)
    {
        $this->assertIsIntegerGreaterThanZero($id);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws OrderReturnConstraintException
     */
    private function assertIsIntegerGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new OrderReturnConstraintException(sprintf('Invalid order return id "%s".', $value), OrderReturnConstraintException::INVALID_ID);
        }
    }
}
