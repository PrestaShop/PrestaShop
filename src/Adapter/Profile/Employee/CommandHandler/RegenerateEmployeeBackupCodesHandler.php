<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\RegenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\RegenerateEmployeeBackupCodesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;

#[AsCommandHandler]
final class RegenerateEmployeeBackupCodesHandler implements RegenerateEmployeeBackupCodesHandlerInterface
{
    public function __construct(private readonly EmployeeTwoFactorManager $manager)
    {
    }

    public function handle(RegenerateEmployeeBackupCodesCommand $command): GeneratedEmployeeBackupCodes
    {
        return $this->manager->regenerateBackupCodes($command->getEmployeeId()->getValue(), $command->getProvider(), $command->getCode());
    }
}
