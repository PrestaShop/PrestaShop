<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Stock\Update;

use Db;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\StockAvailableQuantityUpdater;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockAvailableQuantityUpdaterTest extends KernelTestCase
{
    private const PRODUCT_ID = 999402;
    private const COMBINATION_ID = 999403;

    private StockAvailableQuantityUpdater $updater;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->updater = new StockAvailableQuantityUpdater();
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('stock_available', 'id_product = ' . self::PRODUCT_ID);
        parent::tearDown();
    }

    /**
     * @dataProvider getConcurrentDeltas
     */
    public function testStaleReadersDoNotOverwriteEachOther(int $firstDelta, int $secondDelta, int $expectedQuantity): void
    {
        $stockId = $this->createStock(0, 10);
        $firstReader = new StockAvailable($stockId);
        $secondReader = new StockAvailable($stockId);

        $this->assertTrue($this->updater->applyDelta($firstReader, $firstDelta));
        $this->assertTrue($this->updater->applyDelta($secondReader, $secondDelta));

        $this->assertSame($expectedQuantity, (int) $secondReader->quantity);
        $this->assertSame($expectedQuantity, $this->getStoredQuantity($stockId));
    }

    public static function getConcurrentDeltas(): iterable
    {
        yield 'two decreases' => [-3, -4, 3];
        yield 'a decrease and an increase' => [-3, 5, 12];
    }

    public function testCombinationDeltaUpdatesTheProductTotal(): void
    {
        $productStockId = $this->createStock(0, 10);
        $combinationStockId = $this->createStock(self::COMBINATION_ID, 10);

        $this->assertTrue($this->updater->applyDelta(new StockAvailable($combinationStockId), -4));

        $this->assertSame(6, $this->getStoredQuantity($combinationStockId));
        $this->assertSame(6, $this->getStoredQuantity($productStockId));
    }

    public function testFixedQuantityLeavesOtherColumnsOfAStaleReaderUntouched(): void
    {
        $stockId = $this->createStock(0, 10);
        $staleReader = new StockAvailable($stockId);
        Db::getInstance()->update('stock_available', ['location' => 'A1'], 'id_stock_available = ' . $stockId);

        $this->assertTrue($this->updater->setQuantity($staleReader, 25));

        $this->assertSame(25, (int) $staleReader->quantity);
        $this->assertSame(25, $this->getStoredQuantity($stockId));
        $this->assertSame('A1', Db::getInstance()->getValue(
            'SELECT location FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $stockId,
            false
        ));
    }

    public function testOverrideOfUpdateIsStillCalled(): void
    {
        $stockAvailable = new StockAvailableWithUpdateOverride($this->createStock(0, 10));

        $this->assertTrue($this->updater->applyDelta($stockAvailable, -3));
        $this->assertTrue($this->updater->setQuantity($stockAvailable, 20));

        $this->assertSame(2, $stockAvailable->updateCalls);
    }

    public function testStockThatIsNotPersistedIsRefused(): void
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->quantity = 10;

        $this->assertFalse($this->updater->applyDelta($stockAvailable, -3));
        $this->assertSame(10, (int) $stockAvailable->quantity);
    }

    private function createStock(int $combinationId, int $quantity): int
    {
        $db = Db::getInstance();
        $db->insert('stock_available', [
            'id_product' => self::PRODUCT_ID,
            'id_product_attribute' => $combinationId,
            'id_shop' => 1,
            'id_shop_group' => 0,
            'quantity' => $quantity,
            'physical_quantity' => $quantity,
            'reserved_quantity' => 0,
        ]);

        return (int) $db->Insert_ID();
    }

    private function getStoredQuantity(int $stockId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $stockId,
            false
        );
    }
}

class StockAvailableWithUpdateOverride extends StockAvailable
{
    public int $updateCalls = 0;

    public function update($null_values = false)
    {
        ++$this->updateCalls;

        return parent::update($null_values);
    }
}
