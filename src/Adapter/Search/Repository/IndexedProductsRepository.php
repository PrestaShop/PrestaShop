<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

final class IndexedProductsRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Counts searchable products and how many of them are already indexed.
     *
     * With a shop id, the product's settings of that shop are used. Without (all shops or group context), each product
     * is counted once, with the settings of its default shop — same as the legacy Shop::addSqlAssociation() behaviour.
     *
     * @return array{indexed: int, total: int}
     *
     * @throws DBALException
     */
    public function getIndexedProductsCount(?int $shopId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(*) AS total', 'SUM(ps.indexed) AS indexed')
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                null !== $shopId ? 'ps.id_product = p.id_product AND ps.id_shop = :shopId' : 'ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default'
            )
            ->where($qb->expr()->in('ps.visibility', ':visibilities'))
            ->andWhere('ps.active = 1')
            ->setParameter('visibilities', ['both', 'search'], Connection::PARAM_STR_ARRAY);

        if (null !== $shopId) {
            $qb->setParameter('shopId', $shopId);
        }

        $row = $qb->executeQuery()->fetchAssociative() ?: [];

        return [
            'indexed' => (int) ($row['indexed'] ?? 0),
            'total' => (int) ($row['total'] ?? 0),
        ];
    }
}
