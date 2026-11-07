<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers;

use AdminStatsController;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class AdminStatsControllerTest extends TestCase
{
    private const DAY = '2019-03-04';
    private const NO_INVOICE = '0000-00-00 00:00:00';
    private const PAYMENT_ACCEPTED = 2;
    private const AWAITING_CHEQUE_PAYMENT = 1;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once _PS_ROOT_DIR_ . '/controllers/admin/AdminStatsController.php';
    }

    protected function tearDown(): void
    {
        Configuration::deleteByName('CONF_ORDER_FIXED');
        DatabaseDump::restoreTables(['orders', 'order_detail']);
        parent::tearDown();
    }

    public function testAValidOrderWithoutInvoiceIsCountedOnItsCreationDay(): void
    {
        $this->insertOrder(self::NO_INVOICE);

        $this->assertSame(1, $this->countOrdersOnTheDay());
    }

    public function testAnInvoicedOrderIsCountedOnItsInvoiceDay(): void
    {
        $this->insertOrder(self::DAY . ' 10:00:00', self::PAYMENT_ACCEPTED, '2019-03-03 10:00:00');

        $this->assertSame(1, $this->countOrdersOnTheDay());
    }

    public function testExpensesIncludeAValidOrderWithoutInvoice(): void
    {
        Configuration::updateValue('CONF_ORDER_FIXED', 5);
        $this->insertOrder(self::NO_INVOICE);

        $this->assertEquals(5, AdminStatsController::getExpenses(self::DAY, self::DAY));
    }

    public function testMainCountryCountsOnlyValidOrdersWithoutInvoice(): void
    {
        $this->insertOrder(self::NO_INVOICE);
        $this->insertOrder(self::NO_INVOICE, self::AWAITING_CHEQUE_PAYMENT);

        $mainCountry = AdminStatsController::getMainCountry(self::DAY, self::DAY);

        $this->assertIsArray($mainCountry);
        $this->assertEquals(100, $mainCountry['orders']);
    }

    public function testBestCategoryCountsAnOrderWithoutInvoice(): void
    {
        // Without any sale on the day, the first category is returned
        $categoryWithoutSales = (int) AdminStatsController::getBestCategory(self::DAY, self::DAY);
        $productId = (int) Db::getInstance()->getValue(
            'SELECT cp.id_product FROM ' . _DB_PREFIX_ . 'category_product cp
             WHERE cp.id_product NOT IN (
                SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category = ' . $categoryWithoutSales . '
             )
             ORDER BY cp.id_product'
        );
        $productCategories = array_map('intval', array_column(Db::getInstance()->executeS(
            'SELECT cp.id_category FROM ' . _DB_PREFIX_ . 'category_product cp
             INNER JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category = cp.id_category
             WHERE c.level_depth > 1 AND cp.id_product = ' . $productId
        ), 'id_category'));
        $orderId = $this->insertOrder(self::NO_INVOICE);
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'order_detail
                (id_order, id_order_invoice, id_warehouse, id_shop, product_id, product_attribute_id, product_name, product_quantity,
                 product_price, unit_price_tax_excl, unit_price_tax_incl, total_price_tax_excl, total_price_tax_incl)
             VALUES (' . $orderId . ', 0, 0, 1, ' . $productId . ', 0, "QA", 1, 10, 10, 10, 10, 10)'
        );

        $this->assertNotEmpty($productCategories);
        $this->assertContains((int) AdminStatsController::getBestCategory(self::DAY, self::DAY), $productCategories);
    }

    private function countOrdersOnTheDay(): int
    {
        return array_sum(array_map('intval', (array) AdminStatsController::getOrders(self::DAY, self::DAY, 'day')));
    }

    private function insertOrder(string $invoiceDate, int $stateId = self::PAYMENT_ACCEPTED, ?string $dateAdd = null): int
    {
        $valid = (int) Db::getInstance()->getValue(
            'SELECT logable FROM ' . _DB_PREFIX_ . 'order_state WHERE id_order_state = ' . $stateId
        );

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'orders
                (reference, id_shop_group, id_shop, id_customer, id_cart, id_currency, id_lang, id_address_delivery, id_address_invoice, current_state,
                 valid, payment, module, conversion_rate, total_paid, total_paid_real, total_paid_tax_excl, total_paid_tax_incl, total_products, total_products_wt,
                 total_shipping, total_shipping_tax_excl, total_shipping_tax_incl, invoice_date, delivery_date, date_add, date_upd)
             VALUES ("QASTATS", 1, 1, 1, 0, 1, 1, 1, 1, ' . $stateId . ', ' . $valid . ', "QA", "ps_checkpayment", 1, 10, 10, 10, 10, 10, 10, 0, 0, 0,
                 "' . pSQL($invoiceDate) . '", "0000-00-00 00:00:00", "' . pSQL($dateAdd ?? (self::DAY . ' 09:00:00')) . '", NOW())'
        );

        return (int) Db::getInstance()->Insert_ID();
    }
}
