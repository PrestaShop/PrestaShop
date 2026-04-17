<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\DeleteEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotDeleteEmployeeException;

/**
 * Class DeleteEmployeeHandler.
 */
#[AsCommandHandler]
final class DeleteEmployeeHandler extends AbstractEmployeeHandler implements DeleteEmployeeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteEmployeeCommand $command)
    {
        $employeeId = $command->getEmployeeId();
        $employee = new Employee($employeeId->getValue());

        $this->assertEmployeeWasFoundById($employeeId, $employee);
        $this->assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee($employee);
        $this->assertEmployeeIsNotTheOnlyAdminInShop($employee);

        if (!$employee->delete()) {
            throw new CannotDeleteEmployeeException($employeeId, sprintf('Cannot delete employee with id "%s".', $employeeId->getValue()));
        }
    }
}
