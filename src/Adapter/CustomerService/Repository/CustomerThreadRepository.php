<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * @internal
 */
final class CustomerThreadRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ShopRepository $shopRepository,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Aggregates customer threads by status with a single GROUP BY query so
     * the page does not pay for one COUNT per status value.
     *
     * @return array<string, int>
     */
    public function countThreadsGroupedByStatus(ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('ct.status, COUNT(*) AS count')
            ->from($this->dbPrefix . 'customer_thread', 'ct')
            ->groupBy('ct.status')
        ;

        $this->applyShopConstraint($qb, $shopConstraint, 'ct');

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $counts = [];
        foreach ($rows ?: [] as $row) {
            $counts[(string) $row['status']] = (int) $row['count'];
        }

        return $counts;
    }

    /**
     * Aggregates customer messages by author kind (customer vs employee) in
     * a single grouped query, joined to customer_thread so multi-shop
     * scoping is preserved.
     *
     * @return array<string, int>
     */
    public function countMessagesGroupedByAuthor(ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('IF(cm.id_employee = 0, "customer", "employee") AS author, COUNT(*) AS count')
            ->from($this->dbPrefix . 'customer_message', 'cm')
            ->leftJoin('cm', $this->dbPrefix . 'customer_thread', 'ct', 'cm.id_customer_thread = ct.id_customer_thread')
            ->groupBy('author')
        ;

        $this->applyShopConstraint($qb, $shopConstraint, 'ct');

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $counts = [];
        foreach ($rows ?: [] as $row) {
            $counts[(string) $row['author']] = (int) $row['count'];
        }

        return $counts;
    }

    /**
     * Lists the "customer service" contact categories (e.g. "Webmaster",
     * "Customer service") visible in the given shop scope, with their
     * translated name/description.
     *
     * @return array<int, array{id_contact: int, name: string, description: string}>
     */
    public function getCustomerServiceContactCategories(int $languageId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('DISTINCT c.id_contact, cl.name, cl.description')
            ->from($this->dbPrefix . 'contact', 'c')
            ->innerJoin('c', $this->dbPrefix . 'contact_lang', 'cl', 'cl.id_contact = c.id_contact AND cl.id_lang = :languageId')
            ->where('c.customer_service = 1')
            ->orderBy('c.id_contact')
            ->setParameter('languageId', $languageId)
        ;

        if (!$shopConstraint->forAllShops()) {
            $shopIds = $this->shopRepository->getAssociatedShopIds($shopConstraint);
            if (empty($shopIds)) {
                return [];
            }

            $qb
                ->innerJoin('c', $this->dbPrefix . 'contact_shop', 'cs', 'cs.id_contact = c.id_contact')
                ->andWhere('cs.id_shop IN (:shopIds)')
                ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
            ;
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Counts open threads per contact category, and finds the oldest one
     * still waiting for a reply in each category (the thread with the
     * least recently updated `date_upd`).
     *
     * @return array<int, array{count: int, oldestThreadId: int|null}>
     */
    public function countOpenThreadsGroupedByContact(ShopConstraint $shopConstraint): array
    {
        $shopRestriction = '';
        $params = ['status' => CustomerThreadStatus::OPEN];
        $types = [];

        if (!$shopConstraint->forAllShops()) {
            $shopIds = $this->shopRepository->getAssociatedShopIds($shopConstraint);
            if (empty($shopIds)) {
                return [];
            }

            $shopRestriction = ' AND %1$s.id_shop IN (:shopIds)';
            $params['shopIds'] = $shopIds;
            $types['shopIds'] = ArrayParameterType::INTEGER;
        }

        $sql = '
            SELECT ct.id_contact, COUNT(*) AS thread_count, (
                SELECT ct2.id_customer_thread
                FROM ' . $this->dbPrefix . 'customer_thread ct2
                WHERE ct2.id_contact = ct.id_contact AND ct2.status = :status' . sprintf($shopRestriction, 'ct2') . '
                ORDER BY ct2.date_upd ASC
                LIMIT 1
            ) AS oldest_thread_id
            FROM ' . $this->dbPrefix . 'customer_thread ct
            WHERE ct.status = :status AND ct.id_contact IS NOT NULL' . sprintf($shopRestriction, 'ct') . '
            GROUP BY ct.id_contact
        ';

        $rows = $this->connection->executeQuery($sql, $params, $types)->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['id_contact']] = [
                'count' => (int) $row['thread_count'],
                'oldestThreadId' => null !== $row['oldest_thread_id'] ? (int) $row['oldest_thread_id'] : null,
            ];
        }

        return $result;
    }

    /**
     * Applies shop scoping on the customer_thread table alias.
     *
     * ShopConstraintTrait is intentionally not used here for two reasons:
     * - customer_thread has no id_shop_group column (only id_shop), so the trait's
     *   id_shop_group clause would produce an invalid SQL query.
     * - The queries need an explicit alias qualifier (e.g. ct.id_shop) because
     *   customer_thread is not always the main FROM table (see countMessagesGroupedByAuthor).
     *
     * ShopRepository::getAssociatedShopIds() resolves both single-shop and group
     * constraints to a list of shop IDs, so no separate fast-path is needed.
     */
    private function applyShopConstraint(QueryBuilder $qb, ShopConstraint $shopConstraint, string $alias): void
    {
        if ($shopConstraint->forAllShops()) {
            return;
        }

        $shopIds = $this->shopRepository->getAssociatedShopIds($shopConstraint);
        if (empty($shopIds)) {
            $qb->andWhere('1 = 0');

            return;
        }

        $qb
            ->andWhere(sprintf('%s.id_shop IN (:shopIds)', $alias))
            ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
        ;
    }
}
