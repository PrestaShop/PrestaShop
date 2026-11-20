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
use Language;
use Order;
use OrderInvoice;
use OrderPayment;
use PHPUnit\Framework\TestCase;
use Shop;
use Tools;

/**
 * A cart split across several carriers becomes several orders sharing one reference. Marking one of them
 * paid links its payment to the next one's invoice, because Order::setInvoice() looks payments up by
 * reference - so the amount already received was subtracted from an invoice it had not paid for.
 */
class SplitOrderPaymentTest extends TestCase
{
    private string $reference = '';

    /** @var int[] */
    private array $orderIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $context = Context::getContext();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->shop = new Shop(1);

        $this->reference = 'SPL' . substr((string) microtime(true), -6);
    }

    protected function tearDown(): void
    {
        foreach ($this->orderIds as $idOrder) {
            Db::getInstance()->delete('order_invoice_payment', 'id_order = ' . $idOrder);
            Db::getInstance()->delete('order_invoice', 'id_order = ' . $idOrder);
            Db::getInstance()->delete('orders', 'id_order = ' . $idOrder);
        }
        Db::getInstance()->delete('order_payment', "order_reference = '" . pSQL(Tools::substr($this->reference, 0, 9)) . "'");
        $this->orderIds = [];
        Cache::clean('order_invoice_paid_*');

        parent::tearDown();
    }

    /**
     * @dataProvider sequenceProvider
     */
    public function testEachOrderOfASplitCartRecordsItsOwnAmount(float $first, float $second): void
    {
        $orderA = $this->createOrder($first);
        $orderB = $this->createOrder($second);

        $paidForA = $this->markPaid($orderA);
        $paidForB = $this->markPaid($orderB);

        $this->assertSame([$first], $paidForA);
        $this->assertSame([$second], $paidForB, 'the second order records its own total, not what is left of the first');
        $this->assertSame($first + $second, $this->totalRecorded());
    }

    /**
     * @return array<string, float[]>
     */
    public function sequenceProvider(): array
    {
        return [
            // The report's asymmetry: paying the larger order first left the smaller one with nothing to
            // record, because its whole total had already been subtracted.
            'larger order paid first' => [100.00, 40.00],
            'smaller order paid first' => [40.00, 100.00],
        ];
    }

    /**
     * The case the sibling total exists for: one payment covering several invoices of one order still
     * leaves nothing outstanding on either of them.
     */
    public function testOnePaymentCoveringSeveralInvoicesOfOneOrderLeavesNothingOutstanding(): void
    {
        $order = $this->createOrder(140.00);
        $invoices = [$this->createInvoice((int) $order->id, 100.00), $this->createInvoice((int) $order->id, 40.00)];

        $payment = $this->createPayment($order, 140.00);
        foreach ($invoices as $idInvoice) {
            Db::getInstance()->insert('order_invoice_payment', [
                'id_order_invoice' => $idInvoice,
                'id_order_payment' => (int) $payment->id,
                'id_order' => (int) $order->id,
            ]);
        }
        Cache::clean('order_invoice_paid_*');

        foreach ($invoices as $idInvoice) {
            $this->assertSame(0.0, (new OrderInvoice($idInvoice))->getRestPaid());
        }
    }

    /**
     * Replays the paid branch of OrderHistory::changeIdOrderState(): the invoice is created with the
     * existing payments of the reference, then a payment is recorded for whatever is still outstanding.
     *
     * @return float[] the amounts recorded
     */
    private function markPaid(Order $order): array
    {
        $order->setInvoice(true);
        Cache::clean('order_invoice_paid_*');

        $recorded = [];
        foreach ($order->getInvoicesCollection() as $invoice) {
            /** @var OrderInvoice $invoice */
            $restPaid = $invoice->getRestPaid();
            if ($restPaid > 0) {
                $payment = $this->createPayment($order, $restPaid);
                Db::getInstance()->insert('order_invoice_payment', [
                    'id_order_invoice' => (int) $invoice->id,
                    'id_order_payment' => (int) $payment->id,
                    'id_order' => (int) $order->id,
                ]);
                Cache::clean('order_invoice_paid_*');
                $recorded[] = $restPaid;
            }
        }

        return $recorded;
    }

    private function createOrder(float $total): Order
    {
        $source = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'orders ORDER BY id_order ASC');
        $order = new Order();
        foreach ($source as $field => $value) {
            if ('id_order' !== $field && property_exists($order, $field)) {
                $order->$field = $value;
            }
        }
        $order->id = null;
        $order->reference = $this->reference;
        $order->total_paid = $total;
        $order->total_paid_tax_incl = $total;
        $order->total_paid_tax_excl = $total;
        $order->total_paid_real = 0;
        $order->total_products = $total;
        $order->total_products_wt = $total;
        $order->valid = true;
        $order->add();
        $this->orderIds[] = (int) $order->id;

        return $order;
    }

    private function createInvoice(int $idOrder, float $total): int
    {
        Db::getInstance()->insert('order_invoice', [
            'id_order' => $idOrder,
            'number' => 0,
            'delivery_number' => 0,
            'total_discount_tax_excl' => 0,
            'total_discount_tax_incl' => 0,
            'total_paid_tax_excl' => $total,
            'total_paid_tax_incl' => $total,
            'total_products' => $total,
            'total_products_wt' => $total,
            'total_shipping_tax_excl' => 0,
            'total_shipping_tax_incl' => 0,
            'shipping_tax_computation_method' => 0,
            'total_wrapping_tax_excl' => 0,
            'total_wrapping_tax_incl' => 0,
            'date_add' => date('Y-m-d H:i:s'),
        ]);

        return (int) Db::getInstance()->Insert_ID();
    }

    private function createPayment(Order $order, float $amount): OrderPayment
    {
        $payment = new OrderPayment();
        $payment->order_reference = Tools::substr($order->reference, 0, 9);
        $payment->id_currency = (int) $order->id_currency;
        $payment->amount = $amount;
        $payment->conversion_rate = 1;
        $payment->save();

        return $payment;
    }

    private function totalRecorded(): float
    {
        return (float) Db::getInstance()->getValue(
            'SELECT COALESCE(SUM(amount), 0) FROM ' . _DB_PREFIX_ . 'order_payment
             WHERE order_reference = "' . pSQL(Tools::substr($this->reference, 0, 9)) . '"'
        );
    }
}
