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

use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementHistorySettings;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovementHistory;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;

abstract class AbstractGetStockMovementHistoryHandler
{
    /**
     * @var StockAvailableMultiShopRepository
     */
    protected $stockAvailableRepository;

    /**
     * @var StockMovementRepository
     */
    protected $stockMovementRepository;

    public function __construct(
        StockAvailableMultiShopRepository $stockAvailableRepository,
        StockMovementRepository $stockMovementRepository
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    /**
     * @return StockMovementHistory[]
     */
    protected function getStockMovementHistory(StockId $stockId, int $offset, int $limit): array
    {
        $historySettings = new StockMovementHistorySettings();
        // Filter stock movements by stock ID
        $historySettings->getMainFilter()->setStockIds($stockId);
        // Select stock movements without order as single histories, excluded from groupings
        $historySettings->getSingleFilter()->setGroupedByOrderAssociation(false);
        // Exclude stock movement groupings with zero quantity
        $historySettings->excludeGroupingWithZeroQuantity(true);

        $lastStockMovements = $this->stockMovementRepository->getLastStockMovementHistories(
            $historySettings,
            $offset,
            $limit
        );

        return array_map(
            function (array $historyRow): StockMovementHistory {
                return $historyRow['grouping_type'] === 'single'
                    ? $this->createSingleStockMovementHistory($historyRow)
                    : $this->createGroupStockMovementHistory($historyRow)
                ;
            },
            $lastStockMovements
        );
    }

    protected function createSingleStockMovementHistory(array $historyRow): StockMovementHistory
    {
        return StockMovementHistory::createSingleHistory(
            $historyRow['date_add_min'],
            (int) $historyRow['id_stock_mvt_min'],
            (int) $historyRow['id_stock_list'],
            (int) $historyRow['id_order_list'],
            (int) $historyRow['id_employee_list'],
            $historyRow['employee_firstname'],
            $historyRow['employee_lastname'],
            (int) $historyRow['delta_quantity']
        );
    }

    protected function createGroupStockMovementHistory(array $historyRow): StockMovementHistory
    {
        return StockMovementHistory::createGroupHistory(
            $historyRow['date_add_min'],
            $historyRow['date_add_max'],
            explode(',', $historyRow['id_stock_mvt_list']),
            explode(',', $historyRow['id_stock_list']),
            explode(',', $historyRow['id_order_list']),
            explode(',', $historyRow['id_employee_list']),
            (int) $historyRow['delta_quantity']
        );
    }
}
