<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpgradeModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\UpgradeModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotUpgradeModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleAlreadyUpToDateException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class UpgradeModuleHandler implements UpgradeModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(UpgradeModuleCommand $command): void
    {
        $technical_name = $command->getTechnicalName()->getValue();

        $module = $this->moduleRepository->getModule($technical_name);

        if (!$module->isInstalled()) {
            throw new ModuleNotInstalledException('Module is not installed.');
        }

        if (!$module->canBeUpgraded()) {
            throw new ModuleAlreadyUpToDateException('Module is already up to date.');
        }

        $result = $this->moduleManager->upgrade($technical_name);

        if (!$result) {
            throw new CannotUpgradeModuleException('Technical error occurred while updating module.');
        }
    }
}
