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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractGetStockMovementsHandler
{
    /**
     * @var StockAvailableRepository
     */
    protected $stockAvailableRepository;

    /**
     * @var StockMovementRepository
     */
    protected $stockMovementRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        StockMovementRepository $stockMovementRepository,
        TranslatorInterface $translator
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->stockMovementRepository = $stockMovementRepository;
        $this->translator = $translator;
    }

    /**
     * @return StockMovement[]
     */
    protected function getStockMovements(StockId $stockId, int $offset, int $limit): array
    {
        $lastStockMovements = $this->stockMovementRepository->getLastStockMovements(
            $stockId,
            $offset,
            $limit
        );

        return array_map(
            function (array $historyRow): StockMovement {
                return $historyRow['grouping_type'] === StockMovement::EDITION_TYPE
                    ? $this->createEditionStockMovement($historyRow)
                    : $this->createOrdersStockMovement($historyRow)
                ;
            },
            $lastStockMovements
        );
    }

    /**
     * @param array<string, string|int|null> $historyRow
     *
     * @return StockMovement
     */
    protected function createEditionStockMovement(array $historyRow): StockMovement
    {
        $employeeName = $this->translator->trans('%firstname% %lastname%', [
            '%firstname%' => $historyRow['employee_firstname'],
            '%lastname%' => $historyRow['employee_lastname'],
        ],
            'Admin.Global'
        );

        return StockMovement::createEditionMovement(
            $historyRow['date_add_min'],
            (int) $historyRow['id_stock_mvt_min'],
            (int) $historyRow['id_stock_list'],
            (int) $historyRow['id_order_list'],
            (int) $historyRow['id_employee_list'],
            $employeeName,
            (int) $historyRow['delta_quantity']
        );
    }

    /**
     * @param array<string, string|int|null> $historyRow
     *
     * @return StockMovement
     */
    protected function createOrdersStockMovement(array $historyRow): StockMovement
    {
        return StockMovement::createOrdersMovement(
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
