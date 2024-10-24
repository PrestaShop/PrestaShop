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
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpdateModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\UpdateModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotUpdateModuleException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class UpdateModuleHandler implements UpdateModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(UpdateModuleCommand $command): void
    {
        $technical_name = $command->getTechnicalName()->getValue();
        $source = $command->getSource();

        $module = $this->moduleRepository->getModule($technical_name);

        if(!$module->isInstalled()) {
            throw new ModuleNotInstalledException('Module is not installed.');
        }

        $result = $this->moduleManager->upgrade($technical_name, $source);

        if (!$result) {
            throw new CannotUpdateModuleException('Technical error occurred while updating module.');
        }
    }
}
