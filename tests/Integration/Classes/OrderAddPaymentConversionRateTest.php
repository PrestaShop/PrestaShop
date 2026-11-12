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
use Order;
use OrderPayment;
use PHPUnit\Framework\TestCase;
use PrestaShopCollection;
use Tests\Resources\DatabaseDump;
use Validate;

final class OrderAddPaymentConversionRateTest extends TestCase
{
    private const ORDER_CONVERSION_RATE = 0.88;

    private const PAYMENT_AMOUNT = '10.00';

    private static ?int $sourceOrderId = null;

    private static ?int $testCurrencyId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::restoreTables();

        // Use an existing valid order as a template, but never change that order.
        // Each test creates its own cart and order in a non-default currency.
        foreach (new PrestaShopCollection('Order') as $order) {
            if (Validate::isLoadedObject(new Cart((int) $order->id_cart))) {
                self::$sourceOrderId = (int) $order->id;
                break;
            }
        }

        $defaultCurrencyId = Currency::getDefaultCurrencyId();
        foreach (new PrestaShopCollection('Currency') as $currency) {
            if ((int) $currency->id !== $defaultCurrencyId) {
                $currency->active = true;
                self::assertTrue($currency->update(), 'Unable to activate the test currency');
                self::$testCurrencyId = (int) $currency->id;

                break;
            }
        }

        if (self::$testCurrencyId === null) {
            // A fresh test database may contain only the default currency.
            $currency = clone new Currency($defaultCurrencyId);
            $currency->id = null;
            $currency->iso_code = 'XTS';
            $currency->numeric_iso_code = '963';
            $currency->conversion_rate = 1.25;
            $currency->active = true;
            $currency->deleted = false;

            self::assertTrue($currency->add(), 'Unable to create a non-default test currency');
            self::$testCurrencyId = (int) $currency->id;
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::restoreTables();

        parent::tearDownAfterClass();
    }

    public function testItUsesTheOrderRateWithoutAnExplicitCurrency(): void
    {
        $order = $this->createOrder();

        $this->assertTrue($order->addOrderPayment(self::PAYMENT_AMOUNT));
        $this->assertPaymentRate($order, self::ORDER_CONVERSION_RATE, (int) $order->id_currency);
    }

    public function testItUsesTheHistoricalOrderRateForAnExplicitMatchingCurrency(): void
    {
        $order = $this->createOrder();
        $currency = new Currency((int) $order->id_currency);
        $currency->conversion_rate = 1.25;

        $this->assertTrue($order->addOrderPayment(self::PAYMENT_AMOUNT, null, null, $currency));
        $this->assertPaymentRate($order, self::ORDER_CONVERSION_RATE, (int) $order->id_currency);
    }

    public function testItUsesTheExplicitRateForAnotherCurrency(): void
    {
        $order = $this->createOrder();
        $currency = new Currency((int) Currency::getDefaultCurrencyId());
        $currency->conversion_rate = 1.25;

        // Cross-currency payments use the shop context to determine the rounding precision.
        $context = Context::getContext();
        $previousCurrency = $context->currency;
        $context->currency = new Currency(Currency::getDefaultCurrencyId());

        try {
            $this->assertTrue($order->addOrderPayment(self::PAYMENT_AMOUNT, null, null, $currency));
            $this->assertPaymentRate($order, 1.25, (int) $currency->id);
        } finally {
            $context->currency = $previousCurrency;
        }
    }

    public function testItFallsBackToOneForAZeroOrderRate(): void
    {
        $order = $this->createOrder();
        $order->conversion_rate = 0;
        $this->assertTrue($order->update());

        // Reload the order to test the database-hydrated conversion rate.
        $order = new Order((int) $order->id);

        $this->assertTrue($order->addOrderPayment(self::PAYMENT_AMOUNT));
        $this->assertPaymentRate($order, 1.0, (int) $order->id_currency);
    }

    private function createOrder(): Order
    {
        self::assertNotNull(self::$sourceOrderId, 'An existing order fixture is required');
        self::assertNotNull(self::$testCurrencyId, 'Unable to initialize a non-default currency fixture');

        $source = new Order(self::$sourceOrderId);
        $cart = clone new Cart((int) $source->id_cart);
        $cart->id = null;
        $cart->id_currency = self::$testCurrencyId;
        self::assertTrue($cart->add(), 'Unable to create a test cart');

        $order = clone $source;
        $order->id = null;
        $order->id_cart = (int) $cart->id;
        $order->reference = Order::generateReference();
        $order->id_currency = self::$testCurrencyId;
        $order->conversion_rate = self::ORDER_CONVERSION_RATE;
        $order->total_paid_real = 0;
        self::assertTrue($order->add(), 'Unable to create a test order');

        return $order;
    }

    private function assertPaymentRate(Order $order, float $expectedRate, int $expectedCurrencyId): void
    {
        $payments = $order->getOrderPaymentCollection();
        self::assertCount(1, $payments);

        foreach ($payments as $payment) {
            self::assertInstanceOf(OrderPayment::class, $payment);
            self::assertSame($expectedRate, (float) $payment->conversion_rate);
            self::assertSame($expectedCurrencyId, (int) $payment->id_currency);
        }
    }

    private static function restoreTables(): void
    {
        DatabaseDump::restoreTables(['cart', 'orders', 'order_payment', 'currency', 'currency_lang', 'currency_shop']);
    }
}
