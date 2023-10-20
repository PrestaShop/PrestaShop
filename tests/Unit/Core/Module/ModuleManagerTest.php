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

    public function setUp(): void
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturnArgument(0);

        $adminModuleDataProvider = $this->createMock(AdminModuleDataProvider::class);
        $adminModuleDataProvider->method('isAllowedAccess')->willReturn(true);

        $this->module = $this->getModuleMock();

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getModule')->willReturn($this->module);

        $this->moduleManager = $this->getMockBuilder(ModuleManager::class)
            ->setConstructorArgs([
                $moduleRepository,
                $this->getModuleDataProviderMock(),
                $adminModuleDataProvider,
                $this->createMock(SourceHandlerFactory::class),
                $translatorMock,
                $this->createMock(EventDispatcherInterface::class),
                $this->createMock(HookManager::class),
            ])
            ->onlyMethods(['upgradeMigration'])
            ->getMock()
        ;
        $this->moduleManager->method('upgradeMigration')->willReturn(true);
    }

    public function testInstall(): void
    {
        $this->assertTrue($this->moduleManager->install(self::INSTALLED_MODULE_NAME));
        $this->assertTrue($this->moduleManager->install(self::UNINSTALLED_MODULE_NAME));
    }

    public function testUninstall(): void
    {
        $this->assertTrue($this->moduleManager->uninstall(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
        $this->moduleManager->uninstall(self::UNINSTALLED_MODULE_NAME);
    }

    public function testEnable(): void
    {
        $this->assertTrue($this->moduleManager->enable(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
        $this->moduleManager->enable(self::UNINSTALLED_MODULE_NAME);
    }

    public function testDisable(): void
    {
        $this->assertTrue($this->moduleManager->disable(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
        $this->moduleManager->disable(self::UNINSTALLED_MODULE_NAME);
    }

    public function testEnableMobile(): void
    {
        $this->assertTrue($this->moduleManager->enableMobile(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
        $this->moduleManager->enableMobile(self::UNINSTALLED_MODULE_NAME);
    }

    public function testDisableMobile(): void
    {
        $this->assertTrue($this->moduleManager->disableMobile(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
        $this->moduleManager->disableMobile(self::UNINSTALLED_MODULE_NAME);
    }

    public function testUpgrade(): void
    {
        $this->module->method('get')->with('version')->willReturn('1.0.0');
        $this->assertTrue($this->moduleManager->upgrade(self::INSTALLED_MODULE_NAME));

        $this->expectException(Exception::class);
        $this->expectErrorMessage('The module %module% must be installed first');
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
        $this->expectErrorMessage('The module %module% must be installed first');
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
        $moduleInstance = $this->createMock(LegacyModule::class);
        $moduleInstance->method('getErrors')->willReturnOnConsecutiveCalls([], ['my error']);
        $this->module->method('getInstance')->willReturn($moduleInstance);
        $this->module->method('hasValidInstance')->willReturnOnConsecutiveCalls(false, true, true);

        $this->assertEquals(
            'The module is invalid and cannot be loaded.',
            $this->moduleManager->getError(self::INSTALLED_MODULE_NAME)
        );

        $this->assertEquals(
            'Unfortunately, the module did not return additional details.',
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
            ->setMethodsExcept([])
            ->addMethods(['reset', 'postInstall'])
            ->getMock()
        ;

        $module->method('get')->with('version')->willReturn('1.0.0');
        $module->method('onInstall')->willReturn(true);
        $module->method('onUninstall')->willReturn(true);
        $module->method('onEnable')->willReturn(true);
        $module->method('onDisable')->willReturn(true);
        $module->method('onMobileEnable')->willReturn(true);
        $module->method('onMobileDisable')->willReturn(true);
        $module->method('onUpgrade')->willReturn(true);
        $module->method('onReset')->willReturn(true);
        $module->method('onPostInstall')->willReturn(true);
        $module->method('reset')->willReturn(true);
        $module->method('postInstall')->willReturn(true);

        return $module;
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
