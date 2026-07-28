<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Pack;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The pack's own pack_stock_type must win over the shop-wide PS_PACK_STOCK_TYPE, including when it
 * is STOCK_TYPE_PACK_ONLY - which is 0, and was therefore swallowed by an empty() check.
 */
class PackStockTypeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = [
        'product', 'product_shop', 'product_lang', 'pack', 'stock_available', 'configuration',
    ];

    private const PACK_OWN_QUANTITY = 1;
    private const ITEM_A_QUANTITY = 10;
    private const ITEM_B_QUANTITY = 20;

    private static int $packId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        self::bootKernel();

        Configuration::updateValue('PS_PACK_FEATURE_ACTIVE', 1);
        Configuration::updateValue('PS_STOCK_MANAGEMENT', 1);

        $itemA = self::createProduct('pack-item-a', self::ITEM_A_QUANTITY);
        $itemB = self::createProduct('pack-item-b', self::ITEM_B_QUANTITY);
        self::$packId = self::createProduct('pack-under-test', self::PACK_OWN_QUANTITY);

        // 2 x A + 1 x B, so the components allow min(floor(10/2), floor(20/1)) = 5 packs.
        Pack::addItem(self::$packId, $itemA, 2);
        Pack::addItem(self::$packId, $itemB, 1);
        Pack::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDownAfterClass();
    }

    private static function createProduct(string $reference, int $quantity): int
    {
        $product = new Product();
        $product->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $reference];
        $product->link_rewrite = [(int) Configuration::get('PS_LANG_DEFAULT') => $reference];
        $product->reference = $reference;
        $product->price = 10.0;
        $product->id_category_default = 2;
        $product->add();

        // No stock movement: it would need an employee in the context, which this test has no use for.
        StockAvailable::setQuantity((int) $product->id, 0, $quantity, null, false);

        return (int) $product->id;
    }

    private function setPackStockType(int $packStockType): void
    {
        $pack = new Product(self::$packId);
        $pack->pack_stock_type = $packStockType;
        $pack->save();

        Pack::resetStaticCache();
        Product::flushPriceCache();
    }

    /**
     * The pack explicitly decrements its own stock, while the shop default says to use the items.
     * The pack's own setting must win, so the answer is the pack's own quantity and not the 5 packs
     * its components would allow.
     */
    public function testExplicitPackOnlyIsNotOverriddenByShopDefault(): void
    {
        Configuration::updateValue('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
        $this->setPackStockType(Pack::STOCK_TYPE_PACK_ONLY);

        $this->assertSame(self::PACK_OWN_QUANTITY, Pack::getQuantity(self::$packId));
    }

    /**
     * The counterpart: STOCK_TYPE_DEFAULT really does mean "use the shop setting".
     */
    public function testDefaultStockTypeStillFollowsShopConfiguration(): void
    {
        Configuration::updateValue('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
        $this->setPackStockType(Pack::STOCK_TYPE_DEFAULT);

        $this->assertSame(5, Pack::getQuantity(self::$packId));
    }

    /**
     * And an explicit products-only pack is unaffected by a pack-only shop default.
     */
    public function testExplicitProductsOnlyIsNotOverriddenByShopDefault(): void
    {
        Configuration::updateValue('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_ONLY);
        $this->setPackStockType(Pack::STOCK_TYPE_PRODUCTS_ONLY);

        $this->assertSame(5, Pack::getQuantity(self::$packId));
    }
}
