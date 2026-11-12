<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Stock;

use Db;
use Language;
use Shop;
use StockAvailable;
use StockMvtReason;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;

/**
 * StockAvailable::setQuantity() records a stock movement but used to give the caller no say in what
 * it is attributed to, unlike its sibling updateQuantity(). These cover the movement parameters
 * reaching the recorded movement.
 */
class StockAvailableMovementTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;
    private const DEFAULT_SHOP_ID = 1;

    private ?int $reasonId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        // Global var read by legacy code resolving the container (SymfonyContainer::getInstance).
        // Without it StockManager::saveMovement() returns false before recording anything.
        global $kernel;
        $kernel = self::$kernel;

        ShopResetter::resetShops();

        Shop::resetStaticCache();
        Shop::resetContext();
    }

    public static function tearDownAfterClass(): void
    {
        ShopResetter::resetShops();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // StockAvailable memoizes the stock_available id per product in a static.
        StockAvailable::resetStaticCache();
        Shop::resetStaticCache();
    }

    protected function tearDown(): void
    {
        if (null !== $this->reasonId) {
            (new StockMvtReason($this->reasonId))->delete();
            $this->reasonId = null;
        }
        parent::tearDown();
    }

    public function testSetQuantityRecordsTheMovementReasonTheCallerAsked(): void
    {
        Shop::setContext(Shop::CONTEXT_SHOP, self::DEFAULT_SHOP_ID);
        $this->reasonId = $this->createReason('Sync from an external system');
        $quantity = $this->quantityOf(self::DEFAULT_SHOP_ID);

        StockAvailable::setQuantity(self::PRODUCT_ID, 0, $quantity + 7, self::DEFAULT_SHOP_ID, true, [
            'id_stock_mvt_reason' => $this->reasonId,
        ]);

        $movement = $this->latestMovementForShop(self::DEFAULT_SHOP_ID);
        $this->assertNotNull($movement, 'no stock movement was recorded');
        $this->assertSame($this->reasonId, (int) $movement['id_stock_mvt_reason']);
        $this->assertSame(7, (int) $movement['physical_quantity']);

        StockAvailable::setQuantity(self::PRODUCT_ID, 0, $quantity, self::DEFAULT_SHOP_ID, false);
    }

    private function quantityOf(int $shopId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `quantity` FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product` = ' . self::PRODUCT_ID . ' AND `id_product_attribute` = 0
                AND `id_shop` = ' . $shopId
        );
    }

    private function stockIdOf(int $shopId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `id_stock_available` FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product` = ' . self::PRODUCT_ID . ' AND `id_product_attribute` = 0
                AND `id_shop` = ' . $shopId
        );
    }

    /**
     * @return array<string, string>|null
     */
    private function latestMovementForShop(int $shopId): ?array
    {
        $row = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'stock_mvt`
            WHERE `id_stock` = ' . $this->stockIdOf($shopId) . '
            ORDER BY `id_stock_mvt` DESC'
        );

        return empty($row) ? null : $row;
    }

    private function createReason(string $name): int
    {
        $reason = new StockMvtReason();
        $reason->sign = 1;
        // A multilang field is declared as its scalar type but validateFieldsLang() requires the
        // per-language array, so the property type and the runtime contract disagree here.
        // @phpstan-ignore-next-line
        $reason->name = array_fill_keys(Language::getIDs(false), $name);
        $reason->add();

        return (int) $reason->id;
    }
}
