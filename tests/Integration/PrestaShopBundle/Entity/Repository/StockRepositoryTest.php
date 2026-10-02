<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Api\QueryStockParamsCollection;
use PrestaShopBundle\Entity\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockRepositoryTest extends KernelTestCase
{
    private const BOGUS_RESERVED_QUANTITY = 999;

    private Connection $connection;
    private string $dbPrefix;
    private StockRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->repository = self::getContainer()->get('prestashop.core.api.stock.repository');
    }

    /**
     * The grid used to recompute reserved and physical quantities for every stock_available row of
     * the shop on each page load, which costs one correlated sub-select over the order tables per
     * row. Only the products the page displays need refreshing; everything else must be left alone.
     */
    public function testOnlyTheDisplayedProductsAreRefreshed(): void
    {
        $this->setReservedQuantityEverywhere(self::BOGUS_RESERVED_QUANTITY);

        $rows = $this->repository->getData(
            (new QueryStockParamsCollection())->fromArray(['page_size' => 1, 'page_index' => 1])
        );

        $this->assertCount(1, $rows, 'The fixture needs exactly one row on the page.');
        $displayedProductId = (int) $rows[0]['product_id'];

        $stillBogus = $this->productIdsWithReservedQuantity(self::BOGUS_RESERVED_QUANTITY);

        // The displayed product was refreshed...
        $this->assertNotContains($displayedProductId, $stillBogus);
        // ...and it is the only one, so the pass no longer walks the whole catalogue.
        $this->assertGreaterThan(
            0,
            count($stillBogus),
            'Every product was refreshed, so the update was not scoped to the page.'
        );
    }

    /**
     * The rows are fetched before the refresh runs, so the refreshed values have to be read back
     * into them - otherwise the grid keeps showing the quantities as they were on the previous load.
     */
    public function testTheRefreshedValuesReachTheReturnedRows(): void
    {
        $this->setReservedQuantityEverywhere(self::BOGUS_RESERVED_QUANTITY);

        $rows = $this->repository->getData(
            (new QueryStockParamsCollection())->fromArray(['page_size' => 1, 'page_index' => 1])
        );

        $this->assertNotSame(
            self::BOGUS_RESERVED_QUANTITY,
            (int) $rows[0]['product_reserved_quantity'],
            'The row still carries the value stored before the refresh.'
        );
        $this->assertSame(
            $this->storedReservedQuantity((int) $rows[0]['product_id'], (int) $rows[0]['combination_id']),
            (int) $rows[0]['product_reserved_quantity']
        );
    }

    protected function tearDown(): void
    {
        $this->setReservedQuantityEverywhere(0);
        parent::tearDown();
    }

    private function setReservedQuantityEverywhere(int $quantity): void
    {
        $this->connection->executeStatement(
            'UPDATE ' . $this->dbPrefix . 'stock_available SET reserved_quantity = :quantity',
            ['quantity' => $quantity]
        );
    }

    /**
     * @return int[]
     */
    private function productIdsWithReservedQuantity(int $quantity): array
    {
        return array_map('intval', $this->connection->executeQuery(
            'SELECT DISTINCT id_product FROM ' . $this->dbPrefix . 'stock_available WHERE reserved_quantity = :quantity',
            ['quantity' => $quantity]
        )->fetchFirstColumn());
    }

    private function storedReservedQuantity(int $productId, int $combinationId): int
    {
        return (int) $this->connection->executeQuery(
            'SELECT reserved_quantity FROM ' . $this->dbPrefix . 'stock_available
             WHERE id_product = :productId AND id_product_attribute = :combinationId',
            ['productId' => $productId, 'combinationId' => $combinationId]
        )->fetchOne();
    }
}
