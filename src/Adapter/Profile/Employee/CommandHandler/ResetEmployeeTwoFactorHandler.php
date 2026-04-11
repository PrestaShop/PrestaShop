<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeeTwoFactorCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;

#[AsCommandHandler]
final class ResetEmployeeTwoFactorHandler extends AbstractEmployeeHandler
{
    public function handle(ResetEmployeeTwoFactorCommand $command): void
    {
        $employee = new Employee($command->getEmployeeId()->getValue());

        $this->assertEmployeeWasFoundById($command->getEmployeeId(), $employee);
        $this->assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee($employee);

        $employee->two_factor_enabled = false;
        $employee->two_factor_totp_enabled = false;
        $employee->two_factor_email_enabled = false;
        $employee->two_factor_totp_secret = null;

        if (false === $employee->update()) {
            throw new EmployeeException(sprintf('Cannot reset 2FA for employee with id "%s"', $employee->id));
        }
    }
}
