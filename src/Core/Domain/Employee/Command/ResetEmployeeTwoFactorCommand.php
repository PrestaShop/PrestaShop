<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

class ResetEmployeeTwoFactorCommand
{
    private EmployeeId $employeeId;

    public function __construct(int $employeeId)
    {
        $this->employeeId = new EmployeeId($employeeId);
    }

    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }
}
