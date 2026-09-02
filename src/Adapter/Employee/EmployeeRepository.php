<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class EmployeeRepository extends AbstractObjectModelRepository
{
    public function get(int $employeeId): Employee
    {
        /** @var Employee $employee */
        $employee = $this->getObjectModel($employeeId, Employee::class, EmployeeNotFoundException::class);

        return $employee;
    }
}
