<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendEmployeePasswordResetEmailCommand;
use PrestaShopBundle\Security\Admin\EmployeePasswordResetter;

#[AsCommandHandler]
class SendEmployeePasswordResetEmailHandler implements SendEmployeePasswordResetEmailHandlerInterface
{
    public function __construct(
        private readonly EmployeePasswordResetter $employeePasswordResetter,
    ) {
    }

    public function handle(SendEmployeePasswordResetEmailCommand $command): string
    {
        return $this->employeePasswordResetter->sendResetEmail($command->getEmail()->getValue());
    }
}
