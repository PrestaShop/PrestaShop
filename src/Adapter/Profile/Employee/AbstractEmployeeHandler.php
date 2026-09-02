<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

abstract class AbstractEmployeeHandler
{
    /**
     * @param EmployeeId $employeeId
     *
     * @return Employee
     *
     * @throws EmployeeNotFoundException
     */
    protected function getEmployee(EmployeeId $employeeId): Employee
    {
        $employee = new Employee($employeeId->getValue());

        if ($employee->id !== $employeeId->getValue()) {
            throw new EmployeeNotFoundException($employeeId, sprintf('Employee with id "%s" was not found', $employeeId->getValue()));
        }

        return $employee;
    }
}
