<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Class BulkDeleteEmployeeCommand.
 */
class BulkDeleteEmployeeCommand
{
    /**
     * @var EmployeeId[]
     */
    private $employeeIds;

    /**
     * @param int[] $employeeIds
     */
    public function __construct(array $employeeIds)
    {
        $this->setEmployeeIds($employeeIds);
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
