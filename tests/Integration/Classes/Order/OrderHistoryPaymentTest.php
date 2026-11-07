<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Cache;
use Configuration;
use Context;
use Currency;
use Db;
use Employee;
use Language;
use Order;
use OrderHistory;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

/**
 * @see https://github.com/PrestaShop/PrestaShop/issues/38103
 */
class OrderHistoryPaymentTest extends TestCase
{
    private int $orderId;

    private int $paidStateId;

    /**
     * @var array{currency: mixed, language: mixed, employee: mixed}
     */
    private array $contextBackup;

    private const TABLES_TO_RESTORE = [
        'configuration',
        'order_state',
        'orders',
        'order_history',
        'order_payment',
        'order_invoice',
        'order_invoice_payment',
        'order_invoice_tax',
        'order_detail',
        'order_cart_rule',
        'order_carrier',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $context = Context::getContext();
        $this->contextBackup = ['currency' => $context->currency, 'language' => $context->language, 'employee' => $context->employee];
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->employee = new Employee((int) Db::getInstance()->getValue(
            'SELECT id_employee FROM ' . _DB_PREFIX_ . 'employee WHERE id_profile = ' . _PS_ADMIN_PROFILE_
        ));

        $this->paidStateId = (int) Configuration::get('PS_OS_PAYMENT');
        $this->orderId = (int) Db::getInstance()->getValue(
            'SELECT o.id_order FROM ' . _DB_PREFIX_ . 'orders o
             LEFT JOIN ' . _DB_PREFIX_ . 'order_invoice oi ON oi.id_order = o.id_order
             WHERE oi.id_order IS NULL AND o.total_paid > 0 ORDER BY o.id_order'
        );

        // The paid status does not issue an invoice
        Db::getInstance()->update('order_state', ['invoice' => 0], 'id_order_state = ' . $this->paidStateId);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Configuration::resetStaticCache();
        $context = Context::getContext();
        $context->currency = $this->contextBackup['currency'];
        $context->language = $this->contextBackup['language'];
        $context->employee = $this->contextBackup['employee'];
        Cache::clean('objectmodel_Order_*');
        Cache::clean('order_invoice_paid_*');

        parent::tearDown();
    }

    public function testAnOrderPaidWithoutAnInvoiceStillRecordsItsPayment(): void
    {
        $order = new Order($this->orderId);

        $this->assertCount(0, $order->getInvoicesCollection(), 'The fixture order must start without an invoice.');
        $this->assertSame(0, $this->countPayments($order->reference));

        $this->moveToPaidStatus();

        $this->assertSame(1, $this->countPayments($order->reference));
        $this->assertEqualsWithDelta(
            (float) $order->total_paid_tax_incl,
            (float) Db::getInstance()->getValue(
                'SELECT amount FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference = "' . pSQL($order->reference) . '"'
            ),
            0.01
        );
    }

    public function testTheOrderIsReportedAsPaidForReal(): void
    {
        $order = new Order($this->orderId);

        $this->moveToPaidStatus();

        $this->assertEqualsWithDelta(
            (float) $order->total_paid_tax_incl,
            (float) Db::getInstance()->getValue(
                'SELECT total_paid_real FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
            ),
            0.01
        );
    }

    public function testNoInvoiceLinkIsWrittenWhenThereIsNoInvoice(): void
    {
        $this->moveToPaidStatus();

        $this->assertSame(0, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_invoice_payment WHERE id_order = ' . $this->orderId
        ));
    }

    public function testAnInvoiceIssuedAfterThePaymentDoesNotRecordItAgain(): void
    {
        $order = new Order($this->orderId);
        $this->moveToPaidStatus();

        // A later paid status that does issue the invoice, set the way the webservice and two-argument
        // callers do it: without asking to reuse existing payments.
        $this->changeState($this->getInvoicingPaidStateId(), $this->orderId);

        $invoiceTotal = (float) Db::getInstance()->getValue(
            'SELECT total_paid_tax_incl FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order = ' . $this->orderId
        );
        $this->assertGreaterThan(0, $invoiceTotal, 'The second status must have issued the invoice.');

        // The invoice is settled once: what was paid before it existed counts towards it
        $this->assertEqualsWithDelta($invoiceTotal, (float) Db::getInstance()->getValue(
            'SELECT SUM(amount) FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference = "' . pSQL($order->reference) . '"'
        ), 0.01);
        $this->assertEqualsWithDelta($invoiceTotal, (float) Db::getInstance()->getValue(
            'SELECT total_paid_real FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
        ), 0.01);
        $this->assertSame(0, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_payment op
             LEFT JOIN ' . _DB_PREFIX_ . 'order_invoice_payment oip ON oip.id_order_payment = op.id_order_payment
             WHERE oip.id_order_payment IS NULL AND op.order_reference = "' . pSQL($order->reference) . '"'
        ), 'The payment recorded without an invoice belongs to the invoice issued afterwards.');
    }

    public function testThePaymentKeepsTheWholeOrderReference(): void
    {
        // A reference can be longer than 9 characters since 9.0 (a module, actionGenerateDocumentReference),
        // and payments are found back by it.
        $originalReference = (string) Db::getInstance()->getValue(
            'SELECT reference FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
        );
        Db::getInstance()->update('orders', ['reference' => 'ORD20261234'], 'id_order = ' . $this->orderId);

        try {
            $this->moveToPaidStatus();

            $this->assertSame(1, $this->countPayments('ORD20261234'));
        } finally {
            Db::getInstance()->delete('order_payment', 'order_reference LIKE "ORD202612%"');
            Db::getInstance()->update('orders', ['reference' => pSQL($originalReference)], 'id_order = ' . $this->orderId);
        }
    }

    public function testASplitCartRecordsThePaymentOfEveryOrderOnce(): void
    {
        $secondOrderId = $this->splitTheCart();

        $this->changeState($this->paidStateId, $this->orderId);
        $this->changeState($this->paidStateId, $secondOrderId);

        $this->assertEqualsWithDelta($this->ordersTotal($secondOrderId), $this->paymentsTotal(), 0.01);
    }

    public function testASplitCartInvoicedAfterThePaymentDoesNotRecordItAgain(): void
    {
        $secondOrderId = $this->splitTheCart();

        $this->changeState($this->paidStateId, $this->orderId);
        $this->changeState($this->paidStateId, $secondOrderId);
        $this->changeState($this->getInvoicingPaidStateId(), $this->orderId);
        $this->changeState($this->getInvoicingPaidStateId(), $secondOrderId);

        $this->assertEqualsWithDelta($this->ordersTotal($secondOrderId), $this->paymentsTotal(), 0.01);
        // The invoice of the second order is linked to the payment that covers it, so it is not offered for payment
        $this->assertGreaterThan(0, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_invoice_payment WHERE id_order = ' . $secondOrderId
        ));
    }

    public function testASplitCartInvoicedOnlyForOneOrderBeforeThePayment(): void
    {
        $secondOrderId = $this->splitTheCart();

        $this->changeState($this->paidStateId, $this->orderId);
        $this->changeState($this->getInvoicingPaidStateId(), $this->orderId);
        $this->changeState($this->getInvoicingPaidStateId(), $secondOrderId);

        $this->assertEqualsWithDelta($this->ordersTotal($secondOrderId), $this->paymentsTotal(), 0.01);
        // The invoice of the second order is linked to the payment that covers it, so it is not offered for payment
        $this->assertGreaterThan(0, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_invoice_payment WHERE id_order = ' . $secondOrderId
        ));
    }

    public function testASplitCartInvoicedWhenPaidRecordsEveryInvoice(): void
    {
        $secondOrderId = $this->splitTheCart();

        $this->changeState($this->getInvoicingPaidStateId(), $this->orderId);
        $this->changeState($this->getInvoicingPaidStateId(), $secondOrderId);

        $this->assertEqualsWithDelta($this->ordersTotal($secondOrderId), $this->paymentsTotal(), 0.01);
        $this->assertSame(2, $this->countPayments((new Order($this->orderId))->reference));
    }

    /**
     * Gives a second order without invoice the reference and cart of the fixture order.
     */
    private function splitTheCart(): int
    {
        $order = new Order($this->orderId);
        $secondOrderId = (int) Db::getInstance()->getValue(
            'SELECT o.id_order FROM ' . _DB_PREFIX_ . 'orders o
             LEFT JOIN ' . _DB_PREFIX_ . 'order_invoice oi ON oi.id_order = o.id_order
             WHERE oi.id_order IS NULL AND o.total_paid > 0 AND o.id_order != ' . $this->orderId . ' ORDER BY o.id_order'
        );
        Db::getInstance()->update(
            'orders',
            ['reference' => pSQL($order->reference), 'id_cart' => (int) $order->id_cart],
            'id_order = ' . $secondOrderId
        );
        Cache::clean('objectmodel_Order_*');

        return $secondOrderId;
    }

    private function getInvoicingPaidStateId(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state
             WHERE paid = 1 AND invoice = 1 AND deleted = 0 AND id_order_state != ' . $this->paidStateId . '
             ORDER BY id_order_state'
        );
    }

    private function changeState(int $stateId, int $orderId): void
    {
        $history = new OrderHistory();
        $history->id_order = $orderId;
        $history->changeIdOrderState($stateId, $orderId);
        $history->add();
    }

    private function ordersTotal(int $secondOrderId): float
    {
        return round((float) Db::getInstance()->getValue(
            'SELECT SUM(total_paid_tax_incl) FROM ' . _DB_PREFIX_ . 'orders WHERE id_order IN (' . $this->orderId . ', ' . $secondOrderId . ')'
        ), 2);
    }

    private function paymentsTotal(): float
    {
        return (float) Db::getInstance()->getValue(
            'SELECT SUM(amount) FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference = "' . pSQL((new Order($this->orderId))->reference) . '"'
        );
    }

    private function moveToPaidStatus(): void
    {
        $this->changeState($this->paidStateId, $this->orderId);
    }

    private function countPayments(string $reference): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference = "' . pSQL($reference) . '"'
        );
    }
}
