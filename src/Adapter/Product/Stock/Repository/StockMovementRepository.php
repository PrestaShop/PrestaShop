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
     * Returns the last stock movements with groupings.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLastStockMovementHistories(
        StockMovementHistorySettings $historySettings,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $queryBuilder = $this
            ->createFilterQueryBuilder(
                $historySettings->getMainFilter(),
                $offset,
                $limit
            )
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
        $this->updateQueryBuilderWithGroupings(
            $queryBuilder,
            $historySettings->getSingleFilter()
        );
        if ($historySettings->isZeroQuantityGroupingExcluded()) {
            $queryBuilder->andHaving('delta_quantity != 0');
        }

        return $queryBuilder->execute()->fetchAllAssociative();
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
            ->select('sm.*')
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
        if (null !== $filter->isGroupedByOrderAssociation()) {
            $queryBuilder->andWhere(
                'sm.id_order IS ' . ($filter->isGroupedByOrderAssociation() ? 'NOT NULL' : 'NULL')
            );
        }

        return $queryBuilder;
    }

    /**
     * Updates a query builder with a filter to group rows together
     *
     * @param QueryBuilder $queryBuilder Query builder to update
     * @param StockMovementFilter $singleFilter Filter to exclude single rows from groupings
     */
    protected function updateQueryBuilderWithGroupings(
        QueryBuilder $queryBuilder,
        StockMovementFilter $singleFilter
    ): void {
        $pkColumn = 'sm.id_stock_mvt';
        $groupingIdColumn = 'grouping_id';
        $groupingNameColumn = 'grouping_name';
        $groupingQueryBuilder = $this->createFilterQueryBuilder($singleFilter);
        $groupingCondition = (string) $groupingQueryBuilder->getQueryPart('where');
        $queryBuilder
//            ->addSelect(
//                sprintf(
//                    'MIN(@%2$s := CASE WHEN @%2$s IS NULL THEN %1$s WHEN %3$s THEN %1$s ELSE @%2$s END) %2$s',
//                    $pkColumn,
//                    $groupingIdColumn,
//                    $groupingCondition
//                ),
//                sprintf(
//                    'CASE WHEN %s THEN CONCAT(\'single-\', %s) ELSE CONCAT(\'range-\', @%s) END %s',
//                    $groupingCondition,
//                    $pkColumn,
//                    $groupingIdColumn,
//                    $groupingNameColumn
//                )
//            )
            ->addSelect(
                implode(' ', [
                    "MIN(@$groupingIdColumn := CASE",
                    "WHEN @$groupingIdColumn IS NULL THEN $pkColumn",
                    "WHEN $groupingCondition THEN $pkColumn",
                    "ELSE @$groupingIdColumn",
                    "END) $groupingIdColumn",
                ]),
                implode(' ', [
                    "CASE WHEN $groupingCondition THEN CONCAT('single-', $pkColumn)",
                    "ELSE CONCAT('range-', @$groupingIdColumn)",
                    "END $groupingNameColumn",
                ])
            )
            ->groupBy($groupingNameColumn)
        ;
        foreach ($groupingQueryBuilder->getParameters() as $parameter => $value) {
            $queryBuilder->setParameter($parameter, $value);
        }
    }
}
