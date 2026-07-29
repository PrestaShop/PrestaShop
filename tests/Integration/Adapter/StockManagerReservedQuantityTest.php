<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * `product_quantity` and `product_quantity_refunded` are both UNSIGNED, so an order line whose
 * refunded quantity exceeds the quantity still on the line makes the reserved-quantity subtraction
 * underflow. MySQL then aborts the whole statement with "BIGINT UNSIGNED value is out of range",
 * which takes down changing an order status and the stock pages rather than that one line.
 *
 * A back office user can produce that state: editing the quantity of a partially refunded line is
 * not validated against what was already refunded.
 */
class StockManagerReservedQuantityTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;
    private const PRODUCT_ATTRIBUTE_ID = 0;

    private static int $orderId;
    private static int $orderDetailId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $db = Db::getInstance();
        // A valid order in a state that has not shipped, so it counts towards reserved quantity.
        $notShippedState = (int) $db->getValue(
            'SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state WHERE shipped = 0 AND deleted = 0 ORDER BY id_order_state ASC'
        );

        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'orders
             (id_address_delivery, id_address_invoice, id_cart, id_currency, id_lang, id_customer, id_carrier,
              current_state, payment, conversion_rate, total_paid, total_paid_real, total_products,
              total_products_wt, valid, id_shop, id_shop_group, date_add, date_upd)
             VALUES (1, 1, 0, 1, 1, 1, 1, ' . $notShippedState . ", 'Test', 1, 0, 0, 0, 0, 1, 1, 1, NOW(), NOW())"
        );
        self::$orderId = (int) $db->Insert_ID();

        // The inconsistent line: one unit left on the line, nine already refunded.
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'order_detail
             (id_order, id_order_invoice, id_warehouse, id_shop, product_id, product_attribute_id,
              product_name, product_quantity, product_quantity_refunded, product_price, id_tax_rules_group)
             VALUES (' . self::$orderId . ', 0, 0, 1, ' . self::PRODUCT_ID . ', ' . self::PRODUCT_ATTRIBUTE_ID
             . ", 'Underflow probe', 1, 9, 10.0, 0)"
        );
        self::$orderDetailId = (int) $db->Insert_ID();
    }

    public static function tearDownAfterClass(): void
    {
        Db::getInstance()->delete('order_detail', 'id_order_detail = ' . self::$orderDetailId);
        Db::getInstance()->delete('orders', 'id_order = ' . self::$orderId);
        parent::tearDownAfterClass();
    }

    /**
     * The same subtraction exists in the repository behind the product page stock tab, reached
     * through its own public entry point.
     */
    public function testTheRepositoryPathSurvivesTheSameLine(): void
    {
        self::bootKernel();
        $repository = self::getContainer()->get(StockAvailableRepository::class);

        $stockId = Db::getInstance()->getValue(
            'SELECT id_stock_available FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = ' . self::PRODUCT_ATTRIBUTE_ID
             . ' AND id_shop = 1'
        );
        self::assertNotFalse($stockId, 'no stock row to exercise the repository with');

        $repository->updatePhysicalProductQuantity(
            new StockId((int) $stockId),
            new OrderStateId((int) Configuration::get('PS_OS_ERROR')),
            new OrderStateId((int) Configuration::get('PS_OS_CANCELED'))
        );

        $reserved = (int) Db::getInstance()->getValue(
            'SELECT reserved_quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . (int) $stockId
        );
        self::assertGreaterThanOrEqual(0, $reserved, 'the repository path returned a negative reserved quantity');
    }

    public function testALineRefundedBeyondItsQuantityDoesNotBreakTheStockUpdate(): void
    {
        self::bootKernel();
        $stockManager = new StockManager();

        $stockManager->updatePhysicalProductQuantity(
            1,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            self::PRODUCT_ID
        );

        $reserved = Db::getInstance()->getValue(
            'SELECT reserved_quantity FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = ' . self::PRODUCT_ATTRIBUTE_ID
             . ' AND id_shop = 1'
        );

        self::assertNotFalse($reserved, 'the stock row was not reachable after the update');
        self::assertGreaterThanOrEqual(
            0,
            (int) $reserved,
            'an over-refunded line must reserve nothing rather than a negative quantity'
        );
    }
}
