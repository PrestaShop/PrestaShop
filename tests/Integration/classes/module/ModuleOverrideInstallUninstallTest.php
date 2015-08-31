<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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
        $pscsx3241 = array();
        $pscsx3241['pscsx3241'] = Module::getInstanceByName('pscsx3241');
        $pscsx3241['pscsx32412'] = Module::getInstanceByName('pscsx32412');
        foreach ($pscsx3241 as $module) {
            if ($module->id) {
                $this->assertTrue((bool)$module->uninstall());
                $this->assertTrue((bool)$module->install());
            } else {
                $this->assertTrue((bool)$module->install());
            }
        }
    }

    public function testDiffOverrideAndUninstall()
    {
        $ressource_path = realpath(dirname(__FILE__).'/../../../resources/ModulesOverrideInstallUninstallTest/');
        $override_path_cart = _PS_ROOT_DIR_.'/'.PrestaShopAutoload::getInstance()->getClassPath('Cart');
        $override_path_admin_product_controller = _PS_ROOT_DIR_.'/'.PrestaShopAutoload::getInstance()->getClassPath('AdminProductsController');

        $new_override_cart = file_get_contents($override_path_cart);
        $new_override_admin_product = file_get_contents($override_path_admin_product_controller);
        $old_override_cart = file_get_contents($ressource_path.'/Cart.php');
        $old_override_admin_product = file_get_contents($ressource_path.'/AdminProductsController.php');

        $new_override_cart = preg_replace('~\* date: .*?\n~ism', '', $new_override_cart);
        $new_override_admin_product = preg_replace('~\* date: .*?\n~ism', '', $new_override_admin_product);
        $old_override_cart = preg_replace('~\* date: .*?\n~ism', '', $old_override_cart);
        $old_override_admin_product = preg_replace('~\* date: .*?\n~ism', '', $old_override_admin_product);

        $new_override_cart = preg_replace("#(^\s*$)#ism", "", $new_override_cart);
        $new_override_admin_product = preg_replace("#(^\s*$)#ism", "", $new_override_admin_product);
        $old_override_cart = preg_replace("#(^\s*$)#ism", "", $old_override_cart);
        $old_override_admin_product = preg_replace("#(^\s*$)#ism", "", $old_override_admin_product);

        $this->assertEquals($new_override_cart, $old_override_cart);
        $this->assertEquals($new_override_admin_product, $old_override_admin_product);
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
