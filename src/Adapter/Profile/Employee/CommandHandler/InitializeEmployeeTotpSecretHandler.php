<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\InitializeEmployeeTotpSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\InitializeEmployeeTotpSecretHandlerInterface;

#[AsCommandHandler]
final class InitializeEmployeeTotpSecretHandler implements InitializeEmployeeTotpSecretHandlerInterface
{
    public function __construct(private readonly EmployeeTwoFactorManager $manager)
    {
    }

    public function handle(InitializeEmployeeTotpSecretCommand $command): void
    {
        $this->manager->initializeTotpSecret($command->getEmployeeId()->getValue());
    }
}
