<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Stats;

use AdminStatsController;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

/**
 * An order only gets an invoice date when its status issues invoices. Every statistic used to be
 * restricted to that column, so turning invoices off for a status hid the order from all of them.
 */
class StatsWithoutInvoiceTest extends TestCase
{
    private const DAY = '2019-03-04';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once _PS_ROOT_DIR_ . '/controllers/admin/AdminStatsController.php';
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['orders']);

        parent::tearDown();
    }

    public function testAnOrderWithoutAnInvoiceDateIsCountedOnItsCreationDay(): void
    {
        $this->insertOrder('0000-00-00 00:00:00');

        $this->assertSame(1, $this->countOrdersOnTheDay());
    }

    public function testAnOrderWithAnInvoiceDateIsStillCountedOnTheInvoiceDay(): void
    {
        // Created the day before, invoiced on the day under test: it belongs to the invoice day.
        $this->insertOrder(self::DAY . ' 10:00:00', '2019-03-03 10:00:00');

        $this->assertSame(1, $this->countOrdersOnTheDay());
    }

    private function countOrdersOnTheDay(): int
    {
        return array_sum(array_map('intval', (array) AdminStatsController::getOrders(self::DAY, self::DAY, 'day')));
    }

    private function insertOrder(string $invoiceDate, ?string $dateAdd = null): void
    {
        $logableState = (int) Db::getInstance()->getValue('SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state WHERE logable = 1');
        $reference = 'QASTATS';

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'orders
                (reference, id_shop_group, id_shop, id_customer, id_cart, id_currency, id_lang, id_address_delivery, id_address_invoice, current_state,
                 payment, conversion_rate, total_paid, total_paid_real, total_paid_tax_excl, total_paid_tax_incl, total_products, total_products_wt,
                 total_shipping, total_shipping_tax_excl, total_shipping_tax_incl, invoice_date, delivery_date, date_add, date_upd)
             VALUES ("' . $reference . '", 1, 1, 1, 0, 1, 1, 1, 1, ' . $logableState . ',
                 "QA", 1, 10, 10, 10, 10, 10, 10, 0, 0, 0,
                 "' . pSQL($invoiceDate) . '", "0000-00-00 00:00:00", "' . pSQL($dateAdd ?? (self::DAY . ' 09:00:00')) . '", NOW())'
        );
    }
}
