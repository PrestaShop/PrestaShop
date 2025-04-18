<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
