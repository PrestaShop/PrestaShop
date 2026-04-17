<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\ResetModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\ResetModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotResetModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class ResetModuleHandler implements ResetModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(ResetModuleCommand $command): void
    {
        $module = $this->moduleRepository->getPresentModule($command->getTechnicalName()->getValue());
        if (!$module->isInstalled()) {
            throw new ModuleNotInstalledException('Cannot reset module ' . $command->getTechnicalName()->getValue() . ' since it is not installed');
        }

        if (!$this->moduleManager->reset($command->getTechnicalName()->getValue(), $command->keepData())) {
            throw new CannotResetModuleException('Technical error occurred while resetting module status.');
        }
    }
}
