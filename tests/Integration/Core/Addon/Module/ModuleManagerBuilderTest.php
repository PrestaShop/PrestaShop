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

namespace Tests\Integration\Core\Addon\Module;

use Context;
use Employee;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Tests\Resources\ResourceResetter;
use Tools;

/**
 * These tests install and uninstalls modules causing the cache to be cleared. So it's better to run it isolated.
 *
 * @group isolatedProcess
 */
class ModuleManagerBuilderTest extends TestCase
{
    /**
     * @var ModuleManagerBuilder
     */
    public $moduleManagerBuilder;
    /**
     * @var ModuleManager
     */
    public $moduleManager;
    /**
     * @var string[]
     */
    public $moduleNames;
    /**
     * @var string[]
     */
    public $conflictModuleNames;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dirResources = dirname(__DIR__, 4);

        if (is_dir($dirResources . '/Resources/modules_tests/pscsx3241')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/pscsx3241', _PS_MODULE_DIR_ . '/pscsx3241');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/pscsx32412')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/pscsx32412', _PS_MODULE_DIR_ . '/pscsx32412');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testconflict', _PS_MODULE_DIR_ . '/testconflict');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testtrickyconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testtrickyconflict', _PS_MODULE_DIR_ . '/testtrickyconflict');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testpropertyconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testpropertyconflict', _PS_MODULE_DIR_ . '/testpropertyconflict');
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        // Uninstall modules
        if (Module::isInstalled('pscsx3241')) {
            Module::getInstanceByName('pscsx3241')->uninstall();
        }
        if (Module::isInstalled('pscsx32412')) {
            Module::getInstanceByName('pscsx32412')->uninstall();
        }
        if (Module::isInstalled('testconflict')) {
            Module::getInstanceByName('testconflict')->uninstall();
        }
        if (Module::isInstalled('testtrickyconflict')) {
            Module::getInstanceByName('testtrickyconflict')->uninstall();
        }
        if (Module::isInstalled('testpropertyconflict')) {
            Module::getInstanceByName('testpropertyconflict')->uninstall();
        }

        // Remove modules
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx3241')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx3241');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx32412')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx32412');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testconflict');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testtrickyconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testtrickyconflict');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testpropertyconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testpropertyconflict');
        }

        // Remove overrides
        @unlink(_PS_ROOT_DIR_ . '/override/controllers/admin/AdminProductsController.php');
        @unlink(_PS_ROOT_DIR_ . '/override/classes/Cart.php');

        // Reset modules folder
        (new ResourceResetter())->resetTestModules();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Context::getContext()->employee = new Employee(1);

        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();

        $this->moduleNames = [
            'pscsx32412',
            'pscsx3241',
        ];

        $this->conflictModuleNames = ['testbasicconflict', 'testtrickyconflict', 'testpropertyconflict'];
    }

    public function testInstall(): void
    {
        /*
         * Both modules install overrides in the same files.
         * This test only checks that modules are installed properly.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool) $this->moduleManager->install($name));
        }

        /**
         * This tests first checks that the overrides installed in the previous step
         * resulted in the expected merged files.
         */
        $resource_path = dirname(__DIR__, 4) . '/Resources/modules_tests/override/';

        $actual_override_cart = $this->cleanup(file_get_contents(_PS_ROOT_DIR_ . '/override/classes/Cart.php'));
        $expected_override_cart = $this->cleanup(file_get_contents($resource_path . 'classes/Cart.php'));

        $this->assertEquals($expected_override_cart, $actual_override_cart);

        $actual_override_admin_product = $this->cleanup(file_get_contents(_PS_ROOT_DIR_ . '/override/controllers/admin/AdminProductsController.php'));
        $expected_override_admin_product = $this->cleanup(file_get_contents($resource_path . '/controllers/admin/AdminProductsController.php'));

        $this->assertEquals(
            $actual_override_admin_product,
            $expected_override_admin_product,
            'AdminProductsController.php file different'
        );

        // Then it checks that the overrides are removed once the modules are uninstalled.
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool) $this->moduleManager->uninstall($name));
        }

        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist($actual_override_cart);
            $this->assertFileDoesNotExist($actual_override_admin_product);
        } else {
            $this->assertFileNotExists($actual_override_cart);
            $this->assertFileNotExists($actual_override_admin_product);
        }
    }

    public function testOverrideConflictAtInstall(): void
    {
        $this->moduleManager->install($this->moduleNames[1]);

        /*
         * this will test that install fails when module has a conflicting override,
         * using test modules "testbasicconflict" and "testtrickyconflict", tricky conflict
         * adds several spaces in function definition (it must still be detected as a conflicting method)
         */
        foreach ($this->conflictModuleNames as $name) {
            $this->assertFalse($this->moduleManager->install($name), 'override conflict test on module ' . $name . ' failed');
        }
    }

    /**
     * Used to normalize the PHP source code for file comparison
     * and to strip dates that are inserted in comments when
     * overrides are installed.
     */
    private function cleanup(string $str): string
    {
        $withoutDate = preg_replace('#\* date: .*?\n#m', '', $str);

        return preg_replace('#\n?^(?:\s*)$#m', '', $withoutDate);
    }
}
