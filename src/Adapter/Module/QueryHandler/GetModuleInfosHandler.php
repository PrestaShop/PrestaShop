<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetModuleInfos;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryHandler\GetModuleInfosHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

#[AsQueryHandler]
class GetModuleInfosHandler implements GetModuleInfosHandlerInterface
{
    public function __construct(
        protected ModuleRepository $moduleRepository,
    ) {
    }

    public function handle(GetModuleInfos $query): ModuleInfos
    {
        $module = $this->moduleRepository->getPresentModule($query->getTechnicalName()->getValue());

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
