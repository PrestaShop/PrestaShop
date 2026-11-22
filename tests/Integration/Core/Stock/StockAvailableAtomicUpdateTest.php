<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Stock;

use Db;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Verifies that relative stock changes via StockAvailable::updateQuantityDelta() are applied
 * atomically in SQL so concurrent readers do not overwrite each other's updates.
 */
class StockAvailableAtomicUpdateTest extends KernelTestCase
{
    private const PRODUCT_ID = 999402;

    private int $stockId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();
        $db->insert('stock_available', [
            'id_product' => self::PRODUCT_ID,
            'id_product_attribute' => 0,
            'id_shop' => 1,
            'id_shop_group' => 0,
            'quantity' => 10,
            'physical_quantity' => 10,
            'reserved_quantity' => 0,
        ]);
        $this->stockId = (int) $db->Insert_ID();
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('stock_available', 'id_product = ' . self::PRODUCT_ID);
        parent::tearDown();
    }

    public function testConcurrentDecrementsDoNotLoseUpdates(): void
    {
        $firstReader = new StockAvailable($this->stockId);
        $secondReader = new StockAvailable($this->stockId);

        $this->assertSame(10, (int) $firstReader->quantity);
        $this->assertSame(10, (int) $secondReader->quantity);

        $this->assertTrue($firstReader->updateQuantityDelta(-3));
        $this->assertTrue($secondReader->updateQuantityDelta(-4));

        // Second writer must not overwrite the first decrement back to 10 - 4 = 6
        $this->assertSame(3, (int) $secondReader->quantity);
        $dbQuantity = (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $this->stockId
        );
        $this->assertSame(3, $dbQuantity);
    }

    public function testConcurrentMixedDeltasPreserveNetStock(): void
    {
        $firstReader = new StockAvailable($this->stockId);
        $secondReader = new StockAvailable($this->stockId);

        $this->assertTrue($firstReader->updateQuantityDelta(-3));
        $this->assertTrue($secondReader->updateQuantityDelta(5));

        $this->assertSame(12, (int) $secondReader->quantity);
        $dbQuantity = (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $this->stockId
        );
        $this->assertSame(12, $dbQuantity);
    }
}
