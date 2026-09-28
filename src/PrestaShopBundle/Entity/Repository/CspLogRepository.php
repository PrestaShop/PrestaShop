<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\CspLog;

/**
 * @extends EntityRepository<CspLog>
 */
class CspLogRepository extends EntityRepository
{
    /**
     * Records one violation for (shop, directive, source): inserts the row on first sight,
     * otherwise increments its hit counter and refreshes date_upd.
     *
     * A single INSERT ... ON DUPLICATE KEY UPDATE is used deliberately: the DBAL QueryBuilder
     * cannot express it, and a SELECT-then-INSERT/UPDATE would race under a report flood.
     * Precedent for a DBAL executeStatement() upsert: src/Core/ExtraProperty/Value/ExtraPropertyWriter.php.
     *
     * @return bool true when a new row was inserted, false when an existing row's hit counter was
     *              bumped — so the caller only pays for the row-cap COUNT when the table actually grew
     */
    public function upsert(int $shopId, string $directive, string $source, ?string $documentUri): bool
    {
        $table = $this->getClassMetadata()->getTableName();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $sql = 'INSERT INTO ' . $table . ' (id_shop, directive, source, document_uri, hits, date_add, date_upd)'
            . ' VALUES (:shopId, :directive, :source, :documentUri, 1, :dateAdd, :dateUpd)'
            . ' ON DUPLICATE KEY UPDATE hits = hits + 1, date_upd = :dateUpdOnDuplicate';

        $affectedRows = $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'shopId' => $shopId,
            'directive' => $directive,
            'source' => $source,
            'documentUri' => $documentUri,
            'dateAdd' => $now,
            'dateUpd' => $now,
            'dateUpdOnDuplicate' => $now,
        ]);

        // MySQL returns 1 affected row for a fresh INSERT and 2 when ON DUPLICATE KEY UPDATE fires.
        return 1 === $affectedRows;
    }

    public function countByShop(int $shopId): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(1)')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->setParameter('shopId', $shopId)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Prunes the least significant rows of a shop, keeping the table bounded. Returns the number of
     * rows removed.
     *
     * Rows are ordered by hit count first, then by last-seen date, so the single-hit noise a report
     * flood produces is evicted before a source the storefront reports repeatedly. This stops an
     * unauthenticated flood of distinct made-up hosts from pushing the merchant's real, recurring
     * violations out of the capped log.
     */
    public function deleteLeastReportedByShop(int $shopId, int $limit): int
    {
        if ($limit <= 0) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $table = $this->getClassMetadata()->getTableName();

        $ids = $connection->createQueryBuilder()
            ->select('id_csp_log')
            ->from($table)
            ->where('id_shop = :shopId')
            ->orderBy('hits', 'ASC')
            ->addOrderBy('date_upd', 'ASC')
            ->addOrderBy('id_csp_log', 'ASC')
            ->setMaxResults($limit)
            ->setParameter('shopId', $shopId)
            ->executeQuery()
            ->fetchFirstColumn();

        if (empty($ids)) {
            return 0;
        }

        return (int) $connection->createQueryBuilder()
            ->delete($table)
            ->where('id_csp_log IN (:ids)')
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER)
            ->executeStatement();
    }

    /**
     * Clears the whole log of one shop. Returns the number of rows removed.
     */
    public function deleteByShop(int $shopId): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->delete($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->setParameter('shopId', $shopId)
            ->executeStatement();
    }
}
