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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\QueryHandler;

use DateTime;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementFilter;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetStockMovementHistories;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryHandler\GetStockMovementHistoriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\RangeStockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\SingleStockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovementHistory;

class GetStockMovementHistoriesHandler implements GetStockMovementHistoriesHandlerInterface
{
    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var StockMovementRepository
     */
    private $stockMovementRepository;

    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        StockMovementRepository $stockMovementRepository
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    public function handle(GetStockMovementHistories $query): array
    {
        $stockId = $this->stockAvailableRepository->getStockIdByProduct($query->getProductId());
        $mainFilter = (new StockMovementFilter())->setStockIds($stockId);
        $rangeFilter = (new StockMovementFilter())->setIsOrder(false);

        return array_map(
            function (array $historyRow): StockMovementHistory {
                return $this->createStockMovementHistory($historyRow);
            },
            $this->stockMovementRepository->getLastStockMovementHistories(
                $mainFilter,
                $rangeFilter,
                $query->getOffset(),
                $query->getLimit()
            )
        );
    }

    protected function createStockMovementHistory(array $historyRow): StockMovementHistory
    {
        if (1 === (int) $historyRow['id_stock_mvt_count']) {
            $stockMovementHistory = $this->createSingleStockMovementHistory($historyRow);
        } else {
            $stockMovementHistory = $this->createRangeStockMovementHistory($historyRow);
        }

        return $stockMovementHistory;
    }

    protected function createSingleStockMovementHistory(array $historyRow): SingleStockMovementHistory
    {
        return new SingleStockMovementHistory(
            new StockMovement(
                (int) $historyRow['id_stock_mvt_min'],
                (int) $historyRow['id_stock_list'],
                (int) $historyRow['id_stock_mvt_reason_list'],
                (int) $historyRow['id_order_list'],
                (int) $historyRow['id_employee_list'],
                $historyRow['employee_firstname'],
                $historyRow['employee_lastname'],
                (int) $historyRow['delta_quantity'],
                new DateTime($historyRow['date_add_min'])
            )
        );
    }

    protected function createRangeStockMovementHistory(array $historyRow): RangeStockMovementHistory
    {
        $history = new RangeStockMovementHistory(
            (int) $historyRow['delta_quantity'],
            new DateTimeImmutable($historyRow['date_add_min']),
            new DateTimeImmutable($historyRow['date_add_max'])
        );

        return $history
            ->setStockMovementIds(...$this->splitGroupData($historyRow['id_stock_mvt_list']))
            ->setStockIds(...$this->splitGroupData($historyRow['id_stock_list']))
            ->setStockMovementReasonIds(...$this->splitGroupData($historyRow['id_stock_mvt_reason_list']))
            ->setOrderIds(...$this->splitGroupData($historyRow['id_order_list']))
            ->setEmployeeIds(...$this->splitGroupData($historyRow['id_employee_list']))
        ;
    }

    protected function splitGroupData(string $data, string $type = 'int', string $separator = ','): array
    {
        $values = [];

        foreach (explode($separator, $data) as $value) {
            settype($value, $type);
            $values[] = $value;
        }

        return $values;
    }
}
