<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Context;
use Currency;
use Order;
use OrderInvoice;
use OrderPayment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Tools;

class OrderDeliverySlipPaymentTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['orders', 'order_payment', 'order_invoice', 'order_invoice_payment'];

    private const ORDER_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        // getTotalPaid() memoizes per invoice id; clear it so a restored invoice id is not read
        // from a previous test's cache.
        Cache::clean('order_invoice_paid_*');
        self::bootKernel();

        $order = new Order(self::ORDER_ID);
        // setInvoiceDetails() needs the currency precision from the context.
        Context::getContext()->currency = new Currency((int) $order->id_currency);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDown();
    }

    public function testDeliverySlipAssociatesExistingPaymentSoNoDuplicateIsRecorded(): void
    {
        // Regression test for #41936: generating a delivery slip on an already-paid order (that has
        // no invoice) used to leave the generated invoice unpaid, so the paid-status block recorded
        // a duplicate payment. The existing payment must now be associated with the invoice.
        $order = new Order(self::ORDER_ID);
        $this->addPaymentToOrder($order, (float) $order->total_paid_tax_incl);

        $order->setDeliverySlip();

        $invoices = (new Order(self::ORDER_ID))->getInvoicesCollection();
        $this->assertCount(1, $invoices);
        /** @var OrderInvoice $invoice */
        $invoice = $invoices->getFirst();
        // Nothing left to pay -> the paid-status block would not create another payment.
        $this->assertEqualsWithDelta(0, (new OrderInvoice((int) $invoice->id))->getRestPaid(), 0.001);
    }

    public function testDeliverySlipOnAnUnpaidOrderLeavesTheInvoiceUnpaid(): void
    {
        // The fix must not suppress a legitimate payment: with no payment yet, the generated invoice
        // stays unpaid so the paid-status block still records the (single) real payment.
        $order = new Order(self::ORDER_ID);

        $order->setDeliverySlip();

        $reloaded = new Order(self::ORDER_ID);
        $invoices = $reloaded->getInvoicesCollection();
        $this->assertCount(1, $invoices);
        /** @var OrderInvoice $invoice */
        $invoice = $invoices->getFirst();
        $this->assertEqualsWithDelta(
            (float) $reloaded->total_paid_tax_incl,
            (new OrderInvoice((int) $invoice->id))->getRestPaid(),
            0.001
        );
    }

    private function addPaymentToOrder(Order $order, float $amount): void
    {
        $payment = new OrderPayment();
        $payment->order_reference = Tools::substr($order->reference, 0, 9);
        $payment->id_currency = (int) $order->id_currency;
        $payment->amount = $amount;
        $payment->payment_method = 'Test payment';
        $payment->conversion_rate = $order->conversion_rate ?: 1;
        $payment->save();
    }
}
