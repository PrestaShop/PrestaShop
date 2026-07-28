<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

class IndexedProductsRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Counts searchable products and how many of them are already indexed, scoped to the given shops.
     *
     * @param int[] $shopIds
     *
     * @return array{indexed: int, total: int}
     *
     * @throws DBALException
     */
    public function getIndexedProductsCount(array $shopIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(*) AS total', 'SUM(ps.indexed) AS indexed')
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product')
            ->where($qb->expr()->in('ps.visibility', ':visibilities'))
            ->andWhere('ps.active = 1')
            ->andWhere($qb->expr()->in('ps.id_shop', ':shopIds'))
            ->setParameter('visibilities', ['both', 'search'], Connection::PARAM_STR_ARRAY)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY);

        $row = $qb->executeQuery()->fetchAssociative() ?: [];

        return [
            'indexed' => (int) ($row['indexed'] ?? 0),
            'total' => (int) ($row['total'] ?? 0),
        ];
    }
}
