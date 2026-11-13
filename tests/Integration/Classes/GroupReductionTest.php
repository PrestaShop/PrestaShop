<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use DbPDO;
use GroupReduction;
use PHPUnit\Framework\TestCase;

final class GroupReductionTest extends TestCase
{
    private const ID_GROUP = 3;
    private const ID_CATEGORY_ACCESSORIES = 6;
    private const PRODUCT_IN_ACCESSORIES = 6;
    private const PRODUCT_OUTSIDE_ACCESSORIES = 1;

    public static int $reductionQueries = 0;

    private Db $originalDb;

    protected function setUp(): void
    {
        parent::setUp();
        GroupReduction::resetStaticCache();
        $this->originalDb = Db::getInstance();
    }

    protected function tearDown(): void
    {
        Db::setInstanceForTesting($this->originalDb);
        GroupReduction::resetStaticCache();
        parent::tearDown();
    }

    public function testAGroupWithoutProductReductionsIsLookedUpOnce(): void
    {
        $this->spyOnReductionQueries();

        foreach ([1, 2, 3, 6, 7] as $productId) {
            $this->assertFalse(GroupReduction::getValueForProduct($productId, self::ID_GROUP));
        }

        $this->assertSame(1, self::$reductionQueries);
    }

    public function testAGroupWithProductReductionsReturnsTheProductValue(): void
    {
        $groupReduction = $this->addGroupReduction();
        try {
            GroupReduction::resetStaticCache();

            $this->assertEquals(0.1, (float) GroupReduction::getValueForProduct(self::PRODUCT_IN_ACCESSORIES, self::ID_GROUP));
            $this->assertFalse(GroupReduction::getValueForProduct(self::PRODUCT_OUTSIDE_ACCESSORIES, self::ID_GROUP));
        } finally {
            $groupReduction->delete();
        }
    }

    public function testAReductionAddedAfterALookupIsReturned(): void
    {
        $this->assertFalse(GroupReduction::getValueForProduct(self::PRODUCT_IN_ACCESSORIES, self::ID_GROUP));

        $groupReduction = $this->addGroupReduction();
        try {
            $this->assertEquals(0.1, (float) GroupReduction::getValueForProduct(self::PRODUCT_IN_ACCESSORIES, self::ID_GROUP));
        } finally {
            $groupReduction->delete();
        }
    }

    private function addGroupReduction(): GroupReduction
    {
        $groupReduction = new GroupReduction();
        $groupReduction->id_group = self::ID_GROUP;
        $groupReduction->id_category = self::ID_CATEGORY_ACCESSORIES;
        $groupReduction->reduction = 0.1;
        $this->assertTrue($groupReduction->add());

        return $groupReduction;
    }

    private function spyOnReductionQueries(): void
    {
        self::$reductionQueries = 0;
        $db = new class(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_) extends DbPDO {
            public function getValue($sql, $use_cache = true)
            {
                if (is_string($sql) && str_contains($sql, _DB_PREFIX_ . 'product_group_reduction_cache`')) {
                    ++GroupReductionTest::$reductionQueries;
                }

                return parent::getValue($sql, $use_cache);
            }
        };
        Db::setInstanceForTesting($db);
    }
}
