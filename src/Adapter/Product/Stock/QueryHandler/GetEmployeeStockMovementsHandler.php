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
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementFilter;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetEmployeeStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryHandler\GetEmployeeStockMovementsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

class GetEmployeeStockMovementsHandler implements GetEmployeeStockMovementsHandlerInterface
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

    /**
     * {@inheritDoc}
     */
    public function handle(GetEmployeeStockMovements $query): array
    {
        $stockId = $this->stockAvailableRepository->getStockIdByProduct($query->getProductId());
        $movementsData = $this->stockMovementRepository->getLastStockMovements(
            (new StockMovementFilter())->setStockIds($stockId),
            $query->getOffset(),
            $query->getLimit()
        );

        $movements = [];
        foreach ($movementsData as $movementDatum) {
            $movements[] = new StockMovement(
                (int) $movementDatum['id_stock_mvt'],
                (int) $movementDatum['id_stock'],
                (int) $movementDatum['id_stock_mvt_reason'],
                (int) $movementDatum['id_order'],
                (int) $movementDatum['id_employee'],
                $movementDatum['employee_firstname'],
                $movementDatum['employee_lastname'],
                (int) $movementDatum['physical_quantity'] * (int) $movementDatum['sign'],
                new DateTime($movementDatum['date_add'])
            );
        }

        return $movements;
    }
}
