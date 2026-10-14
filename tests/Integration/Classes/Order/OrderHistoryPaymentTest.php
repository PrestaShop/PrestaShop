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

/**
 * The payment used to be created inside a loop over the order's invoices, so an order reaching a
 * paid status without ever being invoiced recorded nothing at all. A status can carry `paid` without
 * carrying `invoice`, which is what a shop that delegates invoicing to another system does.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38103
 */
class OrderHistoryPaymentTest extends TestCase
{
    private int $orderId;

    private int $paidStateId;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $historyBackup = [];

    private string $stateInvoiceFlag;

    private string $currentState;

    /**
     * @var string|false
     */
    private $invoiceStartNumber;

    protected function setUp(): void
    {
        parent::setUp();

        $context = Context::getContext();
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

        $this->stateInvoiceFlag = (string) Db::getInstance()->getValue(
            'SELECT invoice FROM ' . _DB_PREFIX_ . 'order_state WHERE id_order_state = ' . $this->paidStateId
        );
        $this->currentState = (string) Db::getInstance()->getValue(
            'SELECT current_state FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
        );
        $this->historyBackup = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order = ' . $this->orderId
        ) ?: [];

        // Issuing an invoice consumes this setting, see Order::setLastInvoiceNumber().
        $this->invoiceStartNumber = Configuration::get('PS_INVOICE_START_NUMBER', null, null, (int) Db::getInstance()->getValue(
            'SELECT id_shop FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
        ));

        // The paid status no longer issues an invoice, the shop invoices elsewhere.
        Db::getInstance()->update('order_state', ['invoice' => 0], 'id_order_state = ' . $this->paidStateId);
    }

    protected function tearDown(): void
    {
        $reference = (string) Db::getInstance()->getValue(
            'SELECT reference FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
        );

        Db::getInstance()->update('order_state', ['invoice' => (int) $this->stateInvoiceFlag], 'id_order_state = ' . $this->paidStateId);
        Db::getInstance()->delete('order_payment', 'order_reference = "' . pSQL($reference) . '"');
        Db::getInstance()->delete('order_invoice_payment', 'id_order = ' . $this->orderId);
        Db::getInstance()->delete('order_invoice_tax', 'id_order_invoice IN (SELECT id_order_invoice FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order = ' . $this->orderId . ')');
        Db::getInstance()->delete('order_invoice', 'id_order = ' . $this->orderId);
        Db::getInstance()->update('order_detail', ['id_order_invoice' => 0], 'id_order = ' . $this->orderId);
        Db::getInstance()->update('order_cart_rule', ['id_order_invoice' => 0], 'id_order = ' . $this->orderId);
        Db::getInstance()->update('order_carrier', ['id_order_invoice' => 0], 'id_order = ' . $this->orderId);
        if ($this->invoiceStartNumber) {
            $idShop = (int) Db::getInstance()->getValue('SELECT id_shop FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId);
            Configuration::updateValue('PS_INVOICE_START_NUMBER', $this->invoiceStartNumber, false, null, $idShop);
        }
        Db::getInstance()->delete('order_history', 'id_order = ' . $this->orderId);
        foreach ($this->historyBackup as $row) {
            Db::getInstance()->insert('order_history', $row, false, true, Db::INSERT_IGNORE);
        }
        Db::getInstance()->update(
            'orders',
            ['current_state' => (int) $this->currentState, 'total_paid_real' => 0, 'invoice_number' => 0, 'invoice_date' => '0000-00-00 00:00:00'],
            'id_order = ' . $this->orderId
        );

        // The rows above were reset with SQL, so an Order cached during the test must not outlive it.
        Cache::clean('objectmodel_Order_' . $this->orderId . '_*');

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
            (float) $order->total_paid,
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
            (float) $order->total_paid,
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
        $invoicingPaidState = (int) Db::getInstance()->getValue(
            'SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state
             WHERE paid = 1 AND invoice = 1 AND deleted = 0 AND id_order_state != ' . $this->paidStateId . '
             ORDER BY id_order_state'
        );
        $history = new OrderHistory();
        $history->id_order = $this->orderId;
        $history->changeIdOrderState($invoicingPaidState, $this->orderId);
        $history->add();

        $invoiceTotal = (float) Db::getInstance()->getValue(
            'SELECT total_paid_tax_incl FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order = ' . $this->orderId
        );
        $this->assertGreaterThan(0, $invoiceTotal, 'The second status must have issued the invoice.');

        // The invoice is settled once: what was paid before it existed counts towards it, and only what it
        // still shows as due is recorded on top (the fixture invoice and order totals differ slightly).
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

    private function moveToPaidStatus(): void
    {
        $history = new OrderHistory();
        $history->id_order = $this->orderId;
        $history->changeIdOrderState($this->paidStateId, $this->orderId);
        $history->add();
    }

    private function countPayments(string $reference): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference = "' . pSQL($reference) . '"'
        );
    }
}
