<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkUninstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\BulkUninstallModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotUninstallModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class BulkUninstallModuleHandler implements BulkUninstallModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(BulkUninstallModuleCommand $command): void
    {
        $deleteFile = $command->deleteFiles();
        // First loop through all the modules and check if they are valid for uninstallation
        foreach ($command->getModules() as $moduleName) {
            $module = $this->moduleRepository->getPresentModule($moduleName->getValue());

            if (!$module->isInstalled()) {
                throw new ModuleNotInstalledException('Cannot uninstall module ' . $moduleName->getValue() . ' since it is not installed');
            }
        }

        // Then perform the bulk action
        foreach ($command->getModules() as $moduleName) {
            $result = $this->moduleManager->uninstall($moduleName->getValue(), $deleteFile);

            if (!$result) {
                throw new CannotUninstallModuleException('Technical error occurred while uninstalling module.');
            }
        }
    }
}
