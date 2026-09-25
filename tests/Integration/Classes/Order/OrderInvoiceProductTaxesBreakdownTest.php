<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Configuration;
use Context;
use Currency;
use Order;
use OrderInvoice;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * The tax breakdown of an invoice is built from the details of the whole order, so it has to keep
 * only the rows belonging to the invoice it is rendering. An order can carry several invoices, which
 * is what the back office does when a product is added "with a new invoice".
 */
class OrderInvoiceProductTaxesBreakdownTest extends TestCase
{
    private const FIRST_INVOICE_ID = 1;
    private const SECOND_INVOICE_ID = 2;

    /**
     * @var Currency|null
     */
    private $previousCurrency;

    /**
     * The breakdown rounds with Context::getComputingPrecision(), which reads the currency precision.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $context = Context::getContext();
        $this->previousCurrency = $context->currency;
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->resetComputingPrecision($context);
    }

    protected function tearDown(): void
    {
        $context = Context::getContext();
        $context->currency = $this->previousCurrency;
        $this->resetComputingPrecision($context);

        parent::tearDown();
    }

    /**
     * Taxes applied one after another take the branch that does not group the details, which is the
     * branch the invoice filter was missing from.
     */
    public function testBreakdownIgnoresAnotherInvoiceRowsWhenTaxesAreAppliedOneAfterAnother(): void
    {
        $invoice = $this->buildInvoice(self::SECOND_INVOICE_ID, true);

        $breakdown = $invoice->getProductTaxesBreakdown($this->buildOrder());

        $this->assertSame(['20.000'], array_keys($breakdown));
        $this->assertEquals(100.0, $breakdown['20.000']['total_price_tax_excl']);
        $this->assertEquals(20.0, $breakdown['20.000']['total_amount']);
    }

    /**
     * The grouping branch already filtered by invoice; this pins that it still does.
     */
    public function testBreakdownIgnoresAnotherInvoiceRowsWhenCompositeTaxesAreSummed(): void
    {
        $invoice = $this->buildInvoice(self::SECOND_INVOICE_ID, false);

        $breakdown = $invoice->getProductTaxesBreakdown($this->buildOrder());

        $this->assertSame(['20.000'], array_keys($breakdown));
        $this->assertEquals(100.0, $breakdown['20.000']['total_price_tax_excl']);
        $this->assertEquals(20.0, $breakdown['20.000']['total_amount']);
    }

    /**
     * The first invoice must not pick up the second one either, so the filter is not just "skip
     * everything before me".
     */
    public function testBreakdownOfTheFirstInvoiceIgnoresTheSecondOne(): void
    {
        $invoice = $this->buildInvoice(self::FIRST_INVOICE_ID, true);

        $breakdown = $invoice->getProductTaxesBreakdown($this->buildOrder());

        $this->assertSame(['20.000'], array_keys($breakdown));
        $this->assertEquals(50.0, $breakdown['20.000']['total_price_tax_excl']);
        $this->assertEquals(10.0, $breakdown['20.000']['total_amount']);
    }

    /**
     * The precision is memoised on the context, so a value cached from another currency would survive
     * the assignment above.
     */
    private function resetComputingPrecision(Context $context): void
    {
        $property = (new ReflectionClass(Context::class))->getProperty('priceComputingPrecision');
        $property->setAccessible(true);
        $property->setValue($context, null);
    }

    private function buildInvoice(int $invoiceId, bool $oneAfterAnother): OrderInvoice
    {
        $invoice = new TestableOrderInvoice();
        $invoice->id = $invoiceId;
        $invoice->oneAfterAnother = $oneAfterAnother;

        return $invoice;
    }

    /**
     * One order, two invoices, one product line each, both taxed at the same rate so that a leaked
     * row shows up as an inflated base rather than as an extra rate.
     */
    private function buildOrder(): Order
    {
        $order = $this->createPartialMock(Order::class, ['getProductTaxesDetails']);
        $order->round_mode = 0;
        $order->method('getProductTaxesDetails')->willReturn([
            [
                'id_order_detail' => 1,
                'id_order_invoice' => self::FIRST_INVOICE_ID,
                'id_tax' => 1,
                'tax_rate' => 20.0,
                'total_tax_base' => 50.0,
                'total_amount' => 10.0,
            ],
            [
                'id_order_detail' => 2,
                'id_order_invoice' => self::SECOND_INVOICE_ID,
                'id_tax' => 1,
                'tax_rate' => 20.0,
                'total_tax_base' => 100.0,
                'total_amount' => 20.0,
            ],
        ]);

        return $order;
    }
}

class TestableOrderInvoice extends OrderInvoice
{
    /**
     * @var bool
     */
    public $oneAfterAnother = false;

    public function useOneAfterAnotherTaxComputationMethod()
    {
        return $this->oneAfterAnother;
    }
}
