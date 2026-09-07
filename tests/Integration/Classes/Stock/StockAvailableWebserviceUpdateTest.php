<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Stock;

use Db;
use PHPUnit\Framework\TestCase;
use Product;
use StockAvailable;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * StockAvailable::setQuantity() announces a stock change with actionUpdateQuantity, which is what the
 * customer availability alerts of ps_emailalerts listen to. The webservice does not go through it: the
 * stock_availables resource maps update to updateWs(), which wrote the row through the model and
 * announced nothing, so a restock made over the API reached no one.
 */
class StockAvailableWebserviceUpdateTest extends TestCase
{
    use ContextMockerTrait;

    // The dispatch itself is not asserted here: Hook::getHookModuleExecList() returns false under the
    // integration bootstrap even with ps_emailalerts installed, active and registered for the shop in
    // context, so Hook::exec() returns before it records anything. What is asserted is the state the
    // update leaves behind, including the cache drop that sits immediately after the dispatch.

    private int $productId;

    private int $stockAvailableId;

    protected function setUp(): void
    {
        parent::setUp();
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
        (new Product($this->productId))->delete();
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . $this->productId
        );

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

    private function storedQuantity(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $this->stockAvailableId
        );
    }
}
