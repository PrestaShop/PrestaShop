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
use LogicException;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovementHistory;

class StockMovementHistoryFeatureContext extends AbstractProductFeatureContext
{
    private const DATE_KEYS_BY_TYPE = [
        StockMovementHistory::SINGLE_TYPE => ['add'],
        StockMovementHistory::RANGE_TYPE => ['from', 'to'],
    ];

    /**
     * @When I search stock movement history of product :productReference I should get following results:
     */
    public function assertStockMovementHistoryOfProductForDefaultShop(
        string $productReference,
        TableNode $table
    ): void {
        $this->assertStockMovementHistory(
            new GetProductStockMovementHistory(
                $this->getSharedStorage()->get($productReference),
                $this->getDefaultShopId()
            ),
            $table
        );
    }

    /**
     * @When I search stock movement history of product :productReference with offset :offset and limit :limit I should get following results:
     */
    public function assertStockMovementHistoryPageOfProductForDefaultShop(
        string $productReference,
        int $offset,
        int $limit,
        TableNode $table
    ): void {
        $this->assertStockMovementHistory(
            new GetProductStockMovementHistory(
                $this->getSharedStorage()->get($productReference),
                $this->getDefaultShopId(),
                $offset,
                $limit
            ),
            $table
        );
    }

    private function assertStockMovementHistory(GetProductStockMovementHistory $query, TableNode $table): void
    {
        $stockMovementHistories = $this->getQueryBus()->handle($query);
        $tableRows = $table->getColumnsHash();

        Assert::assertCount(
            count($tableRows),
            $stockMovementHistories,
            'Unexpected history size'
        );
        foreach ($tableRows as $index => $tableRow) {
            $stockMovementHistory = $stockMovementHistories[$index];

            Assert::assertSame(
                $tableRow['type'],
                $stockMovementHistory->getType(),
                sprintf(
                    'Invalid stock movement history type, expected "%s" instead of "%s"',
                    $tableRow['type'],
                    $stockMovementHistory->getType()
                )
            );
            Assert::assertEquals(
                $tableRow['first_name'],
                $stockMovementHistory->getEmployeeFirstName(),
                sprintf(
                    'Invalid employee first name of stock movement history, expected "%s" instead of "%s"',
                    $tableRow['first_name'],
                    $stockMovementHistory->getEmployeeFirstName()
                )
            );
            Assert::assertEquals(
                $tableRow['last_name'],
                $stockMovementHistory->getEmployeeLastName(),
                sprintf(
                    'Invalid employee last name of stock movement history, expected "%s" instead of "%s"',
                    $tableRow['last_name'],
                    $stockMovementHistory->getEmployeeLastName()
                )
            );
            Assert::assertSame(
                (int) $tableRow['delta_quantity'],
                $stockMovementHistory->getDeltaQuantity(),
                sprintf(
                    'Invalid delta quantity of stock movement history, expected "%d" instead of "%d"',
                    $tableRow['delta_quantity'],
                    $stockMovementHistory->getDeltaQuantity()
                )
            );
            foreach ($this->resolveHistoryDateKeys($stockMovementHistory->getType()) as $dateKey) {
                Assert::assertInstanceOf(
                    DateTimeImmutable::class,
                    $stockMovementHistory->getDate($dateKey)
                );
            }
        }
    }

    /**
     * @return string[]
     */
    private function resolveHistoryDateKeys(string $type): array
    {
        if (array_key_exists($type, self::DATE_KEYS_BY_TYPE)) {
            return self::DATE_KEYS_BY_TYPE[$type];
        }
        throw new LogicException(
            sprintf(
                'Invalid history type "%s" given, expected any of: %s.',
                $type,
                implode(', ', array_keys(self::DATE_KEYS_BY_TYPE))
            )
        );
    }
}
