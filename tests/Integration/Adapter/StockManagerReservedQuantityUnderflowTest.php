<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Adapter\StockManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Recomputing the reserved stock quantity must not crash when an order line has been refunded for
 * more units than were ordered. product_quantity and product_quantity_refunded are UNSIGNED, so the
 * legacy SUM(product_quantity - product_quantity_refunded) underflowed and MySQL aborted the update
 * with "BIGINT UNSIGNED value is out of range", which stopped orders from being processed.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37338
 */
class StockManagerReservedQuantityUnderflowTest extends KernelTestCase
{
    private const PRODUCT_ID = 999401;

    private int $orderId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();
        // A stock_available row whose reserved quantity will be recomputed.
        // Stock is not shared at group level on a standard install, so stock_available rows are stored
        // with id_shop_group = 0 (the "not group-scoped" sentinel getStockContext() looks for).
        $db->insert('stock_available', [
            'id_product' => self::PRODUCT_ID,
            'id_product_attribute' => 0,
            'id_shop' => 1,
            'id_shop_group' => 0,
            'quantity' => 10,
            'physical_quantity' => 10,
            'reserved_quantity' => 99,
        ]);

        // A valid, unshipped order...
        $db->insert('orders', [
            'id_address_delivery' => 1,
            'id_address_invoice' => 1,
            'id_cart' => 999401,
            'id_currency' => 1,
            'id_shop' => 1,
            'id_shop_group' => 1,
            'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'id_customer' => 1,
            'id_carrier' => 1,
            'current_state' => (int) Configuration::get('PS_OS_PAYMENT'),
            'payment' => 'Test',
            'valid' => 1,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
            'invoice_date' => '0000-00-00 00:00:00',
            'delivery_date' => '0000-00-00 00:00:00',
        ]);
        $this->orderId = (int) $db->Insert_ID();

        // ...whose line was refunded for more units than were ordered (1 ordered, 5 refunded).
        $db->insert('order_detail', [
            'id_order' => $this->orderId,
            'id_shop' => 1,
            'product_id' => self::PRODUCT_ID,
            'product_attribute_id' => 0,
            'product_name' => 'Underflow fixture',
            'product_quantity' => 1,
            'product_quantity_refunded' => 5,
            'product_weight' => 0,
            'tax_name' => '',
        ]);
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        if ($this->orderId) {
            $db->delete('order_detail', 'id_order = ' . $this->orderId);
            $db->delete('orders', 'id_order = ' . $this->orderId);
        }
        $db->delete('stock_available', 'id_product = ' . self::PRODUCT_ID);
        parent::tearDown();
    }

    public function testReservedQuantityRecomputeDoesNotUnderflowOnOverRefundedLine(): void
    {
        $stockManager = new StockManager();

        // Scoped to our product so only its stock_available row is recomputed. Before the fix this
        // threw a PDOException (SQLSTATE 22003, BIGINT UNSIGNED out of range).
        $result = $stockManager->updatePhysicalProductQuantity(
            1,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            self::PRODUCT_ID
        );

        $this->assertTrue((bool) $result, 'Reserved quantity recompute must complete without a SQL underflow');

        // The over-refunded line contributes 0 reserved units (max(1 - 5, 0)).
        $reserved = Db::getInstance()->getValue(
            'SELECT reserved_quantity FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = 0 AND id_shop = 1'
        );
        $this->assertSame('0', (string) $reserved);
    }
}
