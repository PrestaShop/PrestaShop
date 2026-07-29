<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Order;
use PHPUnit\Framework\TestCase;

/**
 * Order::addOrderPayment() falls back to the order's own currency when no
 * Currency object is passed. The conversion rate stored in the payment must
 * therefore come from the order as well.
 *
 * This covers the front-office order creation path, where
 * PaymentModule::validateOrder() calls addOrderPayment() without explicitly
 * passing a Currency object.
 */
class OrderAddPaymentConversionRateTest extends TestCase
{
    private const ORDER_ID = 1;

    private const ORDER_CONVERSION_RATE = 0.88;

    private const TRANSACTION_ID = 'qa-conversion-rate-probe';

    private float $originalConversionRate;

    private float $originalTotalPaidReal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteTestPayment();

        $order = new Order(self::ORDER_ID);

        $this->assertTrue(
            (bool) $order->id,
            sprintf('Fixture order with ID %d was not found', self::ORDER_ID)
        );

        $this->originalConversionRate = (float) $order->conversion_rate;
        $this->originalTotalPaidReal = (float) $order->total_paid_real;

        $order->conversion_rate = self::ORDER_CONVERSION_RATE;

        $this->assertTrue(
            $order->update(),
            'Unable to update the fixture order conversion rate'
        );
    }

    protected function tearDown(): void
    {
        $this->deleteTestPayment();

        $order = new Order(self::ORDER_ID);

        if ($order->id) {
            $order->conversion_rate = $this->originalConversionRate;
            $order->total_paid_real = $this->originalTotalPaidReal;
            $order->update();
        }

        parent::tearDown();
    }

    public function testItStoresTheOrderConversionRateWhenNoCurrencyIsGiven(): void
    {
        $order = new Order(self::ORDER_ID);

        $this->assertSame(
            self::ORDER_CONVERSION_RATE,
            (float) $order->conversion_rate,
            'Fixture order should carry a non-default conversion rate'
        );

        $result = $order->addOrderPayment(
            '10.00',
            'qa-test',
            self::TRANSACTION_ID,
            null
        );

        $this->assertTrue(
            $result,
            'The order payment could not be created'
        );

        $storedConversionRate = Db::getInstance()->getValue(
            'SELECT `conversion_rate`
            FROM `' . _DB_PREFIX_ . 'order_payment`
            WHERE `transaction_id` = "' . pSQL(self::TRANSACTION_ID) . '"'
        );

        $this->assertNotFalse(
            $storedConversionRate,
            'The payment row was not created'
        );

        $this->assertSame(
            self::ORDER_CONVERSION_RATE,
            (float) $storedConversionRate,
            'Payment recorded in the order currency must keep the order conversion rate, not the default 1'
        );
    }

    private function deleteTestPayment(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_payment`
            WHERE `transaction_id` = "' . pSQL(self::TRANSACTION_ID) . '"'
        );
    }
}
