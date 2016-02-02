<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Module;
use PrestaShopAutoload;

class ModulesOverrideInstallUninstallTest extends IntegrationTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        \PrestaShop\PrestaShop\Tests\Helper\Module::addModule('pscsx3241');
        \PrestaShop\PrestaShop\Tests\Helper\Module::addModule('pscsx32412');
    }

    public static function tearDownAfterClass()
    {
        Module::getInstanceByName('pscsx3241')->uninstall();
        Module::getInstanceByName('pscsx32412')->uninstall();

        \PrestaShop\PrestaShop\Tests\Helper\Module::removeModule('pscsx3241');
        \PrestaShop\PrestaShop\Tests\Helper\Module::removeModule('pscsx32412');
    }

    public function testInstall()
    {
        /**
         * Both modules install overrides in the same files.
         * This test only checks that modules are installed properly.
         */
        $pscsx3241 = array();
        $pscsx3241['pscsx3241'] = Module::getInstanceByName('pscsx3241');
        $pscsx3241['pscsx32412'] = Module::getInstanceByName('pscsx32412');
        foreach ($pscsx3241 as $name => $module) {
            $this->assertTrue((bool)$module->install(), "Could not install $name");
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
            $this->cleanup($actual_override_cart)
        );

        $this->assertEquals(
            $this->cleanup($expected_override_admin_product),
            $this->cleanup($actual_override_admin_product)
        );

        /** Then it checks that the overrides are removed once the modules are
         *  uninstalled.
         */

        $pscsx3241 = array();
        $pscsx3241[] = Module::getInstanceByName('pscsx3241');
        $pscsx3241[] = Module::getInstanceByName('pscsx32412');
        foreach ($pscsx3241 as $module) {
            $this->assertTrue((bool)$module->uninstall());
        }

        $this->assertFileNotExists($override_path_cart);
        $this->assertFileNotExists($override_path_admin_product_controller);
    }
}
