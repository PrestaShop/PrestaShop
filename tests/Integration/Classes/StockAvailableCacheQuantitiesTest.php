<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Db;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockAvailableCacheQuantitiesTest extends KernelTestCase
{
    private const PRODUCT_WITH_COMBINATIONS_ID = 999405;
    private const SIMPLE_PRODUCT_ID = 999406;
    private const MISSING_PRODUCT_ID = 999407;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();
        $db->delete(
            'stock_available',
            'id_product IN (' . self::PRODUCT_WITH_COMBINATIONS_ID . ', ' . self::SIMPLE_PRODUCT_ID . ', ' . self::MISSING_PRODUCT_ID . ')'
        );

        $rows = [
            ['id_product' => self::PRODUCT_WITH_COMBINATIONS_ID, 'id_product_attribute' => 0, 'quantity' => 15],
            ['id_product' => self::PRODUCT_WITH_COMBINATIONS_ID, 'id_product_attribute' => 1, 'quantity' => 10],
            ['id_product' => self::PRODUCT_WITH_COMBINATIONS_ID, 'id_product_attribute' => 2, 'quantity' => 5],
            ['id_product' => self::SIMPLE_PRODUCT_ID, 'id_product_attribute' => 0, 'quantity' => 8],
        ];
        foreach ($rows as $row) {
            $db->insert('stock_available', [
                'id_product' => $row['id_product'],
                'id_product_attribute' => $row['id_product_attribute'],
                'id_shop' => 1,
                'id_shop_group' => 0,
                'quantity' => $row['quantity'],
                'physical_quantity' => $row['quantity'],
                'reserved_quantity' => 0,
            ]);
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_*');
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete(
            'stock_available',
            'id_product IN (' . self::PRODUCT_WITH_COMBINATIONS_ID . ', ' . self::SIMPLE_PRODUCT_ID . ', ' . self::MISSING_PRODUCT_ID . ')'
        );
        Cache::clean('StockAvailable::getQuantityAvailableByProduct_*');
        parent::tearDown();
    }

    public function testCacheQuantitiesPopulatesCacheAndMatchesDirectQueries(): void
    {
        StockAvailable::cacheQuantities([
            self::PRODUCT_WITH_COMBINATIONS_ID,
            self::SIMPLE_PRODUCT_ID,
            self::MISSING_PRODUCT_ID,
        ]);

        $this->assertTrue(Cache::isStored('StockAvailable::getQuantityAvailableByProduct_' . self::PRODUCT_WITH_COMBINATIONS_ID . '-0-0'));
        $this->assertTrue(Cache::isStored('StockAvailable::getQuantityAvailableByProduct_' . self::PRODUCT_WITH_COMBINATIONS_ID . '-1-0'));
        $this->assertTrue(Cache::isStored('StockAvailable::getQuantityAvailableByProduct_' . self::PRODUCT_WITH_COMBINATIONS_ID . '-2-0'));
        $this->assertTrue(Cache::isStored('StockAvailable::getQuantityAvailableByProduct_' . self::SIMPLE_PRODUCT_ID . '-0-0'));
        $this->assertTrue(Cache::isStored('StockAvailable::getQuantityAvailableByProduct_' . self::MISSING_PRODUCT_ID . '-0-0'));

        $this->assertSame(15, StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_WITH_COMBINATIONS_ID, 0));
        $this->assertSame(10, StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_WITH_COMBINATIONS_ID, 1));
        $this->assertSame(5, StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_WITH_COMBINATIONS_ID, 2));
        $this->assertSame(8, StockAvailable::getQuantityAvailableByProduct(self::SIMPLE_PRODUCT_ID, 0));
        $this->assertSame(0, StockAvailable::getQuantityAvailableByProduct(self::MISSING_PRODUCT_ID, 0));
    }

    public function testSetQuantityInvalidatesPrefetchedCache(): void
    {
        StockAvailable::cacheQuantities([self::PRODUCT_WITH_COMBINATIONS_ID]);
        $this->assertSame(10, StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_WITH_COMBINATIONS_ID, 1));

        StockAvailable::setQuantity(self::PRODUCT_WITH_COMBINATIONS_ID, 1, 77, 1, false);

        $this->assertSame(77, StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_WITH_COMBINATIONS_ID, 1));
    }
}
