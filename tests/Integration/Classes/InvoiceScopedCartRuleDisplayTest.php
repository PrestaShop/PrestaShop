<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Currency;
use Db;
use HTMLTemplateInvoice;
use OrderCartRule;
use OrderInvoice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * A discount added from the order page can be attached to a single invoice, which is stored in
 * order_cart_rule.id_order_invoice. The invoice template listed the discounts of the whole order,
 * so a discount belonging to one invoice was printed on every invoice of that order - displayed
 * there but not counted in that invoice's totals.
 */
class InvoiceScopedCartRuleDisplayTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['order_invoice', 'order_cart_rule'];
    private const ORDER_ID = 1;

    private static int $invoiceA;
    private static int $invoiceB;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        self::bootKernel();

        self::$invoiceA = self::createInvoice();
        self::$invoiceB = self::createInvoice();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'order_cart_rule WHERE id_order = ' . self::ORDER_ID);
    }

    private static function createInvoice(): int
    {
        $invoice = new OrderInvoice();
        $invoice->id_order = self::ORDER_ID;
        $invoice->number = 0;
        $invoice->total_discount_tax_excl = 0;
        $invoice->total_discount_tax_incl = 0;
        $invoice->total_paid_tax_excl = 20.0;
        $invoice->total_paid_tax_incl = 20.0;
        $invoice->total_products = 20.0;
        $invoice->total_products_wt = 20.0;
        $invoice->total_shipping_tax_excl = 2.0;
        $invoice->total_shipping_tax_incl = 2.0;
        $invoice->add();

        return (int) $invoice->id;
    }

    private function addCartRule(int $invoiceId, string $name, float $value, bool $freeShipping = false): void
    {
        $rule = new OrderCartRule();
        $rule->id_order = self::ORDER_ID;
        $rule->id_cart_rule = 0;
        $rule->id_order_invoice = $invoiceId;
        $rule->name = $name;
        $rule->value = $value;
        $rule->value_tax_excl = $value;
        $rule->free_shipping = $freeShipping;
        $rule->add();
    }

    /**
     * The rules the invoice itself reports. What the template prints follows from these, but the
     * two are asserted separately: testTheOtherInvoicesDiscountIsNotPrinted goes through the page.
     *
     * @return string[]
     */
    private function invoiceCartRuleNames(int $invoiceId): array
    {
        $names = [];
        foreach ((new OrderInvoice($invoiceId))->getCartRules() as $rule) {
            $names[] = $rule['name'];
        }

        return $names;
    }

    public function testADiscountAttachedToOneInvoiceIsNotListedOnTheOthers(): void
    {
        $this->addCartRule(self::$invoiceB, 'SECOND_INVOICE_ONLY', 5.0);

        $this->assertSame([], $this->invoiceCartRuleNames(self::$invoiceA));
        $this->assertSame(['SECOND_INVOICE_ONLY'], $this->invoiceCartRuleNames(self::$invoiceB));
    }

    /**
     * A rule with no invoice attached came with the cart, so it belongs to the order as a whole and
     * has to keep appearing on every invoice. Without this the fix would hide the ordinary case.
     */
    public function testADiscountWithNoInvoiceStaysOnEveryInvoice(): void
    {
        $this->addCartRule(0, 'WHOLE_ORDER', 5.0);

        $this->assertSame(['WHOLE_ORDER'], $this->invoiceCartRuleNames(self::$invoiceA));
        $this->assertSame(['WHOLE_ORDER'], $this->invoiceCartRuleNames(self::$invoiceB));
    }

    public function testAnInvoiceWithoutDiscountsListsNone(): void
    {
        $this->assertSame([], $this->invoiceCartRuleNames(self::$invoiceA));
        $this->assertSame([], $this->invoiceCartRuleNames(self::$invoiceB));
    }

    /**
     * End to end through the template: the printed invoice must not carry the other invoice's
     * discount. This is the symptom the report describes, so it is asserted on the rendered page.
     */
    public function testTheOtherInvoicesDiscountIsNotPrinted(): void
    {
        $this->addCartRule(self::$invoiceB, 'SECOND_INVOICE_ONLY', 5.0);

        $this->assertStringNotContainsString('SECOND_INVOICE_ONLY', $this->render(self::$invoiceA));
        $this->assertStringContainsString('SECOND_INVOICE_ONLY', $this->render(self::$invoiceB));
    }

    private function render(int $invoiceId): string
    {
        $context = Context::getContext();
        $context->container = self::$kernel->getContainer();
        // A printed invoice needs a currency to resolve its computing precision.
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        smartyRegisterFunction($context->smarty, 'function', 'displayPrice', ['Tools', 'displayPriceSmarty']);

        $template = new HTMLTemplateInvoice(new OrderInvoice($invoiceId), $context->smarty);

        $previousHandler = set_error_handler(static fn () => true);
        try {
            return $template->getContent();
        } finally {
            set_error_handler($previousHandler);
        }
    }
}
