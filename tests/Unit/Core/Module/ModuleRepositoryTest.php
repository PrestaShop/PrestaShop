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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module;

use Doctrine\Common\Cache\CacheProvider;
use Module as LegacyModule;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;

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

    private const ACTIVE_MODULES = [
        'bankwire',
    ];

    private const CONFIGURABLE_MODULES = [
        'ps_banner',
        'bankwire',
    ];

    private const MUST_BE_CONFIGURED_MODULES = [
        'bankwire',
    ];

    /** @var ModuleRepository */
    private $moduleRepository;

    public function setUp(): void
    {
        $this->moduleRepository = $this->getMockBuilder(ModuleRepository::class)
            ->setConstructorArgs([
                $this->createMock(ModuleDataProvider::class),
                $this->createMock(AdminModuleDataProvider::class),
                $this->createMock(CacheProvider::class),
                $this->createMock(HookManager::class),
                dirname(__DIR__, 3) . '/Resources/modules',
                1,
            ])
            ->onlyMethods(['getModule'])
            ->getMock()
        ;
        $this->moduleRepository->method('getModule')->willReturnCallback([$this, 'getModuleMock']);
    }

    public function testGetList(): void
    {
        $this->assertCount(10, $this->moduleRepository->getList());
    }

    public function testGetInstalledModules(): void
    {
        $this->assertCount(count(self::INSTALLED_MODULES), $this->moduleRepository->getInstalledModules());
    }

    public function testGetUpgradableModules(): void
    {
        $this->assertCount(count(self::UPGRADABLE_MODULES), $this->moduleRepository->getUpgradableModules());
    }

    public function testGetMustBeConfiguredModules(): void
    {
        $this->assertCount(count(self::MUST_BE_CONFIGURED_MODULES), $this->moduleRepository->getMustBeConfiguredModules());
    }

    public function testGetModulePath(): void
    {
        $this->assertEquals(
           dirname(__DIR__, 3) . '/Resources/modules/bankwire',
            $this->moduleRepository->getModulePath('bankwire')
        );
        $this->assertNull($this->moduleRepository->getModulePath('no-existing-module'));
    }

    public function getModuleMock(string $moduleName): Module
    {
        $module = $this->createMock(Module::class);
        $moduleInstance = $this->createMock(LegacyModule::class);
        $moduleInstance->warning = 'Configurable warning';

        $module->method('getInstance')->willReturn($moduleInstance);
        $module->method('isInstalled')->willReturn(in_array($moduleName, self::INSTALLED_MODULES));
        $module->method('isActive')->willReturn(in_array($moduleName, self::ACTIVE_MODULES));
        $module->method('isConfigurable')->willReturn(in_array($moduleName, self::CONFIGURABLE_MODULES));
        $module->method('canBeUpgraded')->willReturn(in_array($moduleName, self::UPGRADABLE_MODULES));
        $module->method('hasValidInstance')->willReturn(in_array($moduleName, self::CONFIGURABLE_MODULES));

        return $module;
    }
}
