<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

class StockMovementsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I search stock movements of product :productReference I should get following results:
     */
    public function assertStockMovementsOfProductForDefaultShop(
        string $productReference,
        TableNode $table
    ): void {
        $this->assertStockMovements(
            new GetProductStockMovements(
                $this->getSharedStorage()->get($productReference),
                $this->getDefaultShopId()
            ),
            $table
        );
    }

    /**
     * @When I search stock movements of product :productReference with offset :offset and limit :limit I should get following results:
     */
    public function assertStockMovementsPageOfProductForDefaultShop(
        string $productReference,
        int $offset,
        int $limit,
        TableNode $table
    ): void {
        $this->assertStockMovements(
            new GetProductStockMovements(
                $this->getSharedStorage()->get($productReference),
                $this->getDefaultShopId(),
                $offset,
                $limit
            ),
            $table
        );
    }

    private function assertStockMovements(GetProductStockMovements $query, TableNode $table): void
    {
        $productStockMovements = $this->getQueryBus()->handle($query);
        $tableRows = $table->getColumnsHash();

        Assert::assertCount(
            count($tableRows),
            $productStockMovements,
            'Unexpected history size'
        );
        foreach ($tableRows as $index => $tableRow) {
            /** @var StockMovement $stockMovement */
            $stockMovement = $productStockMovements[$index];

            Assert::assertSame(
                $tableRow['type'],
                $stockMovement->getType(),
                sprintf(
                    'Invalid stock movement event type, expected "%s" instead of "%s"',
                    $tableRow['type'],
                    $stockMovement->getType()
                )
            );
            Assert::assertEquals(
                $tableRow['employee'],
                $stockMovement->getEmployeeName(),
                sprintf(
                    'Invalid employee name of stock movement event, expected "%s" instead of "%s"',
                    $tableRow['employee'],
                    $stockMovement->getEmployeeName()
                )
            );
            Assert::assertSame(
                (int) $tableRow['delta_quantity'],
                $stockMovement->getDeltaQuantity(),
                sprintf(
                    'Invalid delta quantity of stock movement event, expected "%d" instead of "%d"',
                    $tableRow['delta_quantity'],
                    $stockMovement->getDeltaQuantity()
                )
            );
            foreach ($this->resolveHistoryDateKeys($stockMovement->getType()) as $dateKey) {
                Assert::assertInstanceOf(
                    DateTimeImmutable::class,
                    $stockMovement->getDate($dateKey)
                );
            }
        }
    }
}
