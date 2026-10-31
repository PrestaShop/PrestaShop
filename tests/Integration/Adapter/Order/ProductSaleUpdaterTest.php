<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Order\ProductSaleUpdater;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductSaleUpdaterTest extends KernelTestCase
{
    private Connection $connection;
    private ProductSaleUpdater $updater;
    private string $table;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->updater = self::getContainer()->get(ProductSaleUpdater::class);
        $this->table = _DB_PREFIX_ . 'product_sale';
        $this->connection->beginTransaction();
        $this->connection->delete($this->table, ['id_product' => 999991]);
        $this->connection->delete($this->table, ['id_product' => 999992]);
        $this->connection->insert($this->table, [
            'id_product' => 999991,
            'quantity' => 10,
            'sale_nbr' => 2,
            'date_upd' => '2000-01-01',
        ]);
        $this->connection->insert($this->table, [
            'id_product' => 999992,
            'quantity' => 20,
            'sale_nbr' => 3,
            'date_upd' => '2000-01-01',
        ]);
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
        parent::tearDown();
    }

    /**
     * @dataProvider quantityChanges
     */
    public function testUpdateQuantityPreservesSaleCountAndOtherProducts(int $difference, int $expected): void
    {
        $this->updater->updateQuantity(999991, $difference);

        $row = $this->getSales(999991);
        self::assertSame($expected, (int) $row['quantity']);
        self::assertSame(2, (int) $row['sale_nbr']);
        if (0 === $difference) {
            self::assertSame('2000-01-01', $row['date_upd']);
        }
        $otherProduct = $this->getSales(999992);
        self::assertSame(20, (int) $otherProduct['quantity']);
        self::assertSame(3, (int) $otherProduct['sale_nbr']);
        self::assertSame('2000-01-01', $otherProduct['date_upd']);
    }

    public function quantityChanges(): iterable
    {
        yield 'increase' => [3, 13];
        yield 'decrease' => [-4, 6];
        yield 'unchanged' => [0, 10];
        yield 'inconsistent historical quantity cannot underflow' => [-20, 0];
    }

    public function testMissingCounterIsNotRebuiltFromOnlyTheQuantityDifference(): void
    {
        $this->connection->delete($this->table, ['id_product' => 999991]);
        $this->updater->updateQuantity(999991, 3);
        self::assertFalse($this->getSales(999991));
    }

    private function getSales(int $productId): array|false
    {
        return $this->connection->createQueryBuilder()
            ->select('quantity', 'sale_nbr', 'date_upd')
            ->from($this->table)
            ->where('id_product = :productId')
            ->setParameter('productId', $productId)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();
    }
}
