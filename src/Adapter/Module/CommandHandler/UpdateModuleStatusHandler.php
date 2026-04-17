<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpdateModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\UpdateModuleStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotToggleModuleStatusException;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsCommandHandler]
class UpdateModuleStatusHandler implements UpdateModuleStatusHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(UpdateModuleStatusCommand $command): void
    {
        $module = $this->moduleRepository->getPresentModule($command->getTechnicalName()->getValue());

        if (!$module->isInstalled()) {
            throw new ModuleNotInstalledException('Cannot toggle status for module ' . $command->getTechnicalName()->getValue() . ' since it is not installed');
        }

        if ($command->isEnabled()) {
            $result = $this->moduleManager->enable($command->getTechnicalName()->getValue());
        } else {
            $result = $this->moduleManager->disable($command->getTechnicalName()->getValue());
        }

        if (!$result) {
            throw new CannotToggleModuleStatusException('Technical error occurred while toggling module status.');
        }
    }
}
