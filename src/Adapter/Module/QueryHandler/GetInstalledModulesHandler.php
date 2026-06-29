<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\QueryHandler;

use Module;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetInstalledModules;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryHandler\GetInstalledModulesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\InstalledModule;

#[AsQueryHandler]
class GetInstalledModulesHandler implements GetInstalledModulesHandlerInterface
{
    /**
     * @return InstalledModule[]
     */
    public function handle(GetInstalledModules $query): array
    {
        $modules = Module::getModulesInstalled() ?: [];

        return array_map(
            static fn (array $row) => new InstalledModule((int) $row['id_module'], (string) $row['name']),
            $modules
        );
    }
}
