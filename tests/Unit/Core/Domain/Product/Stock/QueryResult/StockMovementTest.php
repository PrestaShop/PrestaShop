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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Stock\QueryResult;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use RuntimeException;

class StockMovementTest extends TestCase
{
    /**
     * @dataProvider getSingleHistoryValidValues
     */
    public function testSingleHistoryIsSuccessfullyConstructed(
        string $dateAdd,
        int $stockMovementId,
        int $stockId,
        ?int $orderId,
        int $employeeId,
        ?string $employeeName,
        int $deltaQuantity
    ): void {
        $history = StockMovement::createEditionMovement(
            $dateAdd,
            $stockMovementId,
            $stockId,
            $orderId,
            $employeeId,
            $employeeName,
            $deltaQuantity
        );
        Assert::assertTrue($history->isEdition());
        Assert::assertFalse($history->isFromOrders());
        Assert::assertSame(StockMovement::EDITION_TYPE, $history->getType());
        Assert::assertEquals(new DateTimeImmutable($dateAdd), $history->getDate('add'));
        Assert::assertSame([$stockMovementId], $history->getStockMovementIds());
        Assert::assertSame([$stockId], $history->getStockIds());
        Assert::assertSame(
            $orderId !== null ? [$orderId] : [],
            $history->getOrderIds()
        );
        Assert::assertSame([$employeeId], $history->getEmployeeIds());
        Assert::assertSame($employeeName, $history->getEmployeeName());
        Assert::assertSame($deltaQuantity, $history->getDeltaQuantity());
    }

    public function getSingleHistoryValidValues(): Generator
    {
        yield 'single history is order' => [
            'dateAdd' => '2022-01-13 18:21:33',
            'stockMovementId' => 1,
            'stockId' => 2,
            'orderId' => 3,
            'employeeId' => 4,
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => -5,
        ];
        yield 'single history is employee edition' => [
            'dateAdd' => '2022-01-13 18:21:33',
            'stockMovementId' => 1,
            'stockId' => 2,
            'orderId' => null,
            'employeeId' => 4,
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => 5,
        ];
        yield 'single history without employee name' => [
            'dateAdd' => '2022-01-13 18:21:33',
            'stockMovementId' => 1,
            'stockId' => 2,
            'orderId' => null,
            'employeeId' => 4,
            'employeeName' => null,
            'deltaQuantity' => 5,
        ];
    }

    /**
     * @dataProvider getGroupHistoryValidValues
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string[]|int[] $stockMovementIds
     * @param string[]|int[] $stockIds
     * @param string[]|int[] $orderIds
     * @param string[]|int[] $employeeIds
     * @param int $deltaQuantity
     */
    public function testGroupHistoryIsSuccessfullyConstructed(
        string $fromDate,
        string $toDate,
        array $stockMovementIds,
        array $stockIds,
        array $orderIds,
        array $employeeIds,
        int $deltaQuantity
    ): void {
        $history = StockMovement::createOrdersMovement(
            $fromDate,
            $toDate,
            $stockMovementIds,
            $stockIds,
            $orderIds,
            $employeeIds,
            $deltaQuantity
        );
        Assert::assertFalse($history->isEdition());
        Assert::assertTrue($history->isFromOrders());
        Assert::assertSame(StockMovement::ORDERS_TYPE, $history->getType());
        Assert::assertEquals(new DateTimeImmutable($fromDate), $history->getDate('from'));
        Assert::assertEquals(new DateTimeImmutable($toDate), $history->getDate('to'));
        Assert::assertEquals($stockMovementIds, $history->getStockMovementIds());
        Assert::assertEquals($stockIds, $history->getStockIds());
        Assert::assertEquals($orderIds, $history->getOrderIds());
        Assert::assertEquals($employeeIds, $history->getEmployeeIds());
        Assert::assertEquals($deltaQuantity, $history->getDeltaQuantity());
    }

    public function getGroupHistoryValidValues(): Generator
    {
        yield 'group history is order' => [
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'stockMovementIds' => [1, 2],
            'stockIds' => [3, 4],
            'orderIds' => [5, 6],
            'employeeIds' => [7, 8],
            'deltaQuantity' => -9,
        ];
        yield 'group history is employee edition' => [
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'stockMovementIds' => [1, 2],
            'stockIds' => [3, 4],
            'orderIds' => [5, 6],
            'employeeIds' => [],
            'deltaQuantity' => 9,
        ];
        yield 'group history is created from strings' => [
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'stockMovementIds' => ['1', '2'],
            'stockIds' => ['3', '4'],
            'orderIds' => ['5', '6'],
            'employeeIds' => ['7', '8'],
            'deltaQuantity' => 9,
        ];
    }

    /**
     * @dataProvider getInvalidDateKeyFromHistory
     */
    public function testHistoryFailsOnInvalidDateKey(StockMovement $history, string $key): void
    {
        $this->expectException(RuntimeException::class);

        $history->getDate($key);
    }

    public function getInvalidDateKeyFromHistory(): Generator
    {
        $singleHistory = StockMovement::createEditionMovement(
            '2022-01-13 18:21:33',
            1,
            2,
            null,
            4,
            'Penelope Cruz',
            5
        );
        $groupHistory = StockMovement::createOrdersMovement(
            '2022-01-13 18:20:58',
            '2022-01-13 18:21:18',
            [1, 2],
            [3, 4],
            [5, 6],
            [7, 8],
            -9
        );

        yield '"from" date key in single history' => [
            'history' => $singleHistory,
            'key' => 'from',
        ];
        yield '"to" date key in single history' => [
            'history' => $singleHistory,
            'key' => 'to',
        ];
        yield '"random" date key in single history' => [
            'history' => $singleHistory,
            'key' => 'random',
        ];
        yield '"add" date key in group history' => [
            'history' => $groupHistory,
            'key' => 'add',
        ];
        yield '"random" date key in group history' => [
            'history' => $groupHistory,
            'key' => 'random',
        ];
    }
}
