<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    /**
     * The name column collates case insensitively while every read goes through a PHP array, so a
     * key differing from an existing one only by case used to resolve to that other key's row: the
     * insert was skipped because the row "existed", the update was skipped because the cache said it
     * did not, and updateValue() returned true having written nothing.
     */
    public function testKeysDifferingOnlyByCaseAreDistinct(): void
    {
        Configuration::deleteByName('PS_TEST_CASE_KEY');
        Configuration::deleteByName('ps_test_case_key');

        Configuration::updateValue('PS_TEST_CASE_KEY', 'upper');
        Configuration::updateValue('ps_test_case_key', 'lower');

        $this->assertSame('upper', Configuration::get('PS_TEST_CASE_KEY'));
        $this->assertSame('lower', Configuration::get('ps_test_case_key'));

        // updating one leaves the other alone
        Configuration::updateValue('PS_TEST_CASE_KEY', 'upper again');

        $this->assertSame('upper again', Configuration::get('PS_TEST_CASE_KEY'));
        $this->assertSame('lower', Configuration::get('ps_test_case_key'));

        // and so does deleting one
        Configuration::deleteByName('ps_test_case_key');

        $this->assertSame('upper again', Configuration::get('PS_TEST_CASE_KEY'));
        $this->assertFalse(Configuration::get('ps_test_case_key'));

        Configuration::deleteByName('PS_TEST_CASE_KEY');
    }

    public function testGetIdByNameMatchesTheExactKeyOnly(): void
    {
        Configuration::deleteByName('PS_TEST_CASE_ID');
        Configuration::updateValue('PS_TEST_CASE_ID', 'value');

        $this->assertGreaterThan(0, Configuration::getIdByName('PS_TEST_CASE_ID'));
        $this->assertSame(0, Configuration::getIdByName('ps_test_case_id'));

        Configuration::deleteByName('PS_TEST_CASE_ID');
    }
}
