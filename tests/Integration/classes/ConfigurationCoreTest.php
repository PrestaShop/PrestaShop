<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\classes;

use Tests\TestCase\ReflexionHelper;
use Tests\TestCase\IntegrationTestCase;
use Configuration;

class ConfigurationCoreTest extends IntegrationTestCase
{
    public $default;

    protected function setUp()
    {
        parent::setUp();
        $id_shops = array(1, 2);
        $id_shop_groups = array(1, 2);

        Configuration::set('PS_TEST_NOT_OVERRIDDEN', 'RESULT_NOT_OVERRIDDEN', 0, 0);
        Configuration::set('PS_TEST_GROUP_OVERRIDDEN', 'RESULT_GROUP_OVERRIDDEN', 0, 0);

        foreach ($id_shop_groups as $id_group) {
            Configuration::set('PS_TEST_GROUP_OVERRIDDEN', 'RESULT_GROUP_OVERRIDDEN_'.$id_group, $id_group, 0);
        }

        Configuration::updateGlobalValue('PS_TEST_SHOP_OVERRIDDEN', 'RESULT_SHOP_OVERRIDDEN');
        foreach ($id_shops as $id_shop) {
            Configuration::set('PS_TEST_SHOP_OVERRIDDEN', 'RESULT_SHOP_OVERRIDDEN_'.$id_shop, 0, $id_shop);
        }

        Configuration::updateGlobalValue('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN');
        foreach ($id_shop_groups as $id_group) {
            Configuration::set('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN_GROUP_'.$id_group, $id_group, 0);
        }
        foreach ($id_shops as $id_shop) {
            Configuration::set('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN_SHOP_'.$id_shop, 0, $id_shop);
        }
    }

    public function teardown()
    {
    }

    public function testGetGlobalValue()
    {
        $this->assertEquals('RESULT_NOT_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_NOT_OVERRIDDEN'));
        $this->assertEquals('RESULT_GROUP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_OVERRIDDEN'));
        $this->assertEquals('RESULT_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_SHOP_OVERRIDDEN'));
        $this->assertEquals('RESULT_GROUP_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_SHOP_OVERRIDDEN'));
        $this->assertFalse(Configuration::getGlobalValue('PS_TEST_DOES_NOT_EXIST'));
    }
}
