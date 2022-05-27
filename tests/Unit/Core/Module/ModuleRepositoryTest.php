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

namespace Tests\Unit\Core\Module;

use Module as LegacyModule;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;

class ModuleRepositoryTest extends TestCase
{
    private const INSTALLED_MODULES = [
        'bankwire',
        'demo',
        'ps_banner',
    ];

    private const UPGRADABLE_MODULES = [
        'bankwire',
        'demo',
    ];

    private const CONFIGURABLE_MODULES = [
        'ps_banner',
    ];

    public function testGetList(): void
    {
        $this->assertCount(10, $this->getModuleRepository()->getList());
    }

    public function testGetInstalledModules(): void
    {
        $this->assertCount(count(self::INSTALLED_MODULES), $this->getModuleRepository()->getInstalledModules());
    }

    public function testGetUpgradableModules(): void
    {
        $this->assertCount(count(self::UPGRADABLE_MODULES), $this->getModuleRepository()->getUpgradableModules());
    }

    public function testGetMustBeConfiguredModules(): void
    {
        $this->assertCount(count(self::CONFIGURABLE_MODULES), $this->getModuleRepository()->getMustBeConfiguredModules());
    }

    public function testGetModulePath(): void
    {
        $moduleRepository = $this->getModuleRepository();

        $this->assertEquals(
           dirname(__DIR__, 3) . '/Resources/modules/bankwire',
           $moduleRepository->getModulePath('bankwire')
        );

        $this->assertNull($moduleRepository->getModulePath('no-existing-module'));
    }

    private function getModuleRepository(): ModuleRepository
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);
        $adminModuleDataProvider = $this->createMock(AdminModuleDataProvider::class);
        $cacheProvider = new DoctrineProvider(new ArrayAdapter());
        $hookManager = $this->createMock(HookManager::class);
        $modulePath = dirname(__DIR__, 3) . '/Resources/modules';

        $moduleRepository = new ModuleRepository(
            $moduleDataProvider,
            $adminModuleDataProvider,
            $cacheProvider,
            $hookManager,
            $modulePath
        );

        $reflection = new \ReflectionClass($moduleRepository);
        $property = $reflection->getProperty('storedSaticCall');
        $property->setAccessible(true);
        $property->setValue($moduleRepository,
            new class($this) {
                private $testClass;

                public function __construct($testClass)
                {
                    $this->testClass = $testClass;
                }

                public function getContextShopIdListCacheKey()
                {
                    return '1';
                }

                public function moduleCollectionCreateFrom(array $modules): ModuleCollection
                {
                    return ModuleCollection::createFrom($modules);
                }

                public function getModuleInstanceByName(string $moduleName)
                {
                    return $this->testClass->getModuleMock($moduleName);
                }

                public function newModule(array $attributes = [], array $disk = [], array $database = []): Module
                {
                    return $this->testClass->getModuleMock($attributes['name']);
                }
            }
        );

        return $moduleRepository;
    }

    public function getModuleMock(string $moduleName): Module
    {
        $module = $this->createMock(Module::class);
        $moduleInstance = $this->createMock(LegacyModule::class);
        $moduleInstance->warning = 'Configurable warning';

        $module->method('getInstance')->willReturn($moduleInstance);
        $module->method('isInstalled')->willReturn(in_array($moduleName, self::INSTALLED_MODULES));
        $module->method('isConfigurable')->willReturn(in_array($moduleName, self::CONFIGURABLE_MODULES));
        $module->method('canBeUpgraded')->willReturn(in_array($moduleName, self::UPGRADABLE_MODULES));
        $module->method('hasValidInstance')->willReturn(in_array($moduleName, self::CONFIGURABLE_MODULES));

        return $module;
    }
}
