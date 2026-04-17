<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

class CombinationStockMovementsFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I search stock movements of combination :combinationReference I should get following results:
     */
    public function assertStockMovementsOfCombinationForDefaultShop(
        string $combinationReference,
        TableNode $table
    ): void {
        $this->assertStockMovements(
            new GetCombinationStockMovements(
                $this->getSharedStorage()->get($combinationReference),
                $this->getDefaultShopId()
            ),
            $table
        );
    }

    /**
     * @When I search stock movements of combination :combinationReference with offset :offset and limit :limit I should get following results:
     */
    public function assertStockMovementsPageOfCombinationForDefaultShop(
        string $combinationReference,
        int $offset,
        int $limit,
        TableNode $table
    ): void {
        $this->assertStockMovements(
            new GetCombinationStockMovements(
                $this->getSharedStorage()->get($combinationReference),
                $this->getDefaultShopId(),
                $offset,
                $limit
            ),
            $table
        );
    }

    private function assertStockMovements(GetCombinationStockMovements $query, TableNode $table): void
    {
        $combinationStockMovements = $this->getQueryBus()->handle($query);
        $tableRows = $table->getColumnsHash();

        Assert::assertCount(
            count($tableRows),
            $combinationStockMovements,
            'Unexpected history size'
        );
        foreach ($tableRows as $index => $tableRow) {
            /** @var StockMovement $stockMovement */
            $stockMovement = $combinationStockMovements[$index];

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
