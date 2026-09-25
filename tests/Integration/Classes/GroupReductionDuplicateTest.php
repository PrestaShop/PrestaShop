<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use GroupReduction;
use PHPUnit\Framework\TestCase;

/**
 * Duplicating the group reduction cache used to emit one INSERT per row, which is a multi statement
 * query. The connection is asked to refuse those: config/defines.inc.php sets
 * _PS_ALLOW_MULTI_STATEMENTS_QUERIES_ to false and DbPDO passes it to the driver, so on a setup where
 * the driver honours it, duplicating a product with more than one reduction row failed.
 */
class GroupReductionDuplicateTest extends TestCase
{
    private const SOURCE_PRODUCT_ID = 900001;
    private const TARGET_PRODUCT_ID = 900002;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clear();
    }

    protected function tearDown(): void
    {
        $this->clear();
        parent::tearDown();
    }

    public function testEveryReductionRowIsCopied(): void
    {
        $this->seed([1 => 0.1, 2 => 0.2, 3 => 0.3]);

        $this->assertTrue((bool) GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID));
        $this->assertSame([1 => 0.1, 2 => 0.2, 3 => 0.3], $this->reductionsOf(self::TARGET_PRODUCT_ID));
    }

    public function testASingleRowStillWorks(): void
    {
        $this->seed([1 => 0.15]);

        $this->assertTrue((bool) GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID));
        $this->assertSame([1 => 0.15], $this->reductionsOf(self::TARGET_PRODUCT_ID));
    }

    public function testAProductWithNoReductionIsNotAnError(): void
    {
        $this->assertTrue((bool) GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID));
        $this->assertSame([], $this->reductionsOf(self::TARGET_PRODUCT_ID));
    }

    /**
     * The insert carries ON DUPLICATE KEY UPDATE, so running it twice must update rather than fail or
     * duplicate.
     */
    public function testRunningItTwiceUpdatesRatherThanDuplicates(): void
    {
        $this->seed([1 => 0.1, 2 => 0.2]);

        GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID);
        GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID);

        $this->assertSame([1 => 0.1, 2 => 0.2], $this->reductionsOf(self::TARGET_PRODUCT_ID));
    }

    /**
     * @param array<int, float> $reductionsByGroup
     */
    private function seed(array $reductionsByGroup): void
    {
        $values = [];
        foreach ($reductionsByGroup as $groupId => $reduction) {
            $values[] = '(' . self::SOURCE_PRODUCT_ID . ', ' . (int) $groupId . ', ' . (float) $reduction . ')';
        }

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`) VALUES ' . implode(', ', $values)
        );
    }

    /**
     * @return array<int, float>
     */
    private function reductionsOf(int $productId): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_group`, `reduction` FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache`
             WHERE `id_product` = ' . $productId . ' ORDER BY `id_group`'
        ) ?: [];

        $reductions = [];
        foreach ($rows as $row) {
            $reductions[(int) $row['id_group']] = (float) $row['reduction'];
        }

        return $reductions;
    }

    private function clear(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache`
             WHERE `id_product` IN (' . self::SOURCE_PRODUCT_ID . ', ' . self::TARGET_PRODUCT_ID . ')'
        );
    }
}
