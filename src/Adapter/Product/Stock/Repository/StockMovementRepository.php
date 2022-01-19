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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class StockMovementRepository
{
    private const DEFAULT_LIMIT = 10;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Returns a new query builder with the given filter.
     */
    protected function createFilterQueryBuilder(
        StockMovementFilter $filter,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ): QueryBuilder {
        $queryBuilder = $this
            ->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'stock_mvt', 'sm')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        if (!empty($filter->getStockIds())) {
            $queryBuilder
                ->andWhere('sm.id_stock IN (:stockIds)')
                ->setParameter('stockIds', $filter->getStockIdsAsString())
            ;
        }
        if (!empty($filter->getReasonIds())) {
            $queryBuilder
                ->andWhere('sm.id_stock_mvt_reason IN (:reasonIds)')
                ->setParameter('reasonIds', $filter->getReasonIdsAsString())
            ;
        }
        if (null !== $filter->getIsOrder()) {
            $queryBuilder->andWhere(
                'sm.id_order IS ' . ($filter->getIsOrder() ? 'NOT NULL' : 'NULL')
            );
        }

        return $queryBuilder;
    }

    /**
     * Returns the last stock movements.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLastStockMovements(
        StockMovementFilter $filter,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        return $this
            ->createFilterQueryBuilder($filter, $offset, $limit)
            ->addOrderBy('sm.id_stock_mvt', 'DESC')
            ->execute()
            ->fetchAllAssociative()
        ;
    }

    /**
     * Returns the last stock movement histories.
     *
     * The main filter is first applied as 1st selection of stock movements.
     * The range filter is then applied as 2nd selection of single histories,
     * whereas the remaining stock movements are grouped into range histories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLastStockMovementHistories(
        StockMovementFilter $mainFilter,
        StockMovementFilter $rangeFilter,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $queryBuilder = $this
            ->createFilterQueryBuilder($mainFilter, $offset, $limit)
            ->select(
                'MIN(sm.id_stock_mvt) id_stock_mvt_min',
                'COUNT(id_stock_mvt) id_stock_mvt_count',
                'GROUP_CONCAT(id_stock_mvt) id_stock_mvt_list',
                'GROUP_CONCAT(id_stock) id_stock_list',
                'GROUP_CONCAT(id_stock_mvt_reason) id_stock_mvt_reason_list',
                'GROUP_CONCAT(id_order) id_order_list',
                'GROUP_CONCAT(id_employee) id_employee_list',
                'MIN(employee_firstname) employee_firstname',
                'MIN(employee_lastname) employee_lastname',
                'SUM(sm.sign * sm.physical_quantity) delta_quantity',
                'MIN(sm.date_add) date_add_min',
                'MAX(sm.date_add) date_add_max'
            )
            ->orderBy('id_stock_mvt_min', 'DESC')
        ;
        $this->updateQueryBuilderWithGroupByRange(
            $queryBuilder,
            $this->createFilterQueryBuilder($rangeFilter)
        );

        return $queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * Updates a query builder with a filter to group irrelevant rows in the same ranges
     *
     * @param QueryBuilder $queryBuilder Query builder to update
     * @param QueryBuilder $rangeQueryBuilder Query builder used as a range filter
     * @param string $pkColumn Primary key column name
     * @param string $rangeColumn Column name used to detect ranges
     * @param string $groupColumn Column name used to group rows into ranges
     */
    protected function updateQueryBuilderWithGroupByRange(
        QueryBuilder $queryBuilder,
        QueryBuilder $rangeQueryBuilder,
        string $pkColumn = 'sm.id_stock_mvt',
        string $rangeColumn = 'range_id',
        string $groupColumn = 'range_name'
    ): void {
        $rangeCondition = (string) $rangeQueryBuilder->getQueryPart('where');
        $queryBuilder
            ->addSelect(
                implode(' ', [
                    "MIN(@$rangeColumn := CASE",
                    "WHEN @$rangeColumn IS NULL THEN $pkColumn",
                    "WHEN $rangeCondition THEN $pkColumn",
                    "ELSE @$rangeColumn",
                    "END) $rangeColumn",
                ]),
                "CASE WHEN $rangeCondition THEN CONCAT('single-', $pkColumn) ELSE CONCAT('range-', @$rangeColumn) END $groupColumn"
            )
            ->groupBy($groupColumn)
        ;
        foreach ($rangeQueryBuilder->getParameters() as $parameter => $value) {
            $queryBuilder->setParameter($parameter, $value);
        }
    }
}
