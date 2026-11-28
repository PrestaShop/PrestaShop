<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Stock\Update;

use Db;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\StockQuantityUpdater;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockQuantityUpdaterTest extends KernelTestCase
{
    private const UNKNOWN_PRODUCT_ID = 999404;
    private const FIXTURE_PRODUCT_ID = 1;
    private const SHOP_ID = 1;

    private StockQuantityUpdater $updater;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->updater = ServiceLocator::get(StockQuantityUpdater::class);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('stock_available', 'id_product = ' . self::UNKNOWN_PRODUCT_ID);
        parent::tearDown();
    }

    public function testSetQuantityCreatesTheStockThenUpdatesIt(): void
    {
        $this->assertTrue($this->updater->setQuantity(self::UNKNOWN_PRODUCT_ID, 0, 7, self::SHOP_ID, false));
        $this->assertSame(7, $this->getStoredQuantity(self::UNKNOWN_PRODUCT_ID, 0));

        $this->assertTrue($this->updater->setQuantity(self::UNKNOWN_PRODUCT_ID, 0, 3, self::SHOP_ID, false));
        $this->assertSame(3, $this->getStoredQuantity(self::UNKNOWN_PRODUCT_ID, 0));
        $this->assertSame(1, (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . self::UNKNOWN_PRODUCT_ID,
            false
        ));
    }

    public function testUnknownProductIsRefused(): void
    {
        $this->assertFalse($this->updater->decreaseQuantity(self::UNKNOWN_PRODUCT_ID, 0, 1, self::SHOP_ID));
        $this->assertFalse($this->updater->increaseQuantity(0, 0, 1, self::SHOP_ID));
    }

    public function testCombinationChangeAlsoMovesTheProductTotal(): void
    {
        $combinationId = (int) Db::getInstance()->getValue(
            'SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'stock_available
            WHERE id_product = ' . self::FIXTURE_PRODUCT_ID . ' AND id_product_attribute <> 0 ORDER BY id_product_attribute',
            false
        );
        $this->assertGreaterThan(0, $combinationId);
        $combinationQuantity = $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, $combinationId);
        $productQuantity = $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, 0);

        try {
            $this->assertTrue($this->updater->decreaseQuantity(self::FIXTURE_PRODUCT_ID, $combinationId, 2, self::SHOP_ID));

            $this->assertSame($combinationQuantity - 2, $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, $combinationId));
            $this->assertSame($productQuantity - 2, $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, 0));
            $this->assertSame(
                $combinationQuantity - 2,
                StockAvailable::getQuantityAvailableByProduct(self::FIXTURE_PRODUCT_ID, $combinationId, self::SHOP_ID)
            );
        } finally {
            $this->updater->increaseQuantity(self::FIXTURE_PRODUCT_ID, $combinationId, 2, self::SHOP_ID);
        }

        $this->assertSame($combinationQuantity, $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, $combinationId));
        $this->assertSame($productQuantity, $this->getStoredQuantity(self::FIXTURE_PRODUCT_ID, 0));
    }

    public function testEmployeeEditionStoresTheOtherPropertiesWithTheQuantity(): void
    {
        $this->updater->setQuantity(self::UNKNOWN_PRODUCT_ID, 0, 10, self::SHOP_ID, false);
        $stockAvailable = new StockAvailable(
            StockAvailable::getStockAvailableIdByProductId(self::UNKNOWN_PRODUCT_ID, 0, self::SHOP_ID)
        );
        // An order placed after the employee loaded the stock
        Db::getInstance()->update('stock_available', ['quantity' => 9], 'id_product = ' . self::UNKNOWN_PRODUCT_ID);

        $stockAvailable->location = 'A1';
        $this->assertTrue($this->updater->editStock(
            $stockAvailable,
            StockModification::buildDeltaQuantity(5),
            self::SHOP_ID,
            1,
            ['location']
        ));

        $this->assertSame(14, $this->getStoredQuantity(self::UNKNOWN_PRODUCT_ID, 0));
        $this->assertSame('A1', Db::getInstance()->getValue(
            'SELECT location FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . self::UNKNOWN_PRODUCT_ID,
            false
        ));
    }

    private function getStoredQuantity(int $productId, int $combinationId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available
            WHERE id_product = ' . $productId . ' AND id_product_attribute = ' . $combinationId,
            false
        );
    }
}
