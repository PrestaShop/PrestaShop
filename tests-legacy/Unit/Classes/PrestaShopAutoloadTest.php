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

namespace LegacyTests\Unit\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use PrestaShopAutoload;

class PrestaShopAutoloadTest extends TestCase
{
    private $file_index_content = null;
    private $file_index = null;

    protected function setUp()
    {
        $this->file_index = PrestaShopAutoload::getCacheFileIndex();
        PrestaShopAutoload::getInstance()->generateIndex();
        $this->file_index_content = md5(file_get_contents($this->file_index));
    }

    public function testGenerateIndex()
    {
        $this->assertFileExists($this->file_index);
        $data = include $this->file_index;
        $this->assertEquals($data['OrderControllerCore']['path'], 'controllers/front/OrderController.php');
    }

    public function testLoad()
    {
        PrestaShopAutoload::getInstance()->load('RequestSql');
        $this->assertTrue(class_exists('RequestSqlCore', false));
        $this->assertTrue(class_exists('RequestSql', false));
    }

    /**
     * Given PS_DISABLE_OVERRIDES is enabled
     * When the class index is regenerated and we have override
     * Then the override shouldn't be include in the class index
     */
    public function testGenerateIndexWithoutOverride()
    {
        Configuration::updateGlobalValue('PS_DISABLE_OVERRIDES', 1);
        @mkdir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override/classes/', 0777, true);
        define('_PS_HOST_MODE_', 1);
        file_put_contents(
            _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override/classes/Connection.php',
            '<?php 
            class Connection extends ConnectionCore {
        }'
        );
        PrestaShopAutoload::getInstance()->generateIndex();
        $this->assertFileExists($this->file_index);
        $data = include $this->file_index;
        $this->assertEquals($data['OrderControllerCore']['path'], 'controllers/front/OrderController.php');
        $this->assertEquals($data['Connection']['override'], false);
        Configuration::updateGlobalValue('PS_DISABLE_OVERRIDES', 0);
        PrestaShopAutoload::getInstance()->generateIndex();
        $data = include $this->file_index;
        $this->assertEquals($data['Connection']['override'], true);
    }

    public function testClassFromCoreDirShouldntBeLoaded()
    {
        PrestaShopAutoload::getInstance()->load('\\PrestaShop\\PrestaShop\\Core\\Payment\\PaymentOption');

        $this->assertFalse(class_exists('\\PrestaShop\\PrestaShop\\Core\\Payment\\PaymentOption', false));
    }

    public static function tearDownAfterClass()
    {
        @unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override/classes/Connection.php');
    }
}
