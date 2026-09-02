<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Class UpdateEmployeesStatusCommand updates employees status.
 */
class BulkUpdateEmployeeStatusCommand
{
    /**
     * @var bool
     */
    private $status;

    /**
     * @var EmployeeId[]
     */
    private $employeeIds;

    /**
     * @param int[] $employeeIds
     * @param bool $status
     */
    public function __construct(array $employeeIds, $status)
    {
        $this->status = $status;

        $this->setEmployeeIds($employeeIds);
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return EmployeeId[]
     */
    public function getEmployeeIds()
    {
        return $this->employeeIds;
    }

    /**
     * @param int[] $employeeIds
     */
    private function setEmployeeIds(array $employeeIds)
    {
        foreach ($employeeIds as $employeeId) {
            $this->employeeIds[] = new EmployeeId((int) $employeeId);
        }
    }
}
