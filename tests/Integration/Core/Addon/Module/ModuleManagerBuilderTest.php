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

        // Remove modules
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx3241')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx3241');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx32412')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx32412');
        }

        // Remove overrides
        @unlink(_PS_ROOT_DIR_ . '/override/controllers/admin/DummyAdminController.php');
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

        $overrideCart = _PS_ROOT_DIR_ . '/override/classes/Cart.php';
        $actual_override_cart = file_get_contents(_PS_ROOT_DIR_ . '/override/classes/Cart.php');
        $expected_override_cart = file_get_contents($resource_path . '/Cart.php');

        $actual_override_cart = $this->cleanup($actual_override_cart);
        $expected_override_cart = $this->cleanup($expected_override_cart);

        $this->assertEquals(
            $expected_override_cart,
            $actual_override_cart,
            'Cart.php file different'
        );

        $actual_override_admin_product = file_get_contents(_PS_ROOT_DIR_ . '/override/controllers/admin/DummyAdminController.php');
        $expected_override_admin_product = file_get_contents($resource_path . '/DummyAdminController.php');

        $actual_override_admin_product = $this->cleanup($actual_override_admin_product);
        $expected_override_admin_product = $this->cleanup($expected_override_admin_product);

        $this->assertEquals(
            $expected_override_admin_product,
            $actual_override_admin_product,
            'DummyAdminController.php file different'
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
