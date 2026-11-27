<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Stock;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * StockAvailable::setQuantity() announces a stock change with actionUpdateQuantity, which is what the
 * customer availability alerts of ps_emailalerts listen to. The webservice does not go through it: the
 * stock_availables resource maps update to updateWs(), which wrote the row through the model and
 * announced nothing, so a restock made over the API reached no one.
 */
class StockAvailableWebserviceUpdateTest extends KernelTestCase
{
    use ContextMockerTrait;

    // The dispatch itself is not asserted here: Hook::getHookModuleExecList() returns false under the
    // integration bootstrap even with ps_emailalerts installed, active and registered for the shop in
    // context, so Hook::exec() returns before it records anything. What is asserted is the state the
    // update leaves behind, including the cache drop that sits immediately after the dispatch.

    private int $productId;

    private int $stockAvailableId;

    /** @var mixed */
    private $previousKernel;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // SymfonyContainer reads the global kernel, so the movements go through the Doctrine repository
        // the way they do in a back office request.
        global $kernel;
        $this->previousKernel = $kernel;
        $kernel = self::$kernel;
        SymfonyContainer::resetStaticCache();
        self::mockContext();

        $product = new Product(null, false, 1);
        $product->name = 'Stock alert product';
        $product->price = 12.0;
        $product->link_rewrite = 'stock-alert-product-' . uniqid();
        $product->save();
        $this->productId = (int) $product->id;

        StockAvailable::setQuantity($this->productId, 0, 0);
        $this->stockAvailableId = (int) StockAvailable::getStockAvailableIdByProductId($this->productId);
        self::assertGreaterThan(0, $this->stockAvailableId, 'the product should have a stock_available row');
    }

    protected function tearDown(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'stock_mvt WHERE id_stock = ' . $this->stockAvailableId
        );
        (new Product($this->productId))->delete();
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . $this->productId
        );

        self::resetContext();
        global $kernel;
        $kernel = $this->previousKernel;
        SymfonyContainer::resetStaticCache();
        parent::tearDown();
    }

    /**
     * The announcement is worth nothing if the row did not actually move.
     */
    public function testTheStoredQuantityIsUpdatedToo(): void
    {
        $stockAvailable = new StockAvailable($this->stockAvailableId);
        $stockAvailable->quantity = 7;
        $stockAvailable->updateWs();

        self::assertSame(7, $this->storedQuantity());
    }

    /**
     * setQuantity() drops its cached quantity when it changes stock. Doing the same here keeps a listener
     * that asks StockAvailable for the quantity from being handed the value from before the update.
     */
    public function testTheCachedQuantityDoesNotSurviveTheUpdate(): void
    {
        StockAvailable::setQuantity($this->productId, 0, 2);
        self::assertSame(2, (int) StockAvailable::getQuantityAvailableByProduct($this->productId));

        $stockAvailable = new StockAvailable($this->stockAvailableId);
        $stockAvailable->quantity = 9;
        $stockAvailable->updateWs();

        self::assertSame(
            9,
            (int) StockAvailable::getQuantityAvailableByProduct($this->productId),
            'a listener would have been given the quantity from before the webservice update'
        );
    }

    public function testAnIncreaseIsRecordedAsAStockMovement(): void
    {
        self::assertNotNull(SymfonyContainer::getInstance(), 'the case needs the Symfony kernel');
        $this->updateQuantityTo(7);

        self::assertSame(
            [['sign' => 1, 'physical_quantity' => 7, 'id_stock_mvt_reason' => (int) Configuration::get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION')]],
            $this->recordedMovements()
        );
    }

    public function testADecreaseIsRecordedAsAStockMovement(): void
    {
        StockAvailable::setQuantity($this->productId, 0, 10, null, false);

        $this->updateQuantityTo(4);

        self::assertSame(
            [['sign' => -1, 'physical_quantity' => 6, 'id_stock_mvt_reason' => (int) Configuration::get('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION')]],
            $this->recordedMovements()
        );
    }

    public function testAnUpdateThatKeepsTheQuantityRecordsNothing(): void
    {
        $this->updateQuantityTo(0);

        self::assertSame([], $this->recordedMovements());
    }

    /**
     * The webservice dispatcher does not boot the Symfony kernel, so the movement is written without it.
     */
    public function testTheMovementIsRecordedWithoutTheSymfonyKernel(): void
    {
        global $kernel;
        $previousKernel = $kernel;
        $kernel = null;
        SymfonyContainer::resetStaticCache();
        try {
            self::assertNull(SymfonyContainer::getInstance(), 'the case needs a request without the Symfony kernel');
            $this->updateQuantityTo(3);
        } finally {
            $kernel = $previousKernel;
            SymfonyContainer::resetStaticCache();
        }

        self::assertSame(
            [['sign' => 1, 'physical_quantity' => 3, 'id_stock_mvt_reason' => (int) Configuration::get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION')]],
            $this->recordedMovements()
        );
    }

    private function updateQuantityTo(int $quantity): void
    {
        $stockAvailable = new StockAvailable($this->stockAvailableId);
        $stockAvailable->quantity = $quantity;
        self::assertTrue($stockAvailable->updateWs());
    }

    /**
     * @return array<int, array{sign: int, physical_quantity: int, id_stock_mvt_reason: int}>
     */
    private function recordedMovements(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT sign, physical_quantity, id_stock_mvt_reason FROM ' . _DB_PREFIX_ . 'stock_mvt
             WHERE id_stock = ' . $this->stockAvailableId . ' ORDER BY id_stock_mvt'
        ) ?: [];

        return array_map(static function (array $row): array {
            return [
                'sign' => (int) $row['sign'],
                'physical_quantity' => (int) $row['physical_quantity'],
                'id_stock_mvt_reason' => (int) $row['id_stock_mvt_reason'],
            ];
        }, $rows);
    }

    private function storedQuantity(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $this->stockAvailableId
        );
    }
}
