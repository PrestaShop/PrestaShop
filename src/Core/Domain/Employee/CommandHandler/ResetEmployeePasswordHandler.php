<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeePasswordCommand;
use PrestaShopBundle\Security\Admin\EmployeePasswordResetter;
use PrestaShopBundle\Security\Admin\Exception\InvalidResetPasswordTokenException;

#[AsCommandHandler]
class ResetEmployeePasswordHandler implements ResetEmployeePasswordHandlerInterface
{
    public function __construct(
        private readonly EmployeePasswordResetter $employeePasswordResetter,
    ) {
    }

    public function handle(ResetEmployeePasswordCommand $command): void
    {
        if (!($employee = $this->employeePasswordResetter->getEmployeeByValidResetPasswordToken($command->getResetToken()))) {
            throw new InvalidResetPasswordTokenException();
        }

        $this->employeePasswordResetter->resetPassword($employee, $command->getPassword());
    }
}
