<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Stats\Repository;

use Doctrine\DBAL\Connection;

/**
 * Provides the aggregate read queries backing the Stats page KPI boxes.
 *
 * @todo: multishop is scoped to the current shop context (array of shop ids), shop groups sharing data
 * across the group are not considered, matching the existing precedent in CategoryRepository.
 */
class StatsRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix
    ) {
    }

    public function countVisits(string $dateFrom, string $dateTo, bool $unique, array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select($unique ? 'COUNT(DISTINCT id_guest)' : 'COUNT(*)')
            ->from($this->dbPrefix . 'connections')
            ->where('date_add BETWEEN :dateFrom AND :dateTo')
            ->andWhere('id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countAbandonedCarts(string $dateFrom, string $dateTo, array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT c.id_guest)')
            ->from($this->dbPrefix . 'cart', 'c')
            ->where('c.date_add BETWEEN :dateFrom AND :dateTo')
            ->andWhere('c.id_shop IN (:shopIds)')
            ->andWhere(sprintf(
                'NOT EXISTS (SELECT 1 FROM %sorders o WHERE o.id_cart = c.id_cart)',
                $this->dbPrefix
            ))
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countInstalledModules(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT m.id_module)')
            ->from($this->dbPrefix . 'module', 'm')
            ->innerJoin('m', $this->dbPrefix . 'module_shop', 'ms', 'ms.id_module = m.id_module')
            ->where('ms.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countDisabledModules(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT m.id_module)')
            ->from($this->dbPrefix . 'module', 'm')
            ->leftJoin(
                'm',
                $this->dbPrefix . 'module_shop',
                'ms',
                'ms.id_module = m.id_module AND ms.id_shop IN (:shopIds)'
            )
            ->where('ms.id_module IS NULL')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    /**
     * @return array{with_stock: int, total: int}
     */
    public function getProductStockCounters(array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('SUM(IF(IFNULL(stock.quantity, 0) > 0, 1, 0)) AS with_stock, COUNT(*) AS total')
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN (:shopIds)')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->leftJoin(
                'p',
                $this->dbPrefix . 'stock_available',
                'stock',
                'stock.id_product = p.id_product AND (stock.id_product_attribute = 0 OR stock.id_product_attribute = pa.id_product_attribute) AND stock.id_shop IN (:shopIds)'
            )
            ->where('ps.active = 1')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();

        return [
            'with_stock' => (int) ($row['with_stock'] ?? 0),
            'total' => (int) ($row['total'] ?? 0),
        ];
    }

    public function getProductAverageGrossMargin(array $shopIds): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'AVG(1 - (IF(IFNULL(pas.wholesale_price, 0) = 0, ps.wholesale_price, pas.wholesale_price)'
            . ' / (IFNULL(pas.price, 0) + ps.price))) AS margin'
        )
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN (:shopIds)')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->leftJoin(
                'pa',
                $this->dbPrefix . 'product_attribute_shop',
                'pas',
                'pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop IN (:shopIds)'
            )
            ->where('ps.active = 1')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (float) $qb->executeQuery()->fetchOne();
    }

    public function countDisabledCategories(int $rootCategoryId, array $shopIds): int
    {
        return $this->countCategories($shopIds, $rootCategoryId, true);
    }

    public function countTotalCategories(array $shopIds): int
    {
        return $this->countCategories($shopIds, null, false);
    }

    private function countCategories(array $shopIds, ?int $excludeCategoryId, bool $disabledOnly): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT c.id_category)')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin('c', $this->dbPrefix . 'category_shop', 'cs', 'cs.id_category = c.id_category AND cs.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        if ($disabledOnly) {
            $qb->andWhere('c.active = 0');
        }
        if (null !== $excludeCategoryId) {
            $qb->andWhere('c.id_category != :excludeCategoryId')
                ->setParameter('excludeCategoryId', $excludeCategoryId)
            ;
        }

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countEmptyCategories(int $rootCategoryId, array $shopIds): int
    {
        $total = $this->countCategories($shopIds, $rootCategoryId, false);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT cp.id_category)')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin('c', $this->dbPrefix . 'category_shop', 'cs', 'cs.id_category = c.id_category AND cs.id_shop IN (:shopIds)')
            ->leftJoin('c', $this->dbPrefix . 'category_product', 'cp', 'cp.id_category = c.id_category')
            ->where('c.id_category != :excludeCategoryId')
            ->setParameter('excludeCategoryId', $rootCategoryId)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $used = (int) $qb->executeQuery()->fetchOne();

        return $total - $used;
    }

    /**
     * @return array{disabled: int, total: int}
     */
    public function getProductActivationCounters(array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('SUM(IF(ps.active = 0, 1, 0)) AS disabled, COUNT(*) AS total')
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();

        return [
            'disabled' => (int) ($row['disabled'] ?? 0),
            'total' => (int) ($row['total'] ?? 0),
        ];
    }

    public function getTotalSales(string $dateFrom, string $dateTo, array $shopIds): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('SUM((o.total_paid_tax_excl - o.total_shipping_tax_excl) / o.conversion_rate)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.current_state = os.id_order_state')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('os.logable = 1')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (float) $qb->executeQuery()->fetchOne();
    }

    public function countOrders(string $dateFrom, string $dateTo, array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.current_state = os.id_order_state')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('os.logable = 1')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countDistinctProductsSold(string $dateFrom, string $dateTo, array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(DISTINCT od.product_id)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_detail', 'od', 'o.id_order = od.id_order')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    /**
     * @return array{id_category: int, sales: float}|null
     */
    public function getBestSellingCategory(string $dateFrom, string $dateTo, array $shopIds): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('ca.id_category, SUM(t.total_price_sold) AS sales')
            ->from($this->dbPrefix . 'category', 'ca')
            ->innerJoin('ca', $this->dbPrefix . 'category_product', 'capr', 'ca.id_category = capr.id_category')
            ->leftJoin(
                'capr',
                '(' . $this->connection->createQueryBuilder()
                    ->select('od.product_id, SUM(od.unit_price_tax_excl * od.product_quantity) / o.conversion_rate AS total_price_sold')
                    ->from($this->dbPrefix . 'order_detail', 'od')
                    ->innerJoin('od', $this->dbPrefix . 'orders', 'o', 'o.id_order = od.id_order')
                    ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
                    ->andWhere('o.id_shop IN (:shopIds)')
                    ->groupBy('od.product_id, o.conversion_rate')
                    ->getSQL() . ')',
                't',
                't.product_id = capr.id_product'
            )
            ->where('ca.level_depth > 1')
            ->groupBy('ca.id_category')
            ->orderBy('sales', 'DESC')
            ->setMaxResults(1)
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();

        return $row ?: null;
    }

    public function countPendingMessages(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'customer_thread', 'ct')
            ->where('(ct.status LIKE :pending OR ct.status = :open)')
            ->andWhere('ct.id_shop IN (:shopIds)')
            ->setParameter('pending', '%pending%')
            ->setParameter('open', 'open')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    /**
     * @return array<int, array{question: string, reply: string}>
     */
    public function getMessageResponseDelays(string $dateFrom, string $dateTo, array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('MIN(cm1.date_add) AS question, MIN(cm2.date_add) AS reply')
            ->from($this->dbPrefix . 'customer_message', 'cm1')
            ->innerJoin(
                'cm1',
                $this->dbPrefix . 'customer_message',
                'cm2',
                'cm1.id_customer_thread = cm2.id_customer_thread AND cm1.date_add < cm2.date_add'
            )
            ->innerJoin('cm1', $this->dbPrefix . 'customer_thread', 'ct', 'cm1.id_customer_thread = ct.id_customer_thread')
            ->where('cm1.date_add BETWEEN :dateFrom AND :dateTo')
            ->andWhere('cm1.id_employee = 0')
            ->andWhere('cm2.id_employee != 0')
            ->andWhere('ct.id_shop IN (:shopIds)')
            ->groupBy('cm1.id_customer_thread')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<int, int> one entry per closed thread, its message count
     */
    public function getMessageCountsPerClosedThread(string $dateFrom, string $dateTo, array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*) AS messages')
            ->from($this->dbPrefix . 'customer_thread', 'ct')
            ->leftJoin('ct', $this->dbPrefix . 'customer_message', 'cm', 'ct.id_customer_thread = cm.id_customer_thread')
            ->where('ct.date_add BETWEEN :dateFrom AND :dateTo')
            ->andWhere('ct.status = :closed')
            ->andWhere('ct.id_shop IN (:shopIds)')
            ->groupBy('ct.id_customer_thread')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('closed', 'closed')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return array_map('intval', array_column($qb->executeQuery()->fetchAllAssociative(), 'messages'));
    }

    public function getPurchases(string $dateFrom, string $dateTo, int $averageProductMarginPercent, array $shopIds): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'SUM(od.product_quantity * IF('
            . 'od.purchase_supplier_price > 0,'
            . ' od.purchase_supplier_price / o.conversion_rate,'
            . ' od.original_product_price * :averageMargin / 100'
            . ')) AS total_purchase_price'
        )
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_detail', 'od', 'o.id_order = od.id_order')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.current_state = os.id_order_state')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('os.logable = 1')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('averageMargin', $averageProductMarginPercent)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (float) $qb->executeQuery()->fetchOne();
    }

    /**
     * Raw per-order data needed to compute the "expenses" (module/carrier fees) KPI.
     * The fee calculation itself stays outside the repository since it reads dynamic,
     * per-module/per-carrier Configuration keys rather than SQL-aggregatable data.
     *
     * @return array<int, array{total_paid_tax_incl: float, total_shipping_tax_excl: float, module: string, id_country: int, id_currency: int, carrier_reference: int}>
     */
    public function getOrdersForExpensesComputation(string $dateFrom, string $dateTo, array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'o.total_paid_tax_incl / o.conversion_rate AS total_paid_tax_incl',
            'o.total_shipping_tax_excl / o.conversion_rate AS total_shipping_tax_excl',
            'o.module',
            'a.id_country',
            'o.id_currency',
            'c.id_reference AS carrier_reference'
        )
            ->from($this->dbPrefix . 'orders', 'o')
            ->leftJoin('o', $this->dbPrefix . 'address', 'a', 'o.id_address_delivery = a.id_address')
            ->leftJoin('o', $this->dbPrefix . 'carrier', 'c', 'o.id_carrier = c.id_carrier')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.current_state = os.id_order_state')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('os.logable = 1')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array{male: int, female: int, neutral: int, total: int}|null
     */
    public function getCustomerGenderCounters(array $shopIds): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'SUM(IF(g.id_gender IS NOT NULL, 1, 0)) AS total',
            'SUM(IF(c.id_gender = :male, 1, 0)) AS male',
            'SUM(IF(c.id_gender = :female, 1, 0)) AS female',
            'SUM(IF(c.id_gender = :neutral, 1, 0)) AS neutral'
        )
            ->from($this->dbPrefix . 'customer', 'c')
            ->leftJoin('c', $this->dbPrefix . 'gender', 'g', 'c.id_gender = g.id_gender')
            ->where('c.active = 1')
            ->andWhere('c.id_shop IN (:shopIds)')
            ->setParameter('male', 0)
            ->setParameter('female', 1)
            ->setParameter('neutral', 2)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();
        if (!$row || !$row['total']) {
            return null;
        }

        return [
            'total' => (int) $row['total'],
            'male' => (int) $row['male'],
            'female' => (int) $row['female'],
            'neutral' => (int) $row['neutral'],
        ];
    }

    public function getAverageCustomerAgeInDays(array $shopIds): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('AVG(DATEDIFF(CURRENT_DATE(), c.birthday))')
            ->from($this->dbPrefix . 'customer', 'c')
            ->where('c.active = 1')
            ->andWhere('c.birthday IS NOT NULL')
            ->andWhere("c.birthday != '0000-00-00'")
            ->andWhere('c.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (float) $qb->executeQuery()->fetchOne();
    }

    public function countActiveNewsletterCustomers(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'customer', 'c')
            ->where('c.newsletter = 1')
            ->andWhere('c.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countActiveEmailSubscriptions(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'emailsubscription', 'e')
            ->where('e.active = 1')
            ->andWhere('e.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    /**
     * @return array{id_country: int, orders: int}|null
     */
    public function getMainCountry(string $dateFrom, string $dateTo, array $shopIds): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.id_country', 'COUNT(*) AS orders')
            ->from($this->dbPrefix . 'orders', 'o')
            ->leftJoin('o', $this->dbPrefix . 'address', 'a', 'o.id_address_delivery = a.id_address')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->groupBy('a.id_country')
            ->orderBy('orders', 'DESC')
            ->setMaxResults(1)
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();

        return $row ?: null;
    }

    public function countActiveCustomers(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'customer', 'c')
            ->where('c.active = 1')
            ->andWhere('c.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    public function countValidOrders(array $shopIds): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.valid = 1')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }

    /**
     * @return array{orders: int, total_paid_tax_excl: float}
     */
    public function getAverageOrderValueCounters(string $dateFrom, string $dateTo, array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(o.id_order) AS orders', 'SUM(o.total_paid_tax_excl / o.conversion_rate) AS total_paid_tax_excl')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.invoice_date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('o.id_shop IN (:shopIds)')
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
        ;
        $row = $qb->executeQuery()->fetchAssociative();

        return [
            'orders' => (int) ($row['orders'] ?? 0),
            'total_paid_tax_excl' => (float) ($row['total_paid_tax_excl'] ?? 0),
        ];
    }
}
