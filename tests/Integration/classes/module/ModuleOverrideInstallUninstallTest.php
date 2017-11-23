<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration;


use Module;
use PrestaShopAutoload;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Tests\TestCase\IntegrationTestCase;
use Tests\TestCase\Module as TestingModule;

class ModuleOverrideInstallUninstallTest extends IntegrationTestCase
{
    public $moduleManagerBuilder;
    public $moduleManager;

    public $moduleNames;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        TestingModule::addModule('pscsx3241');
        TestingModule::addModule('pscsx32412');
    }

    protected function setUp()
    {
        parent::setUp();

        \ContextCore::getContext()->employee = new \Employee(1);
        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();

        $this->moduleNames= [
           'pscsx3241',
           'pscsx32412',
       ];
    }

    public static function tearDownAfterClass()
    {
        Module::getInstanceByName('pscsx3241')->uninstall();
        Module::getInstanceByName('pscsx32412')->uninstall();

        TestingModule::removeModule('pscsx3241');
        TestingModule::removeModule('pscsx32412');

        @unlink(_PS_ROOT_DIR_.'/override/controllers/admin/AdminProductsController.php');
        @unlink(_PS_ROOT_DIR_.'/override/classes/Cart.php');
    }

    public function testInstall()
    {
        /**
         * Both modules install overrides in the same files.
         * This test only checks that modules are installed properly.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool)$this->moduleManager->install($name), "Could not install $name");
        }
    }

    /**
     * Used to normalize the PHP source code for file comparison
     * and to strip dates that are inserted in comments when
     * overrides are installed.
     */
    private function cleanup($str)
    {
        $withoutDate        = preg_replace('#\* date: .*?\n#m', '', $str);
        $withoutBlankLines  = preg_replace('#\n?^(?:\s*)$#m', "", $withoutDate);
        return $withoutBlankLines;
    }

    public function testDiffOverrideAndUninstall()
    {
        /**
         * This tests first checks that the overrides installed in the previous step
         * resulted in the expected merged files.
         */

        $ressource_path = realpath(dirname(__FILE__).'/../../../resources/ModulesOverrideInstallUninstallTest/');
        $override_path_cart = _PS_ROOT_DIR_.'/'.PrestaShopAutoload::getInstance()->getClassPath('Cart');
        $override_path_admin_product_controller = _PS_ROOT_DIR_.'/'.PrestaShopAutoload::getInstance()->getClassPath('AdminProductsController');

        $actual_override_cart = file_get_contents($override_path_cart);
        $actual_override_admin_product = file_get_contents($override_path_admin_product_controller);
        $expected_override_cart = file_get_contents($ressource_path.'/Cart.php');
        $expected_override_admin_product = file_get_contents($ressource_path.'/AdminProductsController.php');

        $this->assertEquals(
            $this->cleanup($expected_override_cart),
            $this->cleanup($actual_override_cart),
            'Cart.php file different'
        );

        $this->assertEquals(
            $this->cleanup($expected_override_admin_product),
            $this->cleanup($actual_override_admin_product),
            'AdminProductsController.php file different'
        );

        /** Then it checks that the overrides are removed once the modules are
         *  uninstalled.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool)$this->moduleManager->uninstall($name), "Could not uninstall $name");
        }

        $this->assertFileNotExists($override_path_cart);
    }
}
