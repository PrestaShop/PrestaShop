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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;

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
    public function getLastStockMovements(
        StockId $stockId,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('sm.*')
            ->from($this->dbPrefix . 'stock_mvt', 'sm')
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
                // SQL doesn't allow use to multiply physical_quantity by negative number because its unsignedInt
                'SUM(IF(sign = 1, physical_quantity, 0)) delta_quantity_positive',
                'SUM(IF(sign = -1, physical_quantity, 0)) delta_quantity_negative',
                'MIN(sm.date_add) date_add_min',
                'MAX(sm.date_add) date_add_max'
            )
        ;

        // Add grouping condition to get orders and edition rows alternatively
        $this->addGroupingCondition($queryBuilder);

        $queryBuilder
            ->orderBy('id_stock_mvt_min', 'DESC')
            ->andWhere('sm.id_stock = :stockId')
            ->setParameter('stockId', $stockId->getValue())
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        // It is CRITICAL to reset the counter before each request
        $this->connection->executeStatement('SET @grouping_id := null');
        $result = $queryBuilder->execute()->fetchAllAssociative();
        foreach ($result as $key => $row) {
            $totalQuantity = $row['delta_quantity_positive'] - $row['delta_quantity_negative'];
            if ($totalQuantity === 0) {
                unset($result[$key]);
                continue;
            }
            $result[$key]['delta_quantity'] = $totalQuantity;
        }

        return $result;
    }

    private function addGroupingCondition(QueryBuilder $queryBuilder): void
    {
        /**
         * This is where the whole magic happens, this query is a bit hard to understand so it deserves explanation. The idea
         * of this query is to group the stock movement based on a criteria: is it a stock movement made by an employee in the
         * product page for an edition or is it a stock movement linked to a customer buying the product? And the criteria for
         * this is checking if stock_movement.id_order is NULL or NOT.
         *
         * So that's our grouping condition, but then it goes further because we want the employee editions to be returned as
         * a single row whereas the customer orders should be aggregated together between each edition. That's where our grouping_id
         * column comes into action, it is generated based on our grouping condition (sm.id_order IS NULL) and basically each time the
         * condition is met we update @grouping_id variable by assigning it the lowest stock movement ID, this indicates that our list
         * changed from a state of single edition movement to a state of grouped orders movements.
         *
         * But it wouldn't be enough because two rows share the same grouping_id, so we need an extra column grouping_type which indicates
         * which type of movement we are dealing with. Finally by concatenating those two infos we get a grouping_name column which is the
         * proper criteria that we are gonna be able to use to group our rows properly using a groupBy(grouping_name).
         *
         * For more clarity here is a simplified subset of the query so that you can picture the expected result, you can see how each
         * row changes from the edition to the orders group, and that orders rows are an aggregate of multiple stock movements which each
         * delta_quantity was summed to get their total.
         *
         * | id_stock_mvt_min | id_stock_mvt_count | id_stock_mvt_list | id_order_list | id_employee_list | delta_quantity | grouping_id | grouping_type | grouping_name |
         * | ---------------- | ------------------ | ----------------- | ------------- | ---------------- | -------------- | ----------- | ------------- | ------------- |
         * | 7                | 3                  | 9,8,7             | 12,11,10      | 1,1,1            | -6             | 6           | orders         | orders-6       |
         * | 6                | 1                  | 6                 | NULL          | 1                | 5              | 6           | edition        | edition-6      |
         * | 4                | 2                  | 5,4               | 9,8           | 1,1              | -9             | 3           | orders         | orders-3       |
         * | 3                | 1                  | 3                 | NULL          | 1                | 10             | 3           | edition        | edition-3      |
         * | 2                | 1                  | 2                 | 6             | 1                | -2             | 1           | orders         | orders-1       |
         *
         * Note: you can see that an orders row is not necessarily composed of multiple stock movements, the important is that it is between two employee edition rows,
         * however we could have multiple edition rows one after another if no products were bought in between.
         *
         * The real query has more columns returned, not all of them are being used currently but it's a good thing to keep them for future use
         * (maybe we'll need to add a link to all related orders for example) and as a demonstration.
         *
         * Note: we don't explicitly need the grouping_type column to handle our grouping conditions but we need it later to easily identify the type of the row.
         */
        $stockMovementId = 'sm.id_stock_mvt';
        $groupingCondition = 'sm.id_order IS NULL';
        $queryBuilder
            ->addSelect(
                "MIN(@grouping_id := CASE WHEN @grouping_id IS NULL THEN $stockMovementId WHEN $groupingCondition THEN $stockMovementId ELSE @grouping_id END) grouping_id",
                "CASE WHEN $groupingCondition THEN CONCAT('edition-', $stockMovementId) ELSE CONCAT('orders-', @grouping_id) END grouping_name",
                "CASE WHEN $groupingCondition THEN 'edition' ELSE 'orders' END grouping_type"
            )
            ->groupBy('grouping_name')
        ;
    }
}
