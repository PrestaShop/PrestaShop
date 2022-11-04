<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
