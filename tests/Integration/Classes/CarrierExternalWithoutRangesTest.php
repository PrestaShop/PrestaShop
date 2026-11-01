<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
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

class CarrierExternalWithoutRangesTest extends KernelTestCase
{
    private const ZONE_ID = 1;
    private const MODULE_NAME = 'probe_carrier_module';
    private const SHIPPING_COST_KEY = 'PROBE_CARRIER_MODULE_SHIPPING_COST';

    private static int $freeExternalCarrierId;
    private static int $pricedExternalCarrierId;
    private static int $moduleCarrierWithoutRangesId;
    private static int $moduleId;
    private static int $groupId;
    private static int $cartId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $db = Db::getInstance();
        self::$groupId = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');

        $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'module WHERE name = "' . self::MODULE_NAME . '"');
        $db->insert('module', ['name' => self::MODULE_NAME, 'active' => 1, 'version' => '1.0.0']);
        self::$moduleId = (int) $db->Insert_ID();
        $db->insert('module_shop', ['id_module' => self::$moduleId, 'id_shop' => 1, 'enable_device' => 7]);

        self::$freeExternalCarrierId = self::insertCarrier('Free external probe', 1, 1, 0);
        self::$pricedExternalCarrierId = self::insertCarrier('Priced external probe', 0, 1, 2);
        self::$moduleCarrierWithoutRangesId = self::insertCarrier('Module probe without ranges', 0, 0, 2);

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
        $carrierIds = implode(',', [self::$freeExternalCarrierId, self::$pricedExternalCarrierId, self::$moduleCarrierWithoutRangesId]);
        foreach (['carrier_lang', 'carrier_shop', 'carrier_zone', 'carrier_group', 'carrier'] as $table) {
            $db->delete($table, 'id_carrier IN (' . $carrierIds . ')');
        }
        $db->delete('module_shop', 'id_module = ' . self::$moduleId);
        $db->delete('module', 'id_module = ' . self::$moduleId);
        Configuration::deleteByName(self::SHIPPING_COST_KEY);
        Carrier::resetStaticCache();
        parent::tearDownAfterClass();
    }

    public function testAFreeCarrierComputingItsPriceExternallyIsOffered(): void
    {
        $offeredIds = array_keys($this->getCarriersForOrder(4.2));

        self::assertContains(self::$freeExternalCarrierId, $offeredIds);
    }

    public function testAPricedCarrierIsOfferedAtThePriceItsModuleReturns(): void
    {
        $carriers = $this->getCarriersForOrder(4.2);

        self::assertArrayHasKey(self::$pricedExternalCarrierId, $carriers);
        self::assertEqualsWithDelta(4.2, (float) $carriers[self::$pricedExternalCarrierId]['price_tax_exc'], 0.001);
    }

    public function testACarrierWhoseModuleReturnsFalseIsNotOffered(): void
    {
        self::assertArrayNotHasKey(self::$pricedExternalCarrierId, $this->getCarriersForOrder(false));
    }

    public function testAModuleCarrierThatDoesNotPriceItselfStillNeedsRanges(): void
    {
        self::assertArrayNotHasKey(self::$moduleCarrierWithoutRangesId, $this->getCarriersForOrder(4.2));
    }

    /**
     * @param float|false $modulePrice
     *
     * @return array<int, array<string, mixed>> offered carriers by id
     */
    private function getCarriersForOrder($modulePrice): array
    {
        self::bootKernel();
        if (false === $modulePrice) {
            Configuration::deleteByName(self::SHIPPING_COST_KEY);
        } else {
            Configuration::updateGlobalValue(self::SHIPPING_COST_KEY, (string) $modulePrice);
        }
        Cache::clean('*');
        Cart::resetStaticCache();

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

        return array_column($carriers, null, 'id_carrier');
    }

    private static function insertCarrier(string $name, int $isFree, int $shippingExternal, int $shippingMethod): int
    {
        $db = Db::getInstance();
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier
             (id_reference, name, url, active, deleted, shipping_handling, range_behavior, is_module,
              is_free, shipping_external, need_range, external_module_name, shipping_method, position,
              max_width, max_height, max_depth, max_weight, grade)
             VALUES (0, "' . pSQL($name) . '", "", 1, 0, 0, 0, 1, ' . $isFree . ', ' . $shippingExternal . ', 0, "' . self::MODULE_NAME . '", ' . $shippingMethod . ', 99, 0, 0, 0, 0, 0)'
        );
        $carrierId = (int) $db->Insert_ID();
        $db->execute('UPDATE ' . _DB_PREFIX_ . 'carrier SET id_reference = ' . $carrierId . ' WHERE id_carrier = ' . $carrierId);
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_lang (id_carrier, id_shop, id_lang, delay) VALUES (' . $carrierId . ', 1, ' . (int) Configuration::get('PS_LANG_DEFAULT') . ', "probe")');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_shop (id_carrier, id_shop) VALUES (' . $carrierId . ', 1)');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_zone (id_carrier, id_zone) VALUES (' . $carrierId . ', ' . self::ZONE_ID . ')');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_carrier, id_group) VALUES (' . $carrierId . ', ' . self::$groupId . ')');

        return $carrierId;
    }
}
