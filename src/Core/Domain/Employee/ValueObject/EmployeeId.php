<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;

/**
 * Defines Employee ID with it's constraints.
 */
class EmployeeId implements EmployeeIdInterface
{
    /**
     * @var int
     */
    private $employeeId;

    /**
     * @param int $employeeId
     *
     * @throws InvalidEmployeeIdException
     */
    public function __construct($employeeId)
    {
        $this->assertIntegerIsGreaterThanZero($employeeId);

        $this->employeeId = $employeeId;
    }

    public function getValue(): int
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     *
     * @throws InvalidEmployeeIdException
     */
    private function assertIntegerIsGreaterThanZero($employeeId)
    {
        if (!is_int($employeeId) || 0 > $employeeId) {
            throw new InvalidEmployeeIdException(sprintf('Invalid employee id %s supplied. Employee id must be positive integer.', var_export($employeeId, true)));
        }
    }
}
