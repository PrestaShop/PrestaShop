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
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleInterface;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;

class ModuleRepository implements ModuleRepositoryInterface, EventSubscriberInterface
{
    private const MODULE_ATTRIBUTES = [
        'warning', 'name', 'tab', 'displayName', 'description', 'author',
        'limited_countries', 'need_instance', 'confirmUninstall',
    ];

    /** @var Finder */
    private $finder;

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

    public function __construct(
        ModuleDataProvider $moduleDataProvider,
        AdminModuleDataProvider $adminModuleDataProvider,
        CacheProvider $cacheProvider,
        HookManager $hookManager,
        string $modulePath
    ) {
        $this->finder = new Finder();
        $this->moduleDataProvider = $moduleDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
        $this->cacheProvider = $cacheProvider;
        $this->hookManager = $hookManager;
        $this->modulePath = $modulePath;
    }

    public static function getSubscribedEvents()
    {
        return [
            ModuleManagementEvent::INSTALL => 'onModuleStateChanged',
            ModuleManagementEvent::UNINSTALL => 'onModuleStateChanged',
            ModuleManagementEvent::ENABLE => 'onModuleStateChanged',
            ModuleManagementEvent::DISABLE => 'onModuleStateChanged',
            ModuleManagementEvent::UPGRADE => 'onModuleStateChanged',
            ModuleManagementEvent::ENABLE_MOBILE => 'onModuleStateChanged',
            ModuleManagementEvent::DISABLE_MOBILE => 'onModuleStateChanged',
        ];
    }

    public function onModuleStateChanged(ModuleManagementEvent $event)
    {
        $moduleName = $event->getModule()->get('name');
        if ($this->cacheProvider->contains($moduleName)) {
            $this->cacheProvider->delete($moduleName);
        }
    }

    public function getList(): array
    {
        $modules = [];
        $modulesDirsList = $this->finder->directories()
            ->in($this->modulePath)
            ->depth('== 0')
            ->exclude(['__MACOSX'])
            ->ignoreVCS(true);

        foreach ($modulesDirsList as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            if (!file_exists($this->modulePath . $moduleName . '/' . $moduleName . '.php')) {
                continue;
            }

            $modules[] = $this->getModule($moduleName);
        }

        return $this->mergeWithModulesFromHook($modules);
    }

    public function getInstalledModules(): array
    {
        return array_filter($this->getList(), function ($module) {
            return $module->database->get('installed') === 1;
        });
    }

    public function getConfigurableModules(): array
    {
        return array_filter($this->getList(), function (Module $module) {
            return $module->hasValidInstance() && !empty($module->getInstance()->warning);
        });
    }

    public function getUpgradableModules(): array
    {
        return array_filter($this->getList(), function (Module $module) {
            return $module->canBeUpgraded();
        });
    }

    /**
     * @return Module
     */
    public function getModule(string $moduleName): ModuleInterface
    {
        $path = $this->modulePath . '/' . $moduleName;
        $filePath = $path . '/' . $moduleName . '.php';

        $filemtime = (int) @filemtime($filePath);

        if ($this->cacheProvider->contains($moduleName)) {
            $module = $this->cacheProvider->fetch($moduleName);
            if ($module->disk->get('filemtime') === $filemtime) {
                return $module;
            }
        }

        $isValid = $filemtime > 0 && $this->moduleDataProvider->isModuleMainClassValid($moduleName);
        $attributes = $this->getModuleAttributes($moduleName, $isValid);
        $disk = $this->getModuleDiskAttributes($moduleName, $isValid, $filemtime);
        $database = $this->getModuleDatabaseAttributes($moduleName);

        $this->cacheProvider->save($moduleName, new Module($attributes, $disk, $database));

        return $this->cacheProvider->fetch($moduleName);
    }

    public function getModulePath(string $moduleName): ?string
    {
        $path = $this->modulePath . '/' . $moduleName;
        $filePath = $path . '/' . $moduleName . '.php';

        if (!is_dir($path) || !is_file($filePath)) {
            return null;
        }

        return $path;
    }

    public function generateActionUrls(ModuleCollection $collection): ModuleCollection
    {
        return $this->adminModuleDataProvider->generateActionUrls($collection);
    }

    private function getModuleAttributes(string $moduleName, bool $isValid): array
    {
        $attributes = ['name' => $moduleName];
        if ($isValid) {
            $tmpModule = ModuleLegacy::getInstanceByName($moduleName);
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
            'is_present' => $this->moduleDataProvider->isOnDisk($moduleName),
            'is_valid' => $isValid,
            'version' => $isValid ? ModuleLegacy::getInstanceByName($moduleName)->version : null,
            'path' => $path,
        ];
    }

    private function getModuleDatabaseAttributes(string $moduleName): array
    {
        return $this->moduleDataProvider->findByName($moduleName);
    }

    private function mergeWithModulesFromHook(array $modules): array
    {
        $externalModules = array_values($this->hookManager->exec('actionListModules', [], null, true));
        if (empty(reset($externalModules))) {
            return $modules;
        }

        foreach (array_merge(...$externalModules) as $externalModule) {
            $merged = false;
            foreach ($modules as $module) {
                if ($module->get('name') === $externalModule['name']) {
                    $module->attributes->add($externalModule);
                    $merged = true;
                    break;
                }
            }
            if (!$merged) {
                $modules[] = new Module($externalModule);
            }
        }

        return $modules;
    }
}
