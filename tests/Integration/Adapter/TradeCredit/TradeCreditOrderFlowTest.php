<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use Cache;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Module;
use Order;
use PaymentModule;
use PrestaShop\PrestaShop\Adapter\TradeCredit\Repository\TradeCreditPaymentModuleRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditInsufficientException;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use PrestaShopException;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

/**
 * Real orders validated through PaymentModule::validateOrder() with the bankwire test payment module.
 */
class TradeCreditOrderFlowTest extends SymfonyIntegrationTestCase
{
    protected const CUSTOMER_ID = 1;
    protected const ADDRESS_ID = 1;
    protected const CARRIER_ID = 2;
    protected const PRODUCT_ID = 1;
    protected const OTHER_PRODUCT_ID = 2;
    protected const PAYMENT_MODULE = 'bankwire';

    protected CustomerTradeCreditRepository $repository;

    protected TradeCreditPaymentModuleRepository $paymentModuleRepository;

    protected PaymentModule $paymentModule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(CustomerTradeCreditRepository::class);
        $this->paymentModuleRepository = self::getContainer()->get(TradeCreditPaymentModuleRepository::class);
        $this->paymentModule = Module::getInstanceByName(self::PAYMENT_MODULE);

        // Credit kept in the shop default currency unless a test says otherwise
        $defaultCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        Configuration::updateValue('PS_TRADE_CREDIT_CURRENCY', $defaultCurrency->iso_code);
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', 1);
        $this->paymentModuleRepository->setTradeCreditModules(1, [(int) $this->paymentModule->id]);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreAllTables();
        Cache::clean('*');
        parent::tearDown();
    }

    public function testOrderWithTradeCreditModuleDebitsTotalWithShipping(): void
    {
        $cart = $this->createCart();

        $order = $this->validateOrder($cart);

        $this->assertGreaterThan(0, (float) $order->total_shipping_tax_incl, 'The test carrier must charge shipping.');
        $expected = number_format(50000 - (float) $order->total_paid_tax_incl, 6, '.', '');
        $this->assertSame($expected, $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testOrderWithOtherModuleDoesNotTouchCredit(): void
    {
        $this->paymentModuleRepository->setTradeCreditModules(1, []);

        $this->validateOrder($this->createCart());

        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testFirstOrderGrantsLazily(): void
    {
        $cart = $this->createCart();
        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));

        $this->validateOrder($cart);

        $this->assertNotNull($this->repository->findOperation('grant:customer:' . self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('debit:cart:' . $cart->id));
    }

    public function testExceedingCreditRejectsOrder(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');
        $cart = $this->createCart();

        try {
            $this->validateOrder($cart);
            $this->fail('The order above the available trade credit must be rejected.');
        } catch (TradeCreditInsufficientException $e) {
            $this->assertStringContainsString('exceeds your available trade credit', $e->getMessage());
        }

        $this->assertFalse(Order::getIdByCartId((int) $cart->id));
        $this->assertSame('10.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testReserveUsesCartTotalAtValidationTime(): void
    {
        $cart = $this->createCart();
        $cart->updateQty(1, self::OTHER_PRODUCT_ID);
        $totalAtValidation = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $this->validateOrder($cart);

        $debit = $this->repository->findOperation('debit:cart:' . $cart->id);
        $this->assertSame(number_format(-$totalAtValidation, 6, '.', ''), $debit['amount']);
    }

    public function testOrderCreationFailureReleasesCredit(): void
    {
        $cart = $this->createCart();
        $failingModule = new class() extends \bankwire {
            protected function createOrderFromCart(Cart $cart, Currency $currency, $productList, $addressId, $context, $reference, $secure_key, $payment_method, $name, $dont_touch_amount, $amount_paid, $warehouseId, $cart_total_paid, $debug, $order_status, $id_order_state, $carrierId = null)
            {
                throw new PrestaShopException('Simulated order creation failure');
            }
        };

        try {
            $this->validateOrder($cart, $failingModule);
            $this->fail('The simulated failure must be propagated.');
        } catch (PrestaShopException $e) {
            $this->assertSame('Simulated order creation failure', $e->getMessage());
        }

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('release:cart:' . $cart->id));
    }

    public function testBackOfficeOrderWithoutCreditFails(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');
        $cart = $this->createCart();

        try {
            self::getContainer()->get('prestashop.core.command_bus')->handle(new AddOrderFromBackOfficeCommand(
                (int) $cart->id,
                1,
                '',
                self::PAYMENT_MODULE,
                (int) Configuration::get('PS_OS_BANKWIRE')
            ));
            $this->fail('The back office order above the available trade credit must be rejected.');
        } catch (OrderException $e) {
            $this->assertStringContainsString('exceeds your available trade credit', $e->getMessage());
        }

        $this->assertFalse(Order::getIdByCartId((int) $cart->id));
    }

    protected function createCart(?Currency $currency = null): Cart
    {
        $customer = new Customer(self::CUSTOMER_ID);
        $currency ??= new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));

        $cart = new Cart();
        $cart->id_customer = self::CUSTOMER_ID;
        $cart->id_address_delivery = self::ADDRESS_ID;
        $cart->id_address_invoice = self::ADDRESS_ID;
        $cart->id_currency = (int) $currency->id;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_carrier = self::CARRIER_ID;
        $cart->secure_key = $customer->secure_key;
        $cart->add();
        $cart->updateQty(1, self::PRODUCT_ID);
        $cart->setDeliveryOption([self::ADDRESS_ID => self::CARRIER_ID . ',']);
        $cart->update();

        $context = Context::getContext();
        $context->cart = $cart;
        $context->customer = $customer;
        $context->currency = $currency;

        return $cart;
    }

    protected function validateOrder(Cart $cart, ?PaymentModule $paymentModule = null, ?float $amountPaid = null): Order
    {
        $paymentModule ??= $this->paymentModule;
        $paymentModule->validateOrder(
            (int) $cart->id,
            (int) Configuration::get('PS_OS_BANKWIRE'),
            $amountPaid ?? (float) $cart->getOrderTotal(true, Cart::BOTH),
            'Bank wire',
            null,
            [],
            null,
            false,
            $cart->secure_key
        );

        return new Order((int) Order::getIdByCartId((int) $cart->id));
    }
}
