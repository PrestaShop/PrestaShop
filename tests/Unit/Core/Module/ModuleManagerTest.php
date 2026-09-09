<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module;

use Exception;
use Module as LegacyModule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleManagerTest extends TestCase
{
    public const INSTALLED_MODULE_NAME = 'installed';
    public const UNINSTALLED_MODULE_NAME = 'uninstalled';
    public const INSTALLED_THEN_UNINSTALLED_MODULE_NAME = 'installed_uninstalled';

    /** @var ModuleManager */
    private $moduleManager;

    /** @var Module&MockObject */
    private $module;

    /** @var LegacyModule&MockObject */
    private $legacyModule;

    public function setUp(): void
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturnArgument(0);

        $adminModuleDataProvider = $this->createMock(AdminModuleDataProvider::class);
        $adminModuleDataProvider->method('isAllowedAccess')->willReturn(true);

        $this->module = $this->getModuleMock();
        $this->moduleManager = $this->createModuleManagerFor($this->module);
    }

    /**
     * Builds a ModuleManager whose repository serves the given module, so a test can install a module
     * that fails without disturbing the shared one every other test relies on.
     */
    private function createModuleManagerFor(Module $module): ModuleManager
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturnArgument(0);

        $adminModuleDataProvider = $this->createMock(AdminModuleDataProvider::class);
        $adminModuleDataProvider->method('isAllowedAccess')->willReturn(true);

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getModule')->willReturn($module);

        $moduleManager = $this->getMockBuilder(ModuleManager::class)
            ->setConstructorArgs([
                $moduleRepository,
                $this->getModuleDataProviderMock(),
                $adminModuleDataProvider,
                $this->createMock(SourceHandlerFactory::class),
                $translatorMock,
                $this->createMock(EventDispatcherInterface::class),
                $this->createMock(HookManager::class),
                _PS_MODULE_DIR_,
                new XliffFileLoader(),
                null,
            ])
            ->onlyMethods(['upgradeMigration'])
            ->getMock()
        ;
        $moduleManager->method('upgradeMigration')->willReturn(true);

        return $moduleManager;
    }

    public function testInstall(): void
    {
        $this->assertTrue($this->moduleManager->install(self::INSTALLED_MODULE_NAME));
        $this->assertTrue($this->moduleManager->install(self::UNINSTALLED_MODULE_NAME));
    }

    /**
     * A module that reports failure must not stay registered: leaving the registration behind made the
     * next attempt match the isInstalled() branch of install(), which answers with upgrade() and
     * reports success without ever running the module's install() again.
     */
    public function testAnInstallationThatReturnsFalseIsRolledBack(): void
    {
        [$module, $legacyModule] = $this->getFailingModuleMock(null);
        $legacyModule->expects($this->once())->method('uninstallCoreRegistration');

        $moduleManager = $this->createModuleManagerFor($module);

        $this->assertFalse($moduleManager->install(self::UNINSTALLED_MODULE_NAME));
    }

    /**
     * A module's install() can fail by throwing rather than by returning false - registerHook() does
     * that for a hook the module never implements. The registration must be undone in that case too,
     * and the error must still reach the caller.
     */
    public function testAnInstallationThatThrowsIsRolledBackAndKeepsTheError(): void
    {
        [$module, $legacyModule] = $this->getFailingModuleMock(new Exception('hook has no method'));
        $legacyModule->expects($this->once())->method('uninstallCoreRegistration');

        $moduleManager = $this->createModuleManagerFor($module);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('hook has no method');
        $moduleManager->install(self::UNINSTALLED_MODULE_NAME);
    }

    /**
     * The rollback deletes rows, so it must be unreachable for an installation that worked.
     */
    public function testASuccessfulInstallationIsNotRolledBack(): void
    {
        $this->legacyModule->expects($this->never())->method('uninstallCoreRegistration');

        $this->assertTrue($this->moduleManager->install(self::UNINSTALLED_MODULE_NAME));
    }

    public function testUninstall(): void
    {
        $this->assertTrue($this->moduleManager->uninstall(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The module %module% must be installed first');
        $this->moduleManager->uninstall(self::UNINSTALLED_MODULE_NAME);
    }

    public function testEnable(): void
    {
        $this->assertTrue($this->moduleManager->enable(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The module %module% must be installed first');
        $this->moduleManager->enable(self::UNINSTALLED_MODULE_NAME);
    }

    public function testDisable(): void
    {
        $this->assertTrue($this->moduleManager->disable(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The module %module% must be installed first');
        $this->moduleManager->disable(self::UNINSTALLED_MODULE_NAME);
    }

    public function testUpgrade(): void
    {
        $this->module->method('get')->with('version')->willReturn('1.0.0');
        $this->assertTrue($this->moduleManager->upgrade(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The module %module% must be installed first');
        $this->moduleManager->upgrade(self::UNINSTALLED_MODULE_NAME);
    }

    public function testReset(): void
    {
        $this->module->expects($this->once())->method('onReset');
        $this->module->expects($this->once())->method('onInstall');
        $this->module->expects($this->once())->method('onUninstall');
        $this->assertTrue($this->moduleManager->reset(self::INSTALLED_THEN_UNINSTALLED_MODULE_NAME, false));
        $this->assertTrue($this->moduleManager->reset(self::INSTALLED_MODULE_NAME, true));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The module %module% must be installed first');
        $this->moduleManager->reset(self::UNINSTALLED_MODULE_NAME);
    }

    public function testPostInstall(): void
    {
        $this->assertTrue($this->moduleManager->postInstall(self::INSTALLED_MODULE_NAME));
        $this->assertFalse($this->moduleManager->postInstall(self::UNINSTALLED_MODULE_NAME));
    }

    public function testIsEnabled(): void
    {
        $this->assertTrue($this->moduleManager->isEnabled(self::INSTALLED_MODULE_NAME));
        $this->assertFalse($this->moduleManager->isEnabled(self::UNINSTALLED_MODULE_NAME));
    }

    public function testIsInstalled(): void
    {
        $this->assertTrue($this->moduleManager->isInstalled(self::INSTALLED_MODULE_NAME));
        $this->assertFalse($this->moduleManager->isInstalled(self::UNINSTALLED_MODULE_NAME));
    }

    public function testGetError(): void
    {
        $this->legacyModule->method('getErrors')->willReturnOnConsecutiveCalls([], ['my error']);
        $this->module->method('hasValidInstance')->willReturnOnConsecutiveCalls(false, true, true);

        $this->assertEquals(
            'The module %module% is invalid and cannot be loaded.',
            $this->moduleManager->getError(self::INSTALLED_MODULE_NAME)
        );

        $this->assertEquals(
            'Unfortunately, the module %module% did not return additional details.',
            $this->moduleManager->getError(self::INSTALLED_MODULE_NAME)
        );

        $this->assertEquals(
            'my error',
            $this->moduleManager->getError(self::INSTALLED_MODULE_NAME)
        );
    }

    /**
     * @return Module&MockObject
     */
    private function getModuleMock(): Module
    {
        /** @var Module&MockObject $module */
        $module = $this->getMockBuilder(Module::class)
            ->disableOriginalConstructor()
            ->enableOriginalClone()
            ->getMock()
        ;

        $this->legacyModule = $this->getMockBuilder(LegacyModule::class)
            ->disableOriginalConstructor()
            ->enableOriginalClone()
            ->addMethods(['reset'])
            ->onlyMethods(['getErrors', 'uninstallOverrides', 'uninstallCoreRegistration'])
            ->getMock()
        ;
        $this->legacyModule->method('reset')->willReturn(true);

        $module->method('get')->with('version')->willReturn('1.0.0');
        $module->method('onInstall')->willReturn(true);
        $module->method('onUninstall')->willReturn(true);
        $module->method('onEnable')->willReturn(true);
        $module->method('onDisable')->willReturn(true);
        $module->method('onUpgrade')->willReturn(true);
        $module->method('onReset')->willReturn(true);
        $module->method('onPostInstall')->willReturn(true);
        $module->method('getInstance')->willReturn($this->legacyModule);

        return $module;
    }

    /**
     * @param Exception|null $throwable what the module's install() does instead of succeeding:
     *                                  throw it, or return false when null
     *
     * @return array{0: Module&MockObject, 1: LegacyModule&MockObject}
     */
    private function getFailingModuleMock(?Exception $throwable): array
    {
        /** @var Module&MockObject $module */
        $module = $this->getMockBuilder(Module::class)
            ->disableOriginalConstructor()
            ->enableOriginalClone()
            ->getMock()
        ;

        /** @var LegacyModule&MockObject $legacyModule */
        $legacyModule = $this->getMockBuilder(LegacyModule::class)
            ->disableOriginalConstructor()
            ->enableOriginalClone()
            ->onlyMethods(['getErrors', 'uninstallOverrides', 'uninstallCoreRegistration'])
            ->getMock()
        ;

        $module->method('get')->with('version')->willReturn('1.0.0');
        $module->method('getInstance')->willReturn($legacyModule);
        if ($throwable === null) {
            $module->method('onInstall')->willReturn(false);
        } else {
            $module->method('onInstall')->willThrowException($throwable);
        }

        return [$module, $legacyModule];
    }

    private function getModuleDataProviderMock(): ModuleDataProvider
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);

        // When you reset a module, there is 2 ways: using the reset function or using uninstall then install.
        // With the second way, we need to be sure that the module is installed before calling uninstall and uninstalled
        // before calling install. This callback returns true twice and then false to simulate the expected behavior
        // for the `INSTALLED_THEN_UNINSTALLED_MODULE_NAME` module name
        $isInstalledCallback = new class() {
            private $count = 0;

            public function isInstalled($name)
            {
                return $name === ModuleManagerTest::INSTALLED_MODULE_NAME
                    || ($name === ModuleManagerTest::INSTALLED_THEN_UNINSTALLED_MODULE_NAME
                        && ++$this->count < 3);
            }
        };

        $moduleDataProvider->method('isInstalled')
            ->willReturnCallback([$isInstalledCallback, 'isInstalled'])
        ;

        $moduleDataProvider->method('isEnabled')
            ->willReturnMap([
                [self::INSTALLED_MODULE_NAME, true],
                [self::UNINSTALLED_MODULE_NAME, false],
            ])
        ;

        $moduleDataProvider->method('getModuleIdByName')
            ->willReturnMap([
                [self::INSTALLED_MODULE_NAME, false, 1],
                [self::UNINSTALLED_MODULE_NAME, false, null],
            ])
        ;

        $moduleDataProvider->method('isOnDisk')->willReturn(true);

        return $moduleDataProvider;
    }
}
