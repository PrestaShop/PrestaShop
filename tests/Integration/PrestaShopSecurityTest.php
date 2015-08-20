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

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_Assert as Assert;
use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use PrestaShopAutoload;
use Tools;
use Db;
use Configuration;
use Module;
use Order;

class PrestaShopSecurityTest extends IntegrationTestCase
{
    protected static $prestafraud = null;

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testInstall()
    {
        if (!file_exists(_PS_MODULE_DIR_.'/prestafraud/prestafraud.php')) {
            $download = file_put_contents(_PS_CACHE_DIR_.'sandbox/prestafraud.zip', Tools::addonsRequest('module', array('id_module' => 4181)));
            $this->assertGreaterThan(20000, $download, 'Fail download module from Addons');
            $extract = Tools::ZipExtract(_PS_CACHE_DIR_.'sandbox/prestafraud.zip', _PS_MODULE_DIR_);
            $this->assertTrue($extract, 'Fail extract module');
            unlink(_PS_CACHE_DIR_.'sandbox/prestafraud.zip');
        }

        if (!file_exists(_PS_MODULE_DIR_.'/cheque/cheque.php')) {
            $download = file_put_contents(_PS_CACHE_DIR_.'sandbox/cheque.zip', Tools::addonsRequest('module', array('id_module' => 4181)));
            $this->assertGreaterThan(20000, $download, 'Fail download module from Addons');
            $extract = Tools::ZipExtract(_PS_CACHE_DIR_.'sandbox/cheque.zip', _PS_MODULE_DIR_);
            $this->assertTrue($extract, 'Fail extract module');
            unlink(_PS_CACHE_DIR_.'sandbox/cheque.zip');
        }

        self::$prestafraud = Module::getInstanceByName('prestafraud');
        $this->assertTrue(is_object(self::$prestafraud), 'Fail Module::getInstanceByName(\'prestafraud\')');
        $this->assertEquals('prestafraud', self::$prestafraud->name);
        if (!Module::isInstalled('prestafraud')) {
            $this->assertTrue((bool)self::$prestafraud->install());
        }

        if (!Module::isInstalled('cheque')) {
            $this->assertTrue((bool)Module::getInstanceByName('cheque')->install());
        }

        $uniqid = uniqid().time();
        $email = 'prestabot+'.$uniqid.'@gmail.com';
        $shop_url = 'http://www.prestashop-unit-test-'.$uniqid.'.com/';
        $result = self::$prestafraud->_createAccount($email, $shop_url);

        $this->assertTrue($result, implode(', ', self::$prestafraud->_errors));
    }

    public function testUninstall()
    {
        $this->assertTrue((bool)self::$prestafraud->uninstall());
    }
}
