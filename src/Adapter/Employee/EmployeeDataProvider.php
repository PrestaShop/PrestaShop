<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Employee;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;

/**
 * Class EmployeeDataProvider provides employee data using legacy logic.
 */
final class EmployeeDataProvider implements EmployeeDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmployeeHashedPassword($employeeId)
    {
        $employee = new Employee($employeeId);

        return (string) $employee->passwd;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin($employeeId)
    {
        $employee = new Employee($employeeId);

        return $employee->isSuperAdmin();
    }
}
