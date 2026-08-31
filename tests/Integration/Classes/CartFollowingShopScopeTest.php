<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cart;
use Configuration as LegacyConfiguration;
use Db;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * "Re-display cart at login" resolves the cart through Cart::lastNoneOrderedCart(), which is shop
 * scoped. The customer's own cart on the current shop must be found even when a more recent cart of
 * theirs exists on another shop.
 */
class CartFollowingShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['cart', 'shop', 'shop_group', 'configuration'];

    private const CUSTOMER_ID = 1;

    private static int $secondShopId;

    private static int $cartOnFirstShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();

        // A second shop in its own group, so nothing is shared with the first one.
        $db->insert('shop_group', [
            'name' => 'test_group_cart', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_cart', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        // The customer has a cart on the first shop, and then a more recent one on the second.
        self::$cartOnFirstShopId = self::insertCart(1, $secondGroupId - 1);
        self::insertCart(self::$secondShopId, $secondGroupId);
    }

    public static function tearDownAfterClass(): void
    {
        // Restoring the tables is not enough: the shop context is static, so leaving it pointed at
        // the shop this class created makes every later test read through a shop that no longer
        // exists once the rows are gone.
        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDownAfterClass();
    }

    private static function insertCart(int $shopId, int $shopGroupId): int
    {
        $db = Db::getInstance();
        $db->insert('cart', [
            'id_shop_group' => $shopGroupId,
            'id_shop' => $shopId,
            'id_customer' => self::CUSTOMER_ID,
            'id_guest' => 1,
            'id_lang' => (int) LegacyConfiguration::get('PS_LANG_DEFAULT'),
            'id_currency' => (int) LegacyConfiguration::get('PS_CURRENCY_DEFAULT'),
            'id_address_delivery' => 0,
            'id_address_invoice' => 0,
            'id_carrier' => 0,
            'recyclable' => 0,
            'gift' => 0,
            'mobile_theme' => 0,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->Insert_ID();
    }

    /**
     * The newest cart of this customer belongs to the other shop, so a lookup that picks it before
     * applying the shop restriction finds nothing and the customer loses their cart at login.
     */
    public function testTheCartOfTheCurrentShopIsFoundDespiteANewerCartElsewhere(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        $this->assertSame(self::$cartOnFirstShopId, Cart::lastNoneOrderedCart(self::CUSTOMER_ID));
    }

    /**
     * The counterpart, so the fix cannot be "return any cart of the customer": browsing the second
     * shop must resolve that shop's cart, not the first one's.
     */
    public function testTheOtherShopResolvesItsOwnCart(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, self::$secondShopId);

        $resolved = Cart::lastNoneOrderedCart(self::CUSTOMER_ID);

        $this->assertNotSame(self::$cartOnFirstShopId, $resolved);
        $this->assertNotFalse($resolved);
    }
}
