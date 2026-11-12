<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

/**
 * Address::delete() must never leave a cart pointing to a non-existing address:
 * carts without order are reset, carts that already have an order follow the
 * address of their order (which is never hard-deleted while the order exists).
 */
class AddressDeleteCartAddressTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['address', 'cart', 'orders']);
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(['address', 'cart', 'orders']);
        parent::tearDownAfterClass();
    }

    public function testOrderedCartFollowsTheOrderAddressWhenItsAddressIsHardDeleted(): void
    {
        $deletedAddress = $this->createAddress('To be deleted');
        $orderAddress = $this->createAddress('Order address');

        // A cart whose order was placed with another address, e.g. a pickup point
        // address created by a carrier module: the order does not reference the
        // cart address, so deleting it is a hard delete.
        $orderedCartId = $this->createCart((int) $deletedAddress->id, (int) $deletedAddress->id);
        $this->createOrder($orderedCartId, (int) $orderAddress->id, (int) $orderAddress->id);

        // A cart without order, reset as before.
        $pendingCartId = $this->createCart((int) $deletedAddress->id, (int) $deletedAddress->id);

        $this->assertTrue($deletedAddress->delete());
        $this->assertFalse(Address::addressExists((int) $deletedAddress->id), 'the address should be hard deleted');

        $orderedCart = $this->getCartAddresses($orderedCartId);
        $this->assertSame((int) $orderAddress->id, $orderedCart['id_address_delivery']);
        $this->assertSame((int) $orderAddress->id, $orderedCart['id_address_invoice']);

        $pendingCart = $this->getCartAddresses($pendingCartId);
        $this->assertSame(0, $pendingCart['id_address_delivery']);
        $this->assertSame(0, $pendingCart['id_address_invoice']);
    }

    public function testOrderedCartIsNotTouchedWhenTheOrderUsesTheAddress(): void
    {
        $usedAddress = $this->createAddress('Used by an order');

        $orderedCartId = $this->createCart((int) $usedAddress->id, (int) $usedAddress->id);
        $this->createOrder($orderedCartId, (int) $usedAddress->id, (int) $usedAddress->id);

        $this->assertTrue($usedAddress->delete());
        $this->assertTrue(Address::addressExists((int) $usedAddress->id), 'the address should only be soft deleted');

        $orderedCart = $this->getCartAddresses($orderedCartId);
        $this->assertSame((int) $usedAddress->id, $orderedCart['id_address_delivery']);
        $this->assertSame((int) $usedAddress->id, $orderedCart['id_address_invoice']);
    }

    private function createAddress(string $alias): Address
    {
        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->alias = $alias;
        $address->lastname = 'Doe';
        $address->firstname = 'John';
        $address->address1 = '1 test street';
        $address->postcode = '75001';
        $address->city = 'Paris';
        $this->assertTrue($address->add());

        return $address;
    }

    private function createCart(int $deliveryAddressId, int $invoiceAddressId): int
    {
        $now = date('Y-m-d H:i:s');
        Db::getInstance()->insert('cart', [
            'id_shop_group' => 1,
            'id_shop' => 1,
            'id_carrier' => 1,
            'delivery_option' => '',
            'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'id_address_delivery' => $deliveryAddressId,
            'id_address_invoice' => $invoiceAddressId,
            'id_currency' => (int) Configuration::get('PS_CURRENCY_DEFAULT'),
            'id_customer' => 0,
            'id_guest' => 0,
            'secure_key' => md5('cart'),
            'date_add' => $now,
            'date_upd' => $now,
        ]);

        return (int) Db::getInstance()->Insert_ID();
    }

    private function createOrder(int $cartId, int $deliveryAddressId, int $invoiceAddressId): void
    {
        $now = date('Y-m-d H:i:s');
        Db::getInstance()->insert('orders', [
            'id_shop_group' => 1,
            'id_shop' => 1,
            'id_carrier' => 1,
            'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'id_customer' => 0,
            'id_cart' => $cartId,
            'id_currency' => (int) Configuration::get('PS_CURRENCY_DEFAULT'),
            'id_address_delivery' => $deliveryAddressId,
            'id_address_invoice' => $invoiceAddressId,
            'current_state' => 1,
            'secure_key' => md5('order'),
            'payment' => 'Test',
            'module' => 'test',
            'conversion_rate' => 1,
            'date_add' => $now,
            'date_upd' => $now,
        ]);
    }

    /**
     * @return array{id_address_delivery: int, id_address_invoice: int}
     */
    private function getCartAddresses(int $cartId): array
    {
        $row = Db::getInstance()->getRow(
            'SELECT id_address_delivery, id_address_invoice FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . $cartId
        );

        return [
            'id_address_delivery' => (int) $row['id_address_delivery'],
            'id_address_invoice' => (int) $row['id_address_invoice'],
        ];
    }
}
