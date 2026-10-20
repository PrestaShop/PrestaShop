<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Db;
use Shop as LegacyShop;
use SpecificPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class SpecificPriceShopGroupScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'specific_price', 'configuration'];

    private static int $secondShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        $configuration->set('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();

        // A second shop group with one shop in it (shop 1 stays in group 1).
        $db->insert('shop_group', [
            'name' => 'test_group_2', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_sp', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();

        LegacyShop::resetStaticCache();

        // Start from a clean slate for this product, then add a single price scoped to shop
        // GROUP 1 only (id_shop = 0 = all shops *in that group*). The fixtures already ship a
        // global specific price for product 1 that legitimately applies everywhere.
        $db->delete('specific_price', 'id_product = 1');
        $db->insert('specific_price', [
            'id_specific_price_rule' => 0, 'id_cart' => 0, 'id_product' => 1, 'id_shop' => 0,
            'id_shop_group' => 1, 'id_currency' => 0, 'id_country' => 0, 'id_group' => 0,
            'id_customer' => 0, 'id_product_attribute' => 0, 'price' => -1, 'from_quantity' => 1,
            'reduction' => 0.5, 'reduction_tax' => 1, 'reduction_type' => 'percentage',
            'from' => '0000-00-00 00:00:00', 'to' => '0000-00-00 00:00:00',
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * A specific price scoped to one shop group (id_shop_group set, id_shop = 0) must not apply to
     * shops in another group. getSpecificPrice() filtered every dimension except id_shop_group, so
     * such a row leaked to all shops in all groups.
     */
    public function testSpecificPriceDoesNotLeakAcrossShopGroups(): void
    {
        self::bootKernel();

        // Control: shop 1 is in group 1, so the group-1 price applies.
        $inGroup = SpecificPrice::getSpecificPrice(1, 1, 0, 0, 0, 1, 0, 0, 0, 0);
        self::assertNotEmpty($inGroup, 'a specific price must apply within its own shop group');

        // The second shop is in another group, so the group-1 price must NOT apply.
        $otherGroup = SpecificPrice::getSpecificPrice(1, self::$secondShopId, 0, 0, 0, 1, 0, 0, 0, 0);
        self::assertEmpty($otherGroup, 'a shop-group-scoped specific price must not leak to another group');
    }
}
