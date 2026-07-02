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
