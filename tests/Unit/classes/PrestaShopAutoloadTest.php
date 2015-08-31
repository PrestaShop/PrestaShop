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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PHPUnit_Framework_TestCase;
use PrestaShopAutoload;

class    PrestaShopAutoloadTest extends PHPUnit_Framework_TestCase
{
    private $file_index_content = null;

    protected function setUp()
    {
        $this->file_index = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.PrestaShopAutoload::INDEX_FILE;
        unlink($this->file_index);
        PrestaShopAutoload::getInstance()->generateIndex();
        $this->file_index_content = md5(file_get_contents($this->file_index));
    }

    public function testGenerateIndex()
    {
        $this->assertTrue(file_exists($this->file_index));
        $data = include($this->file_index);
        $this->assertEquals($data['OrderControllerCore']['path'], 'controllers/front/OrderController.php');
    }

    public function testLoad()
    {
        PrestaShopAutoload::getInstance()->load('RequestSql');
        $this->assertTrue(class_exists('RequestSqlCore', false));
        $this->assertTrue(class_exists('RequestSql', false));
    }

    public function testClassLoadedFromCoreDir()
    {
        PrestaShopAutoload::getInstance()->load('Core_Business_Payment_PaymentOption');
        $this->assertTrue(class_exists('Core_Business_Payment_PaymentOption', false));
    }
}
