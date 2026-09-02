<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\UninstallModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotUninstallModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class UninstallModuleHandler implements UninstallModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(UninstallModuleCommand $command): void
    {
        $module = $this->moduleRepository->getPresentModule($command->getTechnicalName()->getValue());

        // Cannot perform uninstall action of the module is not installed yet UNLESS the commands aims at removing files
        if (!$module->isInstalled() && !$command->deleteFiles()) {
            throw new ModuleNotInstalledException('Cannot uninstall module ' . $command->getTechnicalName()->getValue() . ' since it is not installed');
        }

        if ($module->isInstalled()) {
            $result = $this->moduleManager->uninstall($command->getTechnicalName()->getValue(), $command->deleteFiles());
        } else {
            $result = $this->moduleManager->delete($command->getTechnicalName()->getValue());
        }

        if (!$result) {
            throw new CannotUninstallModuleException('Technical error occurred while uninstalling module.');
        }
    }
}
