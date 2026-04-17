<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
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
        $mockModuleDataProvider = $this->createMock(ModuleDataProvider::class);
        $mockModuleDataProvider->method('can')->willReturn(true);

        $this->moduleRepository = $this->getMockBuilder(ModuleRepository::class)
            ->setConstructorArgs([
                $mockModuleDataProvider,
                $this->createMock(AdminModuleDataProvider::class),
                $this->createMock(CacheProvider::class),
                $this->createMock(HookManager::class),
                dirname(__DIR__, 3) . '/Resources/modules',
                $this->createMock(LanguageContext::class),
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
