<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Class DeleteEmployeeCommand deletes given employee.
 */
class DeleteEmployeeCommand
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @param int $employeeId
     */
    public function __construct($employeeId)
    {
        $this->employeeId = new EmployeeId($employeeId);
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }
}
