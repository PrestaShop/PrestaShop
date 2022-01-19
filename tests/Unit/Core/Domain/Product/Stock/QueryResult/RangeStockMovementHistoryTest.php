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

namespace Tests\Unit\Core\Domain\Product\Stock\Query;

use DateTime;
use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\RangeStockMovementHistory;

class RangeStockMovementHistoryTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int[] $stockMovementIds
     * @param int[] $stockIds
     * @param int[] $stockMovementReasonIds
     * @param int[] $orderIds
     * @param int[] $employeeIds
     * @param string|null $employeeName
     * @param int $deltaQuantity
     * @param string $fromDate
     * @param string $toDate
     * @param string $dateRange
     */
    public function testItIsSuccessfullyConstructed(
        array $stockMovementIds,
        array $stockIds,
        array $stockMovementReasonIds,
        array $orderIds,
        array $employeeIds,
        ?string $employeeName,
        int $deltaQuantity,
        string $fromDate,
        string $toDate,
        string $dateRange
    ): void {
        $dates = [
            'from' => new DateTime($fromDate),
            'to' => new DateTime($toDate),
        ];
        $history = new RangeStockMovementHistory(
            $deltaQuantity,
            $dates['from'],
            $dates['to']
        );
        $history
            ->setStockMovementIds(...$stockMovementIds)
            ->setStockIds(...$stockIds)
            ->setStockMovementReasonIds(...$stockMovementReasonIds)
            ->setOrderIds(...$orderIds)
            ->setEmployeeIds(...$employeeIds)
            ->setEmployeeName($employeeName)
        ;

        Assert::assertSame($stockMovementIds, $history->getStockMovementIds());
        Assert::assertSame($stockIds, $history->getStockIds());
        Assert::assertSame($stockMovementReasonIds, $history->getStockMovementReasonIds());
        Assert::assertSame($orderIds, $history->getOrderIds());
        Assert::assertSame($employeeIds, $history->getEmployeeIds());
        Assert::assertSame($employeeName, $history->getEmployeeName());
        Assert::assertSame($deltaQuantity, $history->getDeltaQuantity());
        Assert::assertSame($dates, $history->getDates());
        Assert::assertSame($dateRange, $history->getDateRange());
    }

    public function getValidValues(): Generator
    {
        yield 'order' => [
            'stockMovementIds' => [1, 2],
            'stockIds' => [3, 4],
            'stockMovementReasonIds' => [5, 6],
            'orderIds' => [7, 8],
            'employeeIds' => [9, 10],
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => -6,
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'dateRange' => '2022-01-13 18:20:58 - 2022-01-13 18:21:18',
        ];
        yield 'employee edition' => [
            'stockMovementIds' => [1, 2],
            'stockIds' => [3, 4],
            'stockMovementReasonIds' => [5, 6],
            'orderIds' => [],
            'employeeIds' => [9, 10],
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => 6,
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'dateRange' => '2022-01-13 18:20:58 - 2022-01-13 18:21:18',
        ];
        yield 'employee name is null' => [
            'stockMovementIds' => [1, 2],
            'stockIds' => [3, 4],
            'stockMovementReasonIds' => [5, 6],
            'orderIds' => [],
            'employeeIds' => [9, 10],
            'employeeName' => null,
            'deltaQuantity' => 6,
            'fromDate' => '2022-01-13 18:20:58',
            'toDate' => '2022-01-13 18:21:18',
            'dateRange' => '2022-01-13 18:20:58 - 2022-01-13 18:21:18',
        ];
    }
}
