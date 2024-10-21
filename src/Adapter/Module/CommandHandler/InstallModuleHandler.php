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
use PrestaShop\PrestaShop\Core\Domain\Module\Command\InstallModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\InstallModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotInstalledModuleException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;

#[AsCommandHandler]
class InstallModuleHandler implements InstallModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
    ) {
    }

    public function handle(InstallModuleCommand $command): void
    {
        $moduleName = $command->getModule();
        $source = $command->getSource();
        $result = $this->moduleManager->install($moduleName, $source);

        if (!$result) {
            throw new CannotInstalledModuleException('Technical error occurred while installing module.');
        }
    }
}
