<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Address;
use Cache;
use Cart;
use Configuration;
use Customer;
use Db;
use FrontController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A logged in customer with no address at all resolves to address zero, which is what the cart already
 * holds, so nothing changes. The controller nevertheless reported an update and saved the cart on every
 * page request of that customer, resetting the product caches and firing actionCartSave each time.
 */
class CartAddressAutoAssignmentTest extends TestCase
{
    private const EMAIL_SUFFIX = '@cart-address-34177.test';

    /** @var Customer */
    private $customerWithoutAddress;
    /** @var Customer */
    private $customerWithAddress;
    /** @var int */
    private $idAddress;

    protected function setUp(): void
    {
        parent::setUp();
        self::removeFixture();

        $this->customerWithoutAddress = self::makeCustomer('no-address');
        $this->customerWithAddress = self::makeCustomer('with-address');
        $this->idAddress = (int) self::makeAddress($this->customerWithAddress)->id;

        Cache::clean('Address::getFirstCustomerAddressId_*');
    }

    protected function tearDown(): void
    {
        self::removeFixture();
        Cache::clean('Address::getFirstCustomerAddressId_*');

        parent::tearDown();
    }

    /**
     * The precondition the bug rests on: there is nothing to assign.
     */
    public function testACustomerWithoutAnAddressResolvesToZero(): void
    {
        self::assertSame(0, (int) Address::getFirstCustomerAddressId((int) $this->customerWithoutAddress->id));
    }

    /**
     * The reported bug: no address to assign must not be reported as an update.
     */
    public function testNoUpdateIsReportedWhenTheCustomerHasNoAddress(): void
    {
        $cart = $this->makeCart($this->customerWithoutAddress);

        self::assertFalse(
            $this->assignAddresses($cart),
            'nothing was assigned, so the cart must not be saved'
        );
        self::assertSame(0, (int) $cart->id_address_delivery);
        self::assertSame(0, (int) $cart->id_address_invoice);
    }

    /**
     * And the behaviour that must be kept.
     */
    public function testTheFirstAddressIsAssignedWhenTheCustomerHasOne(): void
    {
        $cart = $this->makeCart($this->customerWithAddress);

        self::assertTrue($this->assignAddresses($cart), 'an address was assigned, so the cart needs saving');
        self::assertSame($this->idAddress, (int) $cart->id_address_delivery);
        self::assertSame($this->idAddress, (int) $cart->id_address_invoice);
    }

    /**
     * A cart that already carries both addresses has nothing to do either.
     */
    public function testNoUpdateIsReportedWhenBothAddressesAreAlreadySet(): void
    {
        $cart = $this->makeCart($this->customerWithAddress);
        $cart->id_address_delivery = $this->idAddress;
        $cart->id_address_invoice = $this->idAddress;

        self::assertFalse($this->assignAddresses($cart));
    }

    /**
     * The two allocation flags are independent, and a controller that switches one off must not have
     * the cart saved on its behalf by the other being empty.
     */
    public function testOnlyTheEnabledAllocationIsPerformed(): void
    {
        $cart = $this->makeCart($this->customerWithAddress);

        self::assertTrue($this->assignAddresses($cart, true, false));
        self::assertSame($this->idAddress, (int) $cart->id_address_delivery);
        self::assertSame(0, (int) $cart->id_address_invoice, 'invoice allocation was switched off');
    }

    private function assignAddresses(Cart $cart, bool $delivery = true, bool $invoice = true): bool
    {
        // The constructor of a front controller boots a whole page context; this method only reads the
        // two allocation flags and the cart handed to it.
        $controller = (new ReflectionClass(FrontController::class))->newInstanceWithoutConstructor();

        foreach (['automaticallyAllocateDeliveryAddress' => $delivery, 'automaticallyAllocateInvoiceAddress' => $invoice] as $name => $value) {
            $property = new ReflectionProperty(FrontController::class, $name);
            $property->setAccessible(true);
            $property->setValue($controller, $value);
        }

        $method = new ReflectionMethod(FrontController::class, 'assignFirstCustomerAddresses');
        $method->setAccessible(true);

        return (bool) $method->invoke($controller, $cart);
    }

    private function makeCart(Customer $customer): Cart
    {
        // Not saved: the method under test only reads and writes the object's own fields.
        $cart = new Cart();
        $cart->id_customer = (int) $customer->id;
        $cart->id_address_delivery = 0;
        $cart->id_address_invoice = 0;

        return $cart;
    }

    private static function makeCustomer(string $slug): Customer
    {
        $customer = new Customer();
        $customer->firstname = 'Cart';
        $customer->lastname = 'Address';
        $customer->email = $slug . self::EMAIL_SUFFIX;
        // Only a length check (32 or 60); the fixture never authenticates, so this is a placeholder.
        $customer->passwd = str_repeat('x', 60);
        $customer->add();

        return $customer;
    }

    private static function makeAddress(Customer $customer): Address
    {
        $address = new Address();
        $address->id_customer = (int) $customer->id;
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->firstname = 'Cart';
        $address->lastname = 'Address';
        $address->address1 = '55 rue Raspail';
        $address->alias = 'fixture-34177';
        $address->city = 'Levallois';
        $address->add();

        return $address;
    }

    private static function removeFixture(): void
    {
        $db = Db::getInstance();
        $rows = $db->executeS(
            'SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE email LIKE "%' . self::EMAIL_SUFFIX . '"'
        );
        foreach ($rows as $row) {
            $id = (int) $row['id_customer'];
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'address WHERE id_customer = ' . $id);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'customer WHERE id_customer = ' . $id);
        }
    }
}
