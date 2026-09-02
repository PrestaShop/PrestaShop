<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module;

interface ModuleRepositoryInterface
{
    public function getList(): ModuleCollection;

    public function getInstalledModules(): ModuleCollection;

    public function getUpgradableModules(): ModuleCollection;

    public function getMustBeConfiguredModules(): ModuleCollection;

    public function getModule(string $moduleName): ModuleInterface;

    public function getModulePath(string $moduleName): ?string;

    public function setActionUrls(ModuleCollection $moduleCollection): ModuleCollection;
}
