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

class GroupReductionTest extends KernelTestCase
{
    private const SOURCE_PRODUCT_ID = 999403;
    private const TARGET_PRODUCT_ID = 999404;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();
        $db->delete(
            'product_group_reduction_cache',
            'id_product IN (' . self::SOURCE_PRODUCT_ID . ', ' . self::TARGET_PRODUCT_ID . ')'
        );

        $db->insert('product_group_reduction_cache', [
            'id_product' => self::SOURCE_PRODUCT_ID,
            'id_group' => 1,
            'reduction' => 0.1000,
        ]);
        $db->insert('product_group_reduction_cache', [
            'id_product' => self::SOURCE_PRODUCT_ID,
            'id_group' => 2,
            'reduction' => 0.2500,
        ]);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete(
            'product_group_reduction_cache',
            'id_product IN (' . self::SOURCE_PRODUCT_ID . ', ' . self::TARGET_PRODUCT_ID . ')'
        );
        parent::tearDown();
    }

    public function testDuplicateReductionCopiesMultipleRows(): void
    {
        $this->assertTrue((bool) GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID));

        $rows = $this->fetchTargetRows();
        $this->assertCount(2, $rows);
        $this->assertEqualsWithDelta(0.1000, (float) $rows[1], 0.0001);
        $this->assertEqualsWithDelta(0.2500, (float) $rows[2], 0.0001);
    }

    public function testDuplicateReductionUpdatesExistingRowsOnDuplicateKey(): void
    {
        Db::getInstance()->insert('product_group_reduction_cache', [
            'id_product' => self::TARGET_PRODUCT_ID,
            'id_group' => 1,
            'reduction' => 0.0500,
        ]);

        $this->assertTrue((bool) GroupReduction::duplicateReduction(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID));

        $rows = $this->fetchTargetRows();
        $this->assertCount(2, $rows);
        $this->assertEqualsWithDelta(0.1000, (float) $rows[1], 0.0001);
        $this->assertEqualsWithDelta(0.2500, (float) $rows[2], 0.0001);
    }

    public function testDuplicateReductionReturnsTrueWhenSourceHasNoRows(): void
    {
        $this->assertTrue((bool) GroupReduction::duplicateReduction(999499, self::TARGET_PRODUCT_ID));
        $this->assertCount(0, $this->fetchTargetRows());
    }

    /**
     * @return array<int, string>
     */
    private function fetchTargetRows(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_group`, `reduction`
             FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache`
             WHERE `id_product` = ' . self::TARGET_PRODUCT_ID . '
             ORDER BY `id_group` ASC'
        );

        $byGroup = [];
        foreach ($rows ?: [] as $row) {
            $byGroup[(int) $row['id_group']] = (string) $row['reduction'];
        }

        return $byGroup;
    }
}
