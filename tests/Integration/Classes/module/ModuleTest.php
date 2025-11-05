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

namespace Tests\Integration\Classes\module;

use Cache;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * These tests install and uninstalls modules causing the cache to be cleared. So it's better to run it isolated.
 *
 * @group isolatedProcess
 */
class ModuleTest extends TestCase
{
    use ContextMockerTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
    }

    /**
     * @return array a list of modules to control override features
     */
    public function providerModulesOnDisk(): array
    {
        return [
            ['bankwire'],
            ['cronjobs'],
            ['ganalytics'],
            ['ps_emailsubscription'],
            ['ps_featuredproducts'],
        ];
    }

    /**
     * Check if html in trans is escaped when the _raw parameter is used
     *
     * @dataProvider providerModulesOnDisk
     *
     * @param string $moduleName the module name
     */
    public function testTrans(string $moduleName): void
    {
        $module = Module::getInstanceByName($moduleName);
        $transMethod = new \ReflectionMethod($module, 'trans');
        $transMethod->setAccessible(true);
        $trans = $transMethod->invoke($module, '<a href="test">%d Succesful deletion "%s"</a>', ['_raw' => true, 10, '<b>stringTest</b>'], 'Admin.Notifications.Success');
        $this->assertEquals('<a href="test">10 Succesful deletion "<b>stringTest</b>"</a>', $trans);

        $trans = $transMethod->invoke($module, '<a href="test">%d Succesful deletion "%s"</a>', [10, '<b>stringTest</b>'], 'Admin.Notifications.Success');
        $this->assertEquals('&lt;a href="test"&gt;10 Succesful deletion "&lt;b&gt;stringTest&lt;/b&gt;"&lt;/a&gt;', $trans);
    }

    /**
     * @dataProvider providerModulesOnDisk
     * Note: improves module list fixtures in order to cancel any override.
     *
     * @param string $moduleName the module name
     */
    public function testDummyGetOverride(string $moduleName): void
    {
        $module = Module::getInstanceByName($moduleName);

        $this->assertNotFalse($module);
        $this->assertInstanceOf(Module::class, $module);
        $this->assertEmpty($module->getOverrides());
    }

    public function testRealOverrideInModuleDir(): void
    {
        HelperModule::addModule('pscsx3241');
        $module = Module::getInstanceByName('pscsx3241');
        $overrides = $module->getOverrides();

        $this->assertContains('Cart', $overrides);
        $this->assertContains('AdminProductsController', $overrides);
        $this->assertCount(2, $overrides);

        HelperModule::removeModule('pscsx3241');
    }

    /**
     * Test if a module return the good possible hooks list.
     * This test is done on the bankwire generic module.
     *
     * Note: improves module list fixtures in order to get an explicit list of hooks.
     */
    public function testGetRightListForModule(): void
    {
        ModuleManagerBuilder::getInstance()->build()->install('bankwire');
        $module = Module::getInstanceByName('bankwire');
        Cache::clean('hook_alias');
        $possibleHooksList = $module->getPossibleHooksList();

        $this->assertCount(3, $possibleHooksList);

        $this->assertEquals('displayHome', $possibleHooksList[0]['name']);
        $this->assertEquals('displayPaymentReturn', $possibleHooksList[1]['name']);
        $this->assertEquals('paymentOptions', $possibleHooksList[2]['name']);

        Module::getInstanceByName('bankwire')->uninstall();
    }

    /**
     * Test that _clearCache correctly handles templates with "module:" prefix.
     * Templates using "module:" syntax should be passed directly to Smarty
     * without transformation via getTemplatePath() to avoid returning null
     * which would trigger clearAllCache().
     *
     * @dataProvider templatePathProvider
     */
    public function testClearCacheHandlesModuleTemplatesCorrectly(string $template, bool $shouldTransform): void
    {
        $module = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getTemplatePath', 'getDefaultCompileId'])
            ->getMock();

        $module->method('getDefaultCompileId')
            ->willReturn('default');

        if ($shouldTransform) {
            $module->expects($this->once())
                ->method('getTemplatePath')
                ->with($template)
                ->willReturn('/resolved/path/template.tpl');
        } else {
            $module->expects($this->never())
                ->method('getTemplatePath');
        }

        $reflection = new ReflectionClass($module);
        $method = $reflection->getMethod('_clearCache');
        $method->setAccessible(true);

        $batchModeProperty = $reflection->getProperty('_batch_mode');
        $batchModeProperty->setAccessible(true);
        $batchModeProperty->setValue($module, true);

        $result = $method->invoke($module, $template);

        $this->assertEquals(0, $result);

        $batchModeProperty->setValue($module, false);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public function templatePathProvider(): array
    {
        return [
            'module template should not transform' => [
                'module:ps_currencyselector/ps_currencyselector.tpl',
                false,
            ],
            'module template with subpath' => [
                'module:ps_socialfollow/views/templates/hook/socialfollow.tpl',
                false,
            ],
            'regular template should transform' => [
                'views/templates/hook/template.tpl',
                true,
            ],
        ];
    }
}

define('_RESSOURCE_MODULE_DIR_', realpath(dirname(__FILE__, 4) . '/Resources/modules_tests/'));

class HelperModule
{
    /**
     * Copy the directory in resources which get the name $module_dir_name in the module directory
     *
     * @param string $module_dir_name take the directory name of a module contain in /home/prestashop/tests/resources/module
     */
    public static function addModule(string $module_dir_name): bool
    {
        if (is_dir(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseCopy(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name, _PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Delete the directory in /home/prestashop/module which get the name $module_dir_name
     *
     * @param string $module_dir_name take the directory name of a module contain in /home/prestashop/module
     */
    public static function removeModule(string $module_dir_name): bool
    {
        if (is_dir(_PS_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseDelete(_PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Recursivly copy a directory
     *
     * @param string $src the source path (eg. /home/dir/to/copy)
     * @param string $dst the destination path (eg. /home/)
     */
    private static function recurseCopy(string $src, string $dst): void
    {
        $dirp = opendir($src);
        @mkdir($dst);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
    }

    /**
     * Recursivly delete a directory
     *
     * @param string $dir the directory to delete path (eg. /home/dir/to/delete)
     */
    private static function recurseDelete(string $dir): void
    {
        $dirp = opendir($dir);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . '/' . $file)) {
                    self::recurseDelete($dir . '/' . $file);
                } else {
                    unlink($dir . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
        rmdir($dir);
    }
}
