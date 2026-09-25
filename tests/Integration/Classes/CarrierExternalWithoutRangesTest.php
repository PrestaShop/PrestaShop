<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Carrier;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Db;
use Language;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A carrier module that computes its price through an API sets `need_range = 0` and implements
 * `getOrderShippingCostExternal()`. The carrier list filtered those out in SQL, so such a carrier
 * never appeared in the front office and the external price method was never reached.
 */
class CarrierExternalWithoutRangesTest extends KernelTestCase
{
    private const ZONE_ID = 1;

    private static int $externalCarrierId;
    private static int $pricedCarrierId;
    private static int $groupId;
    private static int $cartId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $db = Db::getInstance();
        self::$groupId = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');

        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier
             (id_reference, name, url, active, deleted, shipping_handling, range_behavior, is_module,
              is_free, shipping_external, need_range, external_module_name, shipping_method, position,
              max_width, max_height, max_depth, max_weight, grade)
             VALUES (0, "External probe carrier", "", 1, 0, 0, 0, 1, 1, 1, 0, "probe_carrier_module", 0, 99, 0, 0, 0, 0, 0)'
        );
        self::$externalCarrierId = (int) $db->Insert_ID();

        $db->execute(
            'UPDATE ' . _DB_PREFIX_ . 'carrier SET id_reference = ' . self::$externalCarrierId
            . ' WHERE id_carrier = ' . self::$externalCarrierId
        );
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_lang (id_carrier, id_shop, id_lang, delay)
             VALUES (' . self::$externalCarrierId . ', 1, ' . (int) Configuration::get('PS_LANG_DEFAULT') . ', "probe")'
        );
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_shop (id_carrier, id_shop) VALUES (' . self::$externalCarrierId . ', 1)'
        );
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_zone (id_carrier, id_zone) VALUES (' . self::$externalCarrierId . ', ' . self::ZONE_ID . ')'
        );
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_carrier, id_group) VALUES (' . self::$externalCarrierId . ', ' . self::$groupId . ')'
        );

        // A second one that is not free, so it goes through the range checks and the exclusion
        // becomes visible in the error list rather than silently.
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier
             (id_reference, name, url, active, deleted, shipping_handling, range_behavior, is_module,
              is_free, shipping_external, need_range, external_module_name, shipping_method, position,
              max_width, max_height, max_depth, max_weight, grade)
             VALUES (0, "External priced probe", "", 1, 0, 0, 0, 1, 0, 1, 0, "probe_carrier_module", 2, 99, 0, 0, 0, 0, 0)'
        );
        self::$pricedCarrierId = (int) $db->Insert_ID();
        $db->execute('UPDATE ' . _DB_PREFIX_ . 'carrier SET id_reference = ' . self::$pricedCarrierId . ' WHERE id_carrier = ' . self::$pricedCarrierId);
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_lang (id_carrier, id_shop, id_lang, delay) VALUES (' . self::$pricedCarrierId . ', 1, ' . (int) Configuration::get('PS_LANG_DEFAULT') . ', "probe")');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_shop (id_carrier, id_shop) VALUES (' . self::$pricedCarrierId . ', 1)');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_zone (id_carrier, id_zone) VALUES (' . self::$pricedCarrierId . ', ' . self::ZONE_ID . ')');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_carrier, id_group) VALUES (' . self::$pricedCarrierId . ', ' . self::$groupId . ')');

        // Pricing a non-free carrier needs a cart to price against.
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart
             (id_shop_group, id_shop, id_carrier, delivery_option, id_lang, id_address_delivery,
              id_address_invoice, id_currency, id_customer, secure_key, date_add, date_upd)
             VALUES (1, 1, 0, "", 1, 0, 0, 1, 1, "x", NOW(), NOW())'
        );
        self::$cartId = (int) $db->Insert_ID();
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_product
             (id_cart, id_product, id_address_delivery, id_shop, id_product_attribute, quantity, date_add)
             VALUES (' . self::$cartId . ', 1, 0, 1, 1, 1, NOW())'
        );
    }

    public static function tearDownAfterClass(): void
    {
        $db = Db::getInstance();
        $db->delete('cart_product', 'id_cart = ' . self::$cartId);
        $db->delete('cart', 'id_cart = ' . self::$cartId);
        foreach (['carrier_lang', 'carrier_shop', 'carrier_zone', 'carrier_group', 'carrier'] as $table) {
            $db->delete($table, 'id_carrier IN (' . self::$externalCarrierId . ', ' . self::$pricedCarrierId . ')');
        }
        Carrier::resetStaticCache();
        parent::tearDownAfterClass();
    }

    public function testACarrierComputingItsPriceExternallyIsOffered(): void
    {
        self::bootKernel();

        $context = Context::getContext();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);

        $context->currency = new Currency(1);
        $context->customer = new Customer(1);
        $context->container = self::getContainer();
        $cart = new Cart(self::$cartId);
        $context->cart = $cart;

        $error = [];
        $carriers = Carrier::getCarriersForOrder(self::ZONE_ID, [self::$groupId], $cart, $error);

        $offeredIds = array_map('intval', array_column($carriers, 'id_carrier'));

        self::assertContains(
            self::$externalCarrierId,
            $offeredIds,
            'a module carrier with need_range = 0 was not offered, so its external price is never asked for'
        );

        // The priced one is dropped later for want of a real module, but it must not be dropped for
        // missing ranges - a carrier that declares it needs none cannot be judged by them.
        self::assertArrayNotHasKey(
            self::$pricedCarrierId,
            $error,
            'the carrier was excluded over ranges it declared it does not need'
        );
    }
}
