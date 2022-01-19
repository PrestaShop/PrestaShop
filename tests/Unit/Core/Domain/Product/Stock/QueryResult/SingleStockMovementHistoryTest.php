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
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\SingleStockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

class SingleStockMovementHistoryTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testItIsSuccessfullyConstructed(
        int $stockMovementId,
        int $stockId,
        int $stockMovementReasonId,
        ?int $orderId,
        int $employeeId,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        string $employeeName,
        int $deltaQuantity,
        string $dateAdd
    ): void {
        $dateAddObject = new DateTime($dateAdd);
        $stockMovement = new StockMovement(
            $stockMovementId,
            $stockId,
            $stockMovementReasonId,
            $orderId,
            $employeeId,
            $employeeFirstName,
            $employeeLastName,
            $deltaQuantity,
            $dateAddObject
        );
        $history = new SingleStockMovementHistory($stockMovement);

        Assert::assertSame($stockMovement, $history->getStockMovement());
        Assert::assertSame([$stockMovementId], $history->getStockMovementIds());
        Assert::assertSame([$stockId], $history->getStockIds());
        Assert::assertSame([$stockMovementReasonId], $history->getStockMovementReasonIds());
        Assert::assertSame([$orderId], $history->getOrderIds());
        Assert::assertSame([$employeeId], $history->getEmployeeIds());
        Assert::assertSame($employeeName, $history->getEmployeeName());
        Assert::assertSame($deltaQuantity, $history->getDeltaQuantity());
        Assert::assertSame(['add' => $dateAddObject], $history->getDates());
        Assert::assertSame($dateAdd, $history->getDateRange());
    }

    public function getValidValues(): Generator
    {
        yield 'order' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 3,
            'orderId' => 4,
            'employeeId' => 5,
            'employeeFirstName' => 'Penelope',
            'employeeLastName' => 'Cruz',
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => -6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee edition' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => 'Penelope',
            'employeeLastName' => 'Cruz',
            'employeeName' => 'Penelope Cruz',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee firstname is empty' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => '',
            'employeeLastName' => 'Cruz',
            'employeeName' => 'Cruz',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee firstname is null' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => null,
            'employeeLastName' => 'Cruz',
            'employeeName' => 'Cruz',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee lastname is empty' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => 'Penelope',
            'employeeLastName' => '',
            'employeeName' => 'Penelope',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee lastname is null' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => 'Penelope',
            'employeeLastName' => null,
            'employeeName' => 'Penelope',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee firstname and lastname are empty' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => '',
            'employeeLastName' => '',
            'employeeName' => '',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
        yield 'employee firstname and lastname are null' => [
            'stockMovementId' => 1,
            'stockId' => 2,
            'stockMovementReasonId' => 12,
            'orderId' => null,
            'employeeId' => 5,
            'employeeFirstName' => null,
            'employeeLastName' => null,
            'employeeName' => '',
            'deltaQuantity' => 6,
            'dateAdd' => '2022-01-13 18:21:33',
        ];
    }
}
