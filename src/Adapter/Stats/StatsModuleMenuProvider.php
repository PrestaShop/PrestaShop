<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Stats;

use Module;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;

/**
 * Builds the menu of "stats*" modules displayed on the Stats page, i.e. the modules
 * registered on the "displayAdminStatsModules" hook.
 */
final class StatsModuleMenuProvider
{
    private const HOOK_NAME = 'displayAdminStatsModules';

    public function __construct(
        private readonly HookInformationProvider $hookInformationProvider
    ) {
    }

    /**
     * @return array<int, array{name: string, displayName: string}>
     */
    public function getModules(): array
    {
        $modules = [];
        foreach ($this->hookInformationProvider->getRegisteredModulesByHookName(self::HOOK_NAME) as $registeredModule) {
            $moduleInstance = Module::getInstanceByName($registeredModule['module']);
            if (!$moduleInstance) {
                continue;
            }

            $modules[] = [
                'name' => $registeredModule['module'],
                'displayName' => $moduleInstance->displayName,
            ];
        }

        usort($modules, fn (array $a, array $b) => strcasecmp($a['displayName'], $b['displayName']));

        return $modules;
    }
}
