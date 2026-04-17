<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\BulkDeleteEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotDeleteEmployeeException;

/**
 * Class BulkDeleteEmployeeHandler.
 */
#[AsCommandHandler]
final class BulkDeleteEmployeeHandler extends AbstractEmployeeHandler implements BulkDeleteEmployeeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteEmployeeCommand $command)
    {
        foreach ($command->getEmployeeIds() as $employeeId) {
            $employee = new Employee($employeeId->getValue());

            $this->assertEmployeeWasFoundById($employeeId, $employee);
            $this->assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee($employee);
            $this->assertEmployeeIsNotTheOnlyAdminInShop($employee);

            if (!$employee->delete()) {
                throw new CannotDeleteEmployeeException($employeeId, sprintf('Cannot delete employee with id "%s".', $employeeId->getValue()));
            }
        }
    }
}
