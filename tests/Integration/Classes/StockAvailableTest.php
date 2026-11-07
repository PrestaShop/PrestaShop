<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use Configuration;
use Db;
use OrderHistory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockAvailableTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;

    private int $combinationId = 0;

    private int $orderId = 0;

    private int $productQuantity = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->productQuantity = $this->getStockQuantity(0);

        $combination = new Combination();
        $combination->id_product = self::PRODUCT_ID;
        $combination->add();
        $this->combinationId = (int) $combination->id;

        $db = Db::getInstance();
        $db->insert('orders', [
            'reference' => 'STKDELCMB',
            'secure_key' => md5('STKDELCMB'),
            'module' => 'ps_checkpayment',
            'conversion_rate' => 1,
            'id_address_delivery' => 1,
            'id_address_invoice' => 1,
            'id_cart' => 1,
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
        $db->insert('order_detail', [
            'id_order' => $this->orderId,
            'id_shop' => 1,
            'product_id' => self::PRODUCT_ID,
            'product_attribute_id' => $this->combinationId,
            'product_name' => 'Deleted combination',
            'product_quantity' => 2,
            'product_weight' => 0,
            'tax_name' => '',
        ]);
        $db->insert('order_history', [
            'id_order' => $this->orderId,
            'id_order_state' => (int) Configuration::get('PS_OS_PAYMENT'),
            'id_employee' => 0,
            'date_add' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        $db->delete('order_history', 'id_order = ' . $this->orderId);
        $db->delete('order_detail', 'id_order = ' . $this->orderId);
        $db->delete('orders', 'id_order = ' . $this->orderId);
        $db->delete('stock_available', 'id_product_attribute = ' . $this->combinationId);
        $db->update('stock_available', ['quantity' => $this->productQuantity], 'id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = 0');
        parent::tearDown();
    }

    public function testCancellingAnOrderDoesNotGiveBackTheStockOfADeletedCombination(): void
    {
        (new Combination($this->combinationId))->delete();

        $history = new OrderHistory();
        $history->id_order = $this->orderId;
        $history->changeIdOrderState((int) Configuration::get('PS_OS_CANCELED'), $this->orderId);

        $this->assertSame(
            0,
            (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product_attribute = ' . $this->combinationId),
            'No stock may be recreated for a combination that no longer exists.'
        );
        $this->assertSame($this->productQuantity, $this->getStockQuantity(0), 'The product total must not count the stock of a deleted combination.');
    }

    private function getStockQuantity(int $combinationId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = ' . $combinationId . ' AND id_shop = 1'
        );
    }
}
