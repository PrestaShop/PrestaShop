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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $idShops = [1, 2];
        $idShopGroups = [1, 2];

        Configuration::set('PS_TEST_NOT_OVERRIDDEN', 'RESULT_NOT_OVERRIDDEN', 0, 0);
        Configuration::set('PS_TEST_GROUP_OVERRIDDEN', 'RESULT_GROUP_OVERRIDDEN', 0, 0);
        Configuration::updateGlobalValue('PS_TEST_SHOP_OVERRIDDEN', 'RESULT_SHOP_OVERRIDDEN');
        Configuration::updateGlobalValue('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN');

        foreach ($idShopGroups as $idShopGroup) {
            Configuration::set('PS_TEST_GROUP_OVERRIDDEN', 'RESULT_GROUP_OVERRIDDEN_' . $idShopGroup, $idShopGroup, 0);
            Configuration::set('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN_GROUP_' . $idShopGroup, $idShopGroup, 0);
        }
        foreach ($idShops as $idShop) {
            Configuration::set('PS_TEST_SHOP_OVERRIDDEN', 'RESULT_SHOP_OVERRIDDEN_' . $idShop, 0, $idShop);
            Configuration::set('PS_TEST_GROUP_SHOP_OVERRIDDEN', 'RESULT_GROUP_SHOP_OVERRIDDEN_SHOP_' . $idShop, 0, $idShop);
        }
    }

    public function testGetGlobalValue(): void
    {
        $this->assertEquals('RESULT_NOT_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_NOT_OVERRIDDEN'));
        $this->assertEquals('RESULT_GROUP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_OVERRIDDEN'));
        $this->assertEquals('RESULT_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_SHOP_OVERRIDDEN'));
        $this->assertEquals('RESULT_GROUP_SHOP_OVERRIDDEN', Configuration::getGlobalValue('PS_TEST_GROUP_SHOP_OVERRIDDEN'));
        $this->assertFalse(Configuration::getGlobalValue('PS_TEST_DOES_NOT_EXIST'));
    }
}
