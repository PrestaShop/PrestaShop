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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use Doctrine\Common\Cache\CacheProvider;
use Module as ModuleLegacy;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use Symfony\Component\Finder\Finder;
use Throwable;

class ModuleRepository implements ModuleRepositoryInterface
{
    private const MODULE_ATTRIBUTES = [
        'warning',
        'name',
        'tab',
        'displayName',
        'description',
        'additional_description',
        'author',
        'limited_countries',
        'need_instance',
        'confirmUninstall',
    ];

    /** @var ModuleDataProvider */
    private $moduleDataProvider;

    /** @var AdminModuleDataProvider */
    private $adminModuleDataProvider;

    /** @var HookManager */
    private $hookManager;

    /** @var CacheProvider */
    private $cacheProvider;

    /** @var string */
    private $modulePath;

    /** @var array|null */
    private $installedModules;

    /** @var Module[] */
    private $modulesFromHook;

    /**
     * @var int
     */
    private $contextLangId;

    public function __construct(
        ModuleDataProvider $moduleDataProvider,
        AdminModuleDataProvider $adminModuleDataProvider,
        CacheProvider $cacheProvider,
        HookManager $hookManager,
        string $modulePath,
        int $contextLangId
    ) {
        $this->moduleDataProvider = $moduleDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
        $this->cacheProvider = $cacheProvider;
        $this->hookManager = $hookManager;
        $this->modulePath = $modulePath;
        $this->contextLangId = $contextLangId;
    }

    public function getList(): ModuleCollection
    {
        $modules = new ModuleCollection();
        $modulesDirsList = (new Finder())->directories()
            ->in($this->modulePath)
            ->depth('== 0')
            ->exclude(['__MACOSX'])
            ->ignoreVCS(true);

        foreach ($modulesDirsList as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            if (null === $this->getModulePath($moduleName)) {
                continue;
            }

            $modules->add($this->getModule($moduleName));
        }

        return $this->addModulesFromHook($modules);
    }

    public function getInstalledModules(): ModuleCollection
    {
        return $this->getList()->filter(static function (Module $module) {
            return $module->isInstalled();
        });
    }

    public function getMustBeConfiguredModules(): ModuleCollection
    {
        return $this->getList()->filter(static function (Module $module) {
            return $module->isConfigurable() && $module->isActive() && $module->hasValidInstance() && !empty($module->getInstance()->warning);
        });
    }

    public function getUpgradableModules(): ModuleCollection
    {
        return $this->getList()->filter(static function (Module $module) {
            return $module->canBeUpgraded();
        });
    }

    /**
     * @param string $moduleName
     *
     * @return Module
     */
    public function getModule(string $moduleName): ModuleInterface
    {
        $filePath = $this->getModulePath($moduleName);

        $filemtime = $filePath === null
            ? 0
            : (int) @filemtime($filePath);

        $cacheKey = $this->getCacheKey($moduleName);

        if ($this->cacheProvider->contains($cacheKey)) {
            /** @var Module $module */
            $module = $this->cacheProvider->fetch($cacheKey);
            if ($module->getDiskAttributes()->get('filemtime') === $filemtime) {
                return $this->enrichModuleAttributesFromHook($module);
            }
        }

        $isValid = $filemtime > 0 && $this->moduleDataProvider->isModuleMainClassValid($moduleName);
        $attributes = $this->getModuleAttributes($moduleName, $isValid);
        if (empty($attributes)) {
            $isValid = false;
        }
        $attributes = array_merge(['name' => $moduleName], $attributes);
        $disk = $this->getModuleDiskAttributes($moduleName, $isValid, $filemtime);
        $database = $this->getModuleDatabaseAttributes($moduleName);

        $coreModule = new Module($attributes, $disk, $database);
        $this->cacheProvider->save($cacheKey, $coreModule);

        return $this->enrichModuleAttributesFromHook($coreModule);
    }

    public function getModulePath(string $moduleName): ?string
    {
        $path = $this->modulePath . '/' . $moduleName;
        $filePath = $path . '/' . $moduleName . '.php';

        if (!is_file($filePath)) {
            return null;
        }

        return $path;
    }

    public function setActionUrls(ModuleCollection $collection): ModuleCollection
    {
        return $this->adminModuleDataProvider->setActionUrls($collection);
    }

    /**
     * @param string|null $moduleName The module to clear the cache for. If the name is null, the cache will be cleared for all modules.
     * @param bool $allShops Default to false. If the value is true, the cache will be cleared for all the active shops. If not it will be cleared only for the shop in the context.
     *
     * @return bool
     */
    public function clearCache(?string $moduleName = null, bool $allShops = false): bool
    {
        $this->installedModules = null;
        if ($moduleName !== null) {
            if ($allShops) {
                foreach (Shop::getShops(true, null, true) as $shopId) {
                    $cacheKey = $this->getCacheKey($moduleName, $shopId);
                    if ($this->cacheProvider->contains($cacheKey)) {
                        if (!$this->cacheProvider->delete($cacheKey)) {
                            return false;
                        }
                    }
                }

                return true;
            } else {
                $cacheKey = $this->getCacheKey($moduleName);
                if ($this->cacheProvider->contains($cacheKey)) {
                    return $this->cacheProvider->delete($cacheKey);
                }
            }
        }

        return $this->cacheProvider->deleteAll();
    }

    /**
     * @param string $moduleName
     * @param int|null $shopId If this parameter is given, the key returned will be the one for the shop. Otherwise, it will be the cache key for the shop in the context.
     *
     * @return string
     */
    protected function getCacheKey(string $moduleName, ?int $shopId = null): string
    {
        $shop = $shopId ? [$shopId] : Shop::getContextListShopID();

        return $moduleName . implode('-', $shop) . $this->contextLangId;
    }

    private function getModuleAttributes(string $moduleName, bool $isValid): array
    {
        $attributes = [];
        if ($isValid) {
            try {
                $tmpModule = ModuleLegacy::getInstanceByName($moduleName);
            } catch (Throwable $e) {
                return $attributes;
            }
            foreach (self::MODULE_ATTRIBUTES as $attribute) {
                if (isset($tmpModule->{$attribute})) {
                    $attributes[$attribute] = $tmpModule->{$attribute};
                }
            }
            $attributes['parent_class'] = get_parent_class($tmpModule);
            $attributes['is_paymentModule'] = is_subclass_of($tmpModule, 'PaymentModule');
            $attributes['is_configurable'] = method_exists($tmpModule, 'getContent');
        }

        return $attributes;
    }

    private function getModuleDiskAttributes(string $moduleName, bool $isValid, int $filemtime): array
    {
        $path = $this->modulePath . $moduleName;

        return [
            'filemtime' => $filemtime,
            'is_present' => $filemtime > 0,
            'is_valid' => $isValid,
            'version' => $isValid ? ModuleLegacy::getInstanceByName($moduleName)->version : null,
            'path' => $path,
        ];
    }

    private function getModuleDatabaseAttributes(string $moduleName): array
    {
        if ($this->installedModules === null) {
            $this->installedModules = $this->moduleDataProvider->getInstalled();
        }

        return $this->installedModules[$moduleName] ?? [];
    }

    /**
     * @return array
     */
    private function getModulesFromHook()
    {
        if ($this->modulesFromHook === null) {
            $modulesFromHook = $this->hookManager->exec('actionListModules', [], null, true);
            $modulesFromHook = array_values($modulesFromHook ?? []);

            // Merge hooks from modules if it's an array and not empty
            $filteredModulesFromHook = array_filter($modulesFromHook, function ($item) { return is_array($item); });
            $this->modulesFromHook = empty($filteredModulesFromHook) ? [] : array_merge(...$filteredModulesFromHook);
        }

        return $this->modulesFromHook;
    }

    /**
     * @param ModuleCollection $modules
     *
     * @return ModuleCollection
     */
    protected function addModulesFromHook(ModuleCollection $modules): ModuleCollection
    {
        try {
            $externalModules = $this->getModulesFromHook();
        } catch (\Throwable $e) {
            $modules->addError($e);

            return $modules;
        }

        foreach ($externalModules as $externalModule) {
            $merged = false;
            foreach ($modules as $module) {
                if ($module->get('name') === $externalModule['name']) {
                    $merged = true;
                    break;
                }
            }
            if (!$merged) {
                $modules->add(new Module($externalModule));
            }
        }

        return $modules;
    }

    /**
     * @param Module $module
     *
     * @return Module
     */
    protected function enrichModuleAttributesFromHook(Module $module): ModuleInterface
    {
        try {
            $modulesFromHook = $this->getModulesFromHook();
        } catch (\Throwable $e) {
            return $module;
        }

        foreach ($modulesFromHook as $moduleFromHook) {
            if ($module->get('name') === $moduleFromHook['name']) {
                $module->getAttributes()->add($moduleFromHook);
            }
        }

        return $module;
    }
}
