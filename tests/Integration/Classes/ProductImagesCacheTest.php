<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use DbPDO;
use PHPUnit\Framework\TestCase;
use Product;

final class ProductImagesCacheTest extends TestCase
{
    private const PRODUCT_IDS = [1, 2, 3, 4, 5, 6, 7];
    private const PRODUCT_WITHOUT_IMAGE = 99999991;

    public static int $imageQueries = 0;

    private Db $originalDb;

    protected function setUp(): void
    {
        parent::setUp();
        Product::resetStaticCache();
        $this->originalDb = Db::getInstance();
    }

    protected function tearDown(): void
    {
        Db::setInstanceForTesting($this->originalDb);
        Product::resetStaticCache();
        parent::tearDown();
    }

    public function testCachedImagesMatchThePerProductQueriesWithoutQuerying(): void
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $expected = [];
        foreach (self::PRODUCT_IDS as $productId) {
            $product = new Product($productId);
            $expected[$productId] = $this->getProductImages($product, $languageId);
        }
        $this->assertNotEmpty(array_filter(array_column($expected, 1)), 'no compared product has combination images');

        Product::cacheProductsImages(array_merge(self::PRODUCT_IDS, [self::PRODUCT_WITHOUT_IMAGE]), $languageId);
        $this->spyOnImageQueries();

        foreach (self::PRODUCT_IDS as $productId) {
            $product = new Product($productId);
            $this->assertSame($expected[$productId], $this->getProductImages($product, $languageId));
        }
        $productWithoutImage = new Product();
        $productWithoutImage->id = self::PRODUCT_WITHOUT_IMAGE;
        $this->assertSame([], $productWithoutImage->getImages($languageId));

        $this->assertSame(0, self::$imageQueries, 'images were queried again after caching');
    }

    public function testTheCacheIsClearedWithTheProductStaticCache(): void
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT');
        Product::cacheProductsImages([1], $languageId);
        Product::resetStaticCache();
        $this->spyOnImageQueries();

        (new Product(1))->getImages($languageId);

        $this->assertSame(1, self::$imageQueries);
    }

    /**
     * Combination images sorted by combination: several combinations share an image at the same position
     */
    private function getProductImages(Product $product, int $languageId): array
    {
        $combinationImages = $product->getCombinationImages($languageId);
        if (is_array($combinationImages)) {
            ksort($combinationImages);
        }

        return [$product->getImages($languageId), $combinationImages];
    }

    private function spyOnImageQueries(): void
    {
        self::$imageQueries = 0;
        $db = new class(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_) extends DbPDO {
            public function executeS($sql, $array = true, $use_cache = true)
            {
                if (is_string($sql) && str_contains($sql, _DB_PREFIX_ . 'image`')) {
                    ++ProductImagesCacheTest::$imageQueries;
                }

                return parent::executeS($sql, $array, $use_cache);
            }
        };
        Db::setInstanceForTesting($db);
    }
}
