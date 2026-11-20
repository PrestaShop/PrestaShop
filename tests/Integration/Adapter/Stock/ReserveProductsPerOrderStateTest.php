<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Stock;

use Configuration;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\StockManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Which orders hold their products in reserve used to be hardcoded - not shipped, and either valid or
 * neither the error nor the cancellation status - so a merchant could not decide it. It is now the order
 * status' own reserve_products flag.
 */
class ReserveProductsPerOrderStateTest extends KernelTestCase
{
    private Connection $connection;
    private string $dbPrefix;
    private StockManager $stockManager;
    private int $orderStateId;
    private int $productId;
    private int $combinationId;
    private int $initialFlag;
    private int $initialReservedQuantity;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->stockManager = new StockManager();

        // Restoring whole tables would drop the column on a database built before it existed, and this
        // test only ever changes one flag and one quantity - so it puts those two back itself.
        // Pick a valid order and the product it holds, so there is something to reserve.
        $row = $this->connection->executeQuery(
            'SELECT o.current_state, od.product_id, od.product_attribute_id
             FROM ' . $this->dbPrefix . 'orders o
             INNER JOIN ' . $this->dbPrefix . 'order_detail od ON od.id_order = o.id_order
             INNER JOIN ' . $this->dbPrefix . 'order_state os ON os.id_order_state = o.current_state
             WHERE o.id_shop = 1 AND os.reserve_products = 1
             LIMIT 1'
        )->fetchAssociative();

        $this->assertNotFalse($row, 'The fixture needs an order in a reserving status.');
        $this->orderStateId = (int) $row['current_state'];
        $this->productId = (int) $row['product_id'];
        $this->combinationId = (int) $row['product_attribute_id'];

        $this->initialFlag = (int) $this->connection->executeQuery(
            'SELECT reserve_products FROM ' . $this->dbPrefix . 'order_state WHERE id_order_state = :id',
            ['id' => $this->orderStateId]
        )->fetchOne();
        $this->initialReservedQuantity = $this->readReservedQuantity();
    }

    protected function tearDown(): void
    {
        $this->setReserveProducts((bool) $this->initialFlag);
        $this->connection->executeStatement(
            'UPDATE ' . $this->dbPrefix . 'stock_available SET reserved_quantity = :quantity
             WHERE id_product = :product AND id_product_attribute = :combination AND id_shop = 1',
            [
                'quantity' => $this->initialReservedQuantity,
                'product' => $this->productId,
                'combination' => $this->combinationId,
            ]
        );

        parent::tearDown();
    }

    public function testAStatusStopsReservingWhenTheFlagIsCleared(): void
    {
        // Control: with the flag on, the order's products are reserved.
        $reservedWhileFlagged = $this->resyncAndReadReservedQuantity();
        $this->assertGreaterThan(
            0,
            $reservedWhileFlagged,
            'The fixture order should be reserving its products to begin with.'
        );

        $this->setReserveProducts(false);
        $this->assertSame(0, $this->resyncAndReadReservedQuantity());

        // ...and putting it back restores the reservation, so the flag is what decides it.
        $this->setReserveProducts(true);
        $this->assertSame($reservedWhileFlagged, $this->resyncAndReadReservedQuantity());
    }

    private function setReserveProducts(bool $reserve): void
    {
        $this->connection->executeStatement(
            'UPDATE ' . $this->dbPrefix . 'order_state SET reserve_products = :reserve WHERE id_order_state = :id',
            ['reserve' => (int) $reserve, 'id' => $this->orderStateId]
        );
    }

    private function resyncAndReadReservedQuantity(): int
    {
        $this->stockManager->updatePhysicalProductQuantity(
            1,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            $this->productId
        );

        return $this->readReservedQuantity();
    }

    private function readReservedQuantity(): int
    {
        return (int) $this->connection->executeQuery(
            'SELECT reserved_quantity FROM ' . $this->dbPrefix . 'stock_available
             WHERE id_product = :product AND id_product_attribute = :combination AND id_shop = 1',
            ['product' => $this->productId, 'combination' => $this->combinationId]
        )->fetchOne();
    }
}
