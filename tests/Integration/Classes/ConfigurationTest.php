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

namespace Tests\Integration\Classes;

use Configuration;
use Db;
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

    /**
     * Test that updateValue() skips DB operations when value is unchanged
     * This is a performance optimization to avoid unnecessary writes and cache invalidations
     */
    public function testUpdateValueSkipsDbWriteWhenValueUnchanged(): void
    {
        $testKey = 'PS_TEST_PERF_UNCHANGED_VALUE';
        $testValue = 'initial_value_' . time();

        // Initial insert
        $result = Configuration::updateValue($testKey, $testValue);
        $this->assertTrue($result);
        $this->assertEquals($testValue, Configuration::get($testKey));

        // Get initial date_upd to verify DB is not written
        $initialDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );

        // Wait 1 second to ensure date_upd would change if DB was written
        sleep(1);

        // Update with same value - should return true but not write to DB
        $result = Configuration::updateValue($testKey, $testValue);
        $this->assertTrue($result);
        $this->assertEquals($testValue, Configuration::get($testKey));

        // Verify date_upd was not changed (no DB write occurred)
        $newDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );
        $this->assertEquals(
            $initialDateUpd,
            $newDateUpd,
            'date_upd should not change when value is unchanged - DB write should be skipped'
        );

        // Cleanup
        Configuration::deleteByName($testKey);
    }

    /**
     * Test that updateValue() performs DB write when value changes
     */
    public function testUpdateValueWritesDbWhenValueChanges(): void
    {
        $testKey = 'PS_TEST_PERF_CHANGED_VALUE';
        $initialValue = 'initial_value_' . time();
        $newValue = 'new_value_' . time();

        // Initial insert
        Configuration::updateValue($testKey, $initialValue);
        $initialDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );

        // Wait to ensure date_upd will be different
        sleep(1);

        // Update with different value - should write to DB
        $result = Configuration::updateValue($testKey, $newValue);
        $this->assertTrue($result);
        $this->assertEquals($newValue, Configuration::get($testKey));

        // Verify date_upd was changed (DB write occurred)
        $newDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );
        $this->assertNotEquals(
            $initialDateUpd,
            $newDateUpd,
            'date_upd should change when value changes - DB write should occur'
        );

        // Cleanup
        Configuration::deleteByName($testKey);
    }

    /**
     * Test that numeric comparison works correctly (== vs ===)
     */
    public function testUpdateValueNumericComparison(): void
    {
        $testKey = 'PS_TEST_PERF_NUMERIC';

        // Test with string '1'
        Configuration::updateValue($testKey, '1');
        $initialDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );

        sleep(1);

        // Update with integer 1 (should be considered same due to == comparison)
        Configuration::updateValue($testKey, 1);
        $newDateUpd = Db::getInstance()->getValue(
            'SELECT date_upd FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($testKey) . '\''
        );

        $this->assertEquals(
            $initialDateUpd,
            $newDateUpd,
            'Numeric values should use loose comparison (==) - "1" and 1 should be considered equal'
        );

        // Cleanup
        Configuration::deleteByName($testKey);
    }
}
