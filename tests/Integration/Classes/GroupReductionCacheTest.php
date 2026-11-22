<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use GroupReduction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupReductionCacheTest extends KernelTestCase
{
    private const PRODUCT_WITH_REDUCTION_A = 999410;
    private const PRODUCT_WITH_REDUCTION_B = 999411;
    private const PRODUCT_WITHOUT_REDUCTION = 999412;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cleanUpTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();
        parent::tearDown();
    }

    public function testCacheValuesForProductsMatchesDirectQueriesAndCachesMisses(): void
    {
        $db = Db::getInstance();
        $db->insert('product_group_reduction_cache', [
            'id_product' => self::PRODUCT_WITH_REDUCTION_A,
            'id_group' => 1,
            'reduction' => 0.1500,
        ]);
        $db->insert('product_group_reduction_cache', [
            'id_product' => self::PRODUCT_WITH_REDUCTION_A,
            'id_group' => 2,
            'reduction' => 0.2500,
        ]);
        $db->insert('product_group_reduction_cache', [
            'id_product' => self::PRODUCT_WITH_REDUCTION_B,
            'id_group' => 1,
            'reduction' => 0.0500,
        ]);

        GroupReduction::cacheValuesForProducts([
            self::PRODUCT_WITH_REDUCTION_A,
            self::PRODUCT_WITH_REDUCTION_B,
            self::PRODUCT_WITHOUT_REDUCTION,
        ], 1);

        // Other group must still read from DB (not polluted by group 1 prefetch)
        $this->assertSame('0.2500', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_A, 2));

        // Delete DB rows for group 1 to prove getValueForProduct() hits static cache for both hits and misses
        $db->delete(
            'product_group_reduction_cache',
            'id_group = 1 AND id_product IN (' . self::PRODUCT_WITH_REDUCTION_A . ', ' . self::PRODUCT_WITH_REDUCTION_B . ', ' . self::PRODUCT_WITHOUT_REDUCTION . ')'
        );

        $this->assertSame('0.1500', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_A, 1));
        $this->assertSame('0.0500', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_B, 1));
        $this->assertFalse(GroupReduction::getValueForProduct(self::PRODUCT_WITHOUT_REDUCTION, 1));
    }

    public function testCacheValuesForProductsSkipsEmptyInputAndRespectsResetStaticCache(): void
    {
        GroupReduction::cacheValuesForProducts([], 1);

        $db = Db::getInstance();
        $db->insert('product_group_reduction_cache', [
            'id_product' => self::PRODUCT_WITH_REDUCTION_A,
            'id_group' => 1,
            'reduction' => 0.1200,
        ]);

        GroupReduction::cacheValuesForProducts([self::PRODUCT_WITH_REDUCTION_A], 1);
        $this->assertSame('0.1200', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_A, 1));

        $db->update(
            'product_group_reduction_cache',
            ['reduction' => 0.3000],
            'id_product = ' . self::PRODUCT_WITH_REDUCTION_A . ' AND id_group = 1'
        );

        // Still cached until static cache is reset
        $this->assertSame('0.1200', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_A, 1));

        GroupReduction::resetStaticCache();
        $this->assertSame('0.3000', GroupReduction::getValueForProduct(self::PRODUCT_WITH_REDUCTION_A, 1));
    }

    private function cleanUpTestData(): void
    {
        Db::getInstance()->delete(
            'product_group_reduction_cache',
            'id_product IN (' . self::PRODUCT_WITH_REDUCTION_A . ', ' . self::PRODUCT_WITH_REDUCTION_B . ', ' . self::PRODUCT_WITHOUT_REDUCTION . ')'
        );
        GroupReduction::resetStaticCache();
    }
}
