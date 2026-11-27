<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductCleanPositionsTest extends TestCase
{
    use ContextMockerTrait;

    private const TEST_CATEGORY_ID = 999902;
    private const PRODUCT_IDS = [1, 2, 3, 4, 5];
    private const OLD_DATE = '2000-01-01 00:00:00';

    /** @var array<int, array{product: string, product_shop: array<int, string>}> */
    private $originalDates = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $db = Db::getInstance();
        foreach (self::PRODUCT_IDS as $productId) {
            $productShopDates = [];
            foreach ($db->executeS('SELECT `id_shop`, `date_upd` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_product` = ' . $productId) as $row) {
                $productShopDates[(int) $row['id_shop']] = $row['date_upd'];
            }
            $this->originalDates[$productId] = [
                'product' => $db->getValue('SELECT `date_upd` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product` = ' . $productId),
                'product_shop' => $productShopDates,
            ];
        }
        $db->update('product', ['date_upd' => self::OLD_DATE], '`id_product` IN (' . implode(',', self::PRODUCT_IDS) . ')');
        $db->update('product_shop', ['date_upd' => self::OLD_DATE], '`id_product` IN (' . implode(',', self::PRODUCT_IDS) . ')');
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        $db->delete('category_product', '`id_category` = ' . self::TEST_CATEGORY_ID);
        foreach ($this->originalDates as $productId => $dates) {
            $db->update('product', ['date_upd' => $dates['product']], '`id_product` = ' . $productId);
            foreach ($dates['product_shop'] as $shopId => $date) {
                $db->update('product_shop', ['date_upd' => $date], '`id_product` = ' . $productId . ' AND `id_shop` = ' . $shopId);
            }
        }
        parent::tearDown();
    }

    public function testRemovalShiftsAndTouchesOnlyFollowingProducts(): void
    {
        $this->setPositions([1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4]);
        Db::getInstance()->delete('category_product', '`id_category` = ' . self::TEST_CATEGORY_ID . ' AND `id_product` = 2');

        $this->assertTrue(Product::cleanPositions(self::TEST_CATEGORY_ID, 1));

        $this->assertSame([1 => 0, 3 => 1, 4 => 2, 5 => 3], $this->getPositions());
        $this->assertSame([3, 4, 5], $this->getTouchedProductIds());
    }

    public function testFullRenumberingKeepsOrderAndTouchesAllProducts(): void
    {
        $this->setPositions([5 => 0, 3 => 4, 1 => 4, 4 => 9, 2 => 12]);

        $this->assertTrue(Product::cleanPositions(self::TEST_CATEGORY_ID));

        // Equal positions keep the id_product order
        $this->assertSame([5 => 0, 1 => 1, 3 => 2, 4 => 3, 2 => 4], $this->getPositions());
        $this->assertSame(self::PRODUCT_IDS, $this->getTouchedProductIds());
    }

    /**
     * @param array<int, int> $positions
     */
    private function setPositions(array $positions): void
    {
        foreach ($positions as $productId => $position) {
            Db::getInstance()->insert('category_product', [
                'id_category' => self::TEST_CATEGORY_ID,
                'id_product' => $productId,
                'position' => $position,
            ]);
        }
    }

    /**
     * @return array<int, int>
     */
    private function getPositions(): array
    {
        $positions = [];
        foreach (Db::getInstance()->executeS(
            'SELECT `id_product`, `position` FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_category` = ' . self::TEST_CATEGORY_ID . ' ORDER BY `position`'
        ) as $row) {
            $positions[(int) $row['id_product']] = (int) $row['position'];
        }

        return $positions;
    }

    /**
     * Products whose date_upd changed in both product and product_shop of the default shop.
     *
     * @return int[]
     */
    private function getTouchedProductIds(): array
    {
        return array_map('intval', array_column(Db::getInstance()->executeS(
            'SELECT p.`id_product` FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON ps.`id_product` = p.`id_product` AND ps.`id_shop` = p.`id_shop_default`
            WHERE p.`id_product` IN (' . implode(',', self::PRODUCT_IDS) . ')
            AND p.`date_upd` != "' . self::OLD_DATE . '" AND ps.`date_upd` != "' . self::OLD_DATE . '"
            ORDER BY p.`id_product`'
        ), 'id_product'));
    }
}
