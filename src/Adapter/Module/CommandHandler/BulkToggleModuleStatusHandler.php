<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use Module;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\BulkToggleModuleStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotInstalledException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Psr\Log\LoggerInterface;

/**
 * Bulk toggles Module status
 */
#[AsCommandHandler]
class BulkToggleModuleStatusHandler implements BulkToggleModuleStatusHandlerInterface
{
    /**
     * @param ModuleManager $moduleManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ModuleManager $moduleManager,
        private readonly ModuleRepository $moduleRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleModuleStatusCommand $command): void
    {
        $modulesToUpdate = [];
        // First loop checks that the provided modules exist and don't need some update
        // If one module is not found the whole bulk is cancelled because an exception is thrown
        foreach ($command->getModules() as $moduleName) {
            $module = $this->moduleRepository->getPresentModule($moduleName);
            if (!$module->isInstalled()) {
                throw new ModuleNotInstalledException('Cannot toggle status for module ' . $moduleName . ' since it is not installed');
            }

            if ($this->isDisablingAlreadyDisabledModule($command->getExpectedStatus(), $moduleName)) {
                continue;
            }
            $modulesToUpdate[] = $moduleName;
        }

        // Now we can perform the toggle
        foreach ($modulesToUpdate as $moduleName) {
            if ($command->getExpectedStatus()) {
                if ($this->moduleManager->enable($moduleName)) {
                    $this->logger->warning(
                        sprintf(
                            'The module %s has been enabled',
                            $moduleName
                        )
                    );
                }
            } else {
                if ($this->moduleManager->disable($moduleName)) {
                    $this->logger->warning(
                        sprintf(
                            'The module %s has been disabled',
                            $moduleName
                        )
                    );
                }
            }
        }
    }

    private function isDisablingAlreadyDisabledModule(bool $expectedStatus, string $moduleName): bool
    {
        return !$expectedStatus && !$this->moduleManager->isInstalledAndActive($moduleName);
    }
}
