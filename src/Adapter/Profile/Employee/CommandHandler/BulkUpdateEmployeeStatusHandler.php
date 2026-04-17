<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\BulkUpdateEmployeeStatusHandlerInterface;

/**
 * Class BulkUpdateEmployeeStatusHandler.
 */
#[AsCommandHandler]
final class BulkUpdateEmployeeStatusHandler extends AbstractEmployeeHandler implements BulkUpdateEmployeeStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateEmployeeStatusCommand $command)
    {
        foreach ($command->getEmployeeIds() as $employeeId) {
            $employee = new Employee($employeeId->getValue());

            $this->assertEmployeeWasFoundById($employeeId, $employee);
            $this->assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee($employee);
            $this->assertEmployeeIsNotTheOnlyAdminInShop($employee);

            $employee->active = $command->getStatus();
            $employee->save();
        }
    }
}
