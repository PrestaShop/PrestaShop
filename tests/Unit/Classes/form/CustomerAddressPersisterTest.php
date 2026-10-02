<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\form;

use Address;
use Cart;
use Customer;
use CustomerAddressPersister;
use PHPUnit\Framework\TestCase;

class CustomerAddressPersisterTest extends TestCase
{
    private const TOKEN = 'address-token';

    public function testSaveIsRejectedWhenCustomerIsNotLoaded(): void
    {
        $customer = $this->createCustomer(null);
        $cart = $this->createCart(0);
        $address = $this->createAddressThatMustNotBeSaved();

        $persister = new CustomerAddressPersister(
            $customer,
            $cart,
            self::TOKEN
        );

        self::assertFalse(
            $persister->save($address, self::TOKEN)
        );
        self::assertSame(0, $address->id_customer);
    }

    public function testSaveIsRejectedWhenCartBelongsToAnotherCustomer(): void
    {
        $customer = $this->createCustomer(42);
        $cart = $this->createCart(43);
        $address = $this->createAddressThatMustNotBeSaved();

        $persister = new CustomerAddressPersister(
            $customer,
            $cart,
            self::TOKEN
        );

        self::assertFalse(
            $persister->save($address, self::TOKEN)
        );
        self::assertSame(0, $address->id_customer);
    }

    public function testSavePersistsAddressWhenCustomerOwnsCart(): void
    {
        $customer = $this->createCustomer(42);
        $cart = $this->createCart(42);
        $address = $this->createAddressThatMustBeSaved();

        $persister = new CustomerAddressPersister(
            $customer,
            $cart,
            self::TOKEN
        );

        self::assertTrue(
            $persister->save($address, self::TOKEN)
        );
        self::assertSame(42, $address->id_customer);
    }

    public function testSavePersistsAddressWhenCartHasNoCustomer(): void
    {
        $customer = $this->createCustomer(42);
        $cart = $this->createCart(0);
        $address = $this->createAddressThatMustBeSaved();

        $persister = new CustomerAddressPersister(
            $customer,
            $cart,
            self::TOKEN
        );

        self::assertTrue(
            $persister->save($address, self::TOKEN)
        );
        self::assertSame(42, $address->id_customer);
    }

    private function createCustomer(?int $id): Customer
    {
        $customer = $this->createMock(Customer::class);
        $customer->id = $id;

        return $customer;
    }

    private function createCart(int $idCustomer): Cart
    {
        $cart = $this->createMock(Cart::class);
        $cart->id_customer = $idCustomer;

        return $cart;
    }

    private function createAddressThatMustNotBeSaved(): Address
    {
        $address = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isUsed', 'save'])
            ->getMock();

        $address->id_customer = 0;

        $address
            ->expects(self::never())
            ->method('isUsed');

        $address
            ->expects(self::never())
            ->method('save');

        return $address;
    }

    private function createAddressThatMustBeSaved(): Address
    {
        $address = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isUsed', 'save'])
            ->getMock();

        $address->id_customer = 0;

        $address
            ->expects(self::once())
            ->method('isUsed')
            ->willReturn(false);

        $address
            ->expects(self::once())
            ->method('save')
            ->willReturn(true);

        return $address;
    }
}
