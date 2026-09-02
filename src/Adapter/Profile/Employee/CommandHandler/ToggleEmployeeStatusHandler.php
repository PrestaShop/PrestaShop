<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ToggleEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\ToggleEmployeeStatusHandlerInterface;

/**
 * Class ToggleEmployeeStatusHandler encapsulates Employee status toggling using legacy Employee object model.
 */
#[AsCommandHandler]
final class ToggleEmployeeStatusHandler extends AbstractEmployeeHandler implements ToggleEmployeeStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleEmployeeStatusCommand $command)
    {
        $employeeId = $command->getEmployeeId();
        $employee = new Employee($employeeId->getValue());

        $this->assertEmployeeWasFoundById($employeeId, $employee);
        $this->assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee($employee);
        $this->assertEmployeeIsNotTheOnlyAdminInShop($employee);

        $employee->toggleStatus();
    }
}
