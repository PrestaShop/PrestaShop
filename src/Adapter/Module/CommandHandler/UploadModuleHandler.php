<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UploadModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\UploadModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\CannotUploadModuleException;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Throwable;

#[AsCommandHandler]
class UploadModuleHandler implements UploadModuleHandlerInterface
{
    public function __construct(
        protected ModuleManager $moduleManager,
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(UploadModuleCommand $command): ModuleInfos
    {
        try {
            $technicalName = $this->moduleManager->upload($command->getSource());
        } catch (Throwable) {
            throw new CannotUploadModuleException('Technical error occurred while uploading module.');
        }

        $module = $this->moduleRepository->getPresentModule($technicalName);

        return new ModuleInfos(
            $module->database->get('id'),
            $module->get('name'),
            $module->disk->get('version'),
            $module->database->get('version', null),
            $module->isActive(),
            $module->isInstalled(),
        );
    }
}
