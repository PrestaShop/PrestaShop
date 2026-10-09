<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cart;
use Context;
use Currency;
use Customer;
use Db;
use Employee;
use Language;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A cart keeps the id of the address it was placed with, and that row can disappear - deleted
 * directly, or by something that did not clean up after itself. Pricing then asked for an address
 * that no longer exists and `Address::initialize()` threw, so the whole customer page in the back
 * office answered with a 500 rather than the one line that could not be priced.
 */
class ProductPriceWithDeletedAddressTest extends KernelTestCase
{
    private const MISSING_ADDRESS_ID = 999999;

    private static int $cartId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $db = Db::getInstance();
        self::assertEmpty(
            $db->getValue('SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE id_address = ' . self::MISSING_ADDRESS_ID),
            'the fixture needs an address id that does not exist'
        );

        // Written directly: the point is a cart pointing at an address row that is not there, which
        // the model would refuse to create.
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart
             (id_shop_group, id_shop, id_carrier, delivery_option, id_lang, id_address_delivery,
              id_address_invoice, id_currency, id_customer, secure_key, date_add, date_upd)
             VALUES (1, 1, 1, "", 1, ' . self::MISSING_ADDRESS_ID . ', ' . self::MISSING_ADDRESS_ID . ', 1, 1, "x", NOW(), NOW())'
        );
        self::$cartId = (int) $db->Insert_ID();

        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_product
             (id_cart, id_product, id_address_delivery, id_shop, id_product_attribute, quantity, date_add)
             VALUES (' . self::$cartId . ', 1, ' . self::MISSING_ADDRESS_ID . ', 1, 1, 1, NOW())'
        );
    }

    public static function tearDownAfterClass(): void
    {
        Db::getInstance()->delete('cart_product', 'id_cart = ' . self::$cartId);
        Db::getInstance()->delete('cart', 'id_cart = ' . self::$cartId);
        Cart::resetStaticCache();
        parent::tearDownAfterClass();
    }

    public function testACartPointingAtARemovedAddressCanStillBePriced(): void
    {
        self::bootKernel();
        Cart::resetStaticCache();

        $context = Context::getContext();
        // Legacy cart code resolves services through the context rather than being injected.
        $context->container = self::getContainer();
        $context->currency = new Currency(1);
        $context->language = new Language(1);
        $context->shop = new Shop(1);
        $context->customer = new Customer(1);
        // The back office reaches this with an employee, which is what decides whether the cart id
        // has to be passed down to the price computation.
        $context->employee = new Employee(1);

        $cart = new Cart(self::$cartId);
        $context->cart = $cart;

        $total = $cart->getOrderTotal(true, Cart::BOTH);

        self::assertIsNumeric($total, 'the cart total could not be computed at all');
        self::assertGreaterThan(0, (float) $total, 'the line should still be priced, using the contextual address');
    }
}
