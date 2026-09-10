<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Stock;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\StockManager;
use ReflectionMethod;

/**
 * The reserved quantity is a sum over open order lines. A stock line with no such order has no sum, and
 * the query used to store that absence as NULL in a NOT NULL column, which only works because
 * DbPDO::connect() issues SET SESSION sql_mode = ''. Under MySQL's own defaults it is error 1048.
 */
class ReservedQuantityTest extends TestCase
{
    private StockManager $stockManager;

    private ReflectionMethod $updateReserved;

    private string $sqlMode = '';

    private int $idProduct = 0;

    private int $idStockAvailable = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockManager = new StockManager();
        $this->updateReserved = new ReflectionMethod($this->stockManager, 'updateReservedProductQuantity');
        $this->updateReserved->setAccessible(true);

        $this->sqlMode = (string) Db::getInstance()->getValue('SELECT @@SESSION.sql_mode');

        // a product nobody has ordered, so the sum over open orders has nothing to add up
        $this->idProduct = 1 + (int) Db::getInstance()->getValue(
            'SELECT MAX(id_product) FROM ' . _DB_PREFIX_ . 'stock_available'
        );
        Db::getInstance()->insert('stock_available', [
            'id_product' => $this->idProduct,
            'id_product_attribute' => 0,
            'id_shop' => 1,
            'id_shop_group' => 0,
            'quantity' => 7,
            'physical_quantity' => 0,
            'reserved_quantity' => 42,
            'depends_on_stock' => 0,
            'out_of_stock' => 0,
        ]);
        $this->idStockAvailable = (int) Db::getInstance()->Insert_ID();
    }

    protected function tearDown(): void
    {
        Db::getInstance()->execute("SET SESSION sql_mode = '" . pSQL($this->sqlMode) . "'");
        if ($this->idStockAvailable) {
            Db::getInstance()->delete('stock_available', 'id_stock_available = ' . $this->idStockAvailable);
        }

        parent::tearDown();
    }

    public function testAStockLineWithNoOpenOrderIsResetToZero(): void
    {
        $this->recompute();

        $this->assertSame(0, $this->storedReservedQuantity());
    }

    /**
     * The same, with the strictness MySQL applies by default rather than the one PrestaShop turns off.
     */
    public function testItDoesNotRelyOnAnEmptySqlMode(): void
    {
        Db::getInstance()->execute("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");
        $this->assertSame('STRICT_TRANS_TABLES', (string) Db::getInstance()->getValue('SELECT @@SESSION.sql_mode'));

        $this->recompute();

        $this->assertSame(0, $this->storedReservedQuantity());
    }

    private function recompute(): void
    {
        $this->updateReserved->invoke(
            $this->stockManager,
            1,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            $this->idProduct
        );
    }

    private function storedReservedQuantity(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT reserved_quantity FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_stock_available = ' . $this->idStockAvailable
        );
    }
}
