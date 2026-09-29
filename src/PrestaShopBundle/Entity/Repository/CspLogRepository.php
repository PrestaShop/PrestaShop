<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\CspLog;
use PrestaShopBundle\Entity\CspRule;

/**
 * @extends EntityRepository<CspLog>
 */
class CspLogRepository extends EntityRepository
{
    /** Cap the id list / delete size per statement so a huge log is cleared in bounded steps. */
    private const DELETE_CHUNK_SIZE = 500;

    private const DELETE_MAX_ATTEMPTS = 3;

    private const DELETE_RETRY_BASE_DELAY_US = 50000;

    /**
     * Records one violation for (shop, directive, source): inserts on first sight, else bumps hits/date_upd.
     * Uses INSERT ... ON DUPLICATE KEY UPDATE since a SELECT-then-write races under a flood (MySQL/MariaDB-specific).
     *
     * @return bool true on insert, false when an existing row was bumped, so a batch caller can skip the row-cap COUNT
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

        // MySQL returns 1 affected row for a fresh INSERT, 2 when ON DUPLICATE KEY UPDATE fires.
        return 1 === $affectedRows;
    }

    /** Seeds a log row (hits = 0) for a directly-added source so it shows in the log-driven grid; a no-op if a row exists. */
    public function insertPlaceholderIfAbsent(int $shopId, string $directive, string $source): void
    {
        $table = $this->getClassMetadata()->getTableName();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $sql = 'INSERT INTO ' . $table . ' (id_shop, directive, source, document_uri, hits, date_add, date_upd)'
            . ' VALUES (:shopId, :directive, :source, NULL, 0, :dateAdd, :dateUpd)'
            . ' ON DUPLICATE KEY UPDATE id_csp_log = id_csp_log';

        $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'shopId' => $shopId,
            'directive' => $directive,
            'source' => $source,
            'dateAdd' => $now,
            'dateUpd' => $now,
        ]);
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

    /** Whether the shop has any log row; cheaper than countByShop() when only existence matters (the baseline check). */
    public function existsByShop(int $shopId): bool
    {
        return (bool) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->executeQuery()
            ->fetchOne();
    }

    /** Prunes a shop's lowest-hit, oldest rows to keep the table bounded; allow-list-backed rows are never evicted. Returns the count removed. */
    public function deleteLeastReportedByShop(int $shopId, int $limit): int
    {
        if ($limit <= 0) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $table = $this->getClassMetadata()->getTableName();
        $ruleTable = $this->getEntityManager()->getClassMetadata(CspRule::class)->getTableName();

        // Re-select the victims inside the retry so a deadlock rollback never reuses stale ids.
        return $this->retryOnDeadlock(function () use ($connection, $table, $ruleTable, $shopId, $limit): int {
            $ids = $connection->createQueryBuilder()
                ->select('l.id_csp_log')
                ->from($table, 'l')
                ->leftJoin('l', $ruleTable, 'r', 'r.id_shop = l.id_shop AND r.directive = l.directive AND r.source = l.source')
                ->where('l.id_shop = :shopId')
                ->andWhere('r.id_csp_rule IS NULL')
                ->orderBy('l.hits', 'ASC')
                ->addOrderBy('l.date_upd', 'ASC')
                ->addOrderBy('l.id_csp_log', 'ASC')
                ->setMaxResults($limit)
                ->setParameter('shopId', $shopId)
                ->executeQuery()
                ->fetchFirstColumn();

            return $this->deleteByIds($connection, $table, $ids);
        });
    }

    /** Clears a shop's violation log but keeps rows backed by an allow-list rule, so curated rules stay in the grid. Returns the count removed. */
    public function deleteByShop(int $shopId): int
    {
        return $this->deletePrunableRowsInChunks($shopId);
    }

    /** Deletes a shop's reports older than $before, keeping rows backed by an allow-list rule. Returns the count removed. */
    public function deleteOlderThanByShop(int $shopId, DateTimeInterface $before): int
    {
        return $this->deletePrunableRowsInChunks($shopId, static function (QueryBuilder $qb) use ($before): void {
            $qb->andWhere('l.date_add < :before')
                ->setParameter('before', $before->format('Y-m-d H:i:s'));
        });
    }

    /**
     * Deletes every prunable (not allow-list-backed) row for a shop in bounded chunks, so a large log
     * never builds one oversized IN() statement or holds a single long delete. Each chunk retries on
     * a transient deadlock.
     *
     * @param (callable(QueryBuilder): void)|null $addConditions extra WHERE on the id selection (alias l)
     */
    private function deletePrunableRowsInChunks(int $shopId, ?callable $addConditions = null): int
    {
        $connection = $this->getEntityManager()->getConnection();
        $table = $this->getClassMetadata()->getTableName();
        $ruleTable = $this->getEntityManager()->getClassMetadata(CspRule::class)->getTableName();

        $total = 0;
        do {
            $deleted = $this->retryOnDeadlock(function () use ($connection, $table, $ruleTable, $shopId, $addConditions): int {
                $idQuery = $connection->createQueryBuilder()
                    ->select('l.id_csp_log')
                    ->from($table, 'l')
                    ->leftJoin('l', $ruleTable, 'r', 'r.id_shop = l.id_shop AND r.directive = l.directive AND r.source = l.source')
                    ->where('l.id_shop = :shopId')
                    ->andWhere('r.id_csp_rule IS NULL')
                    ->orderBy('l.id_csp_log', 'ASC')
                    ->setMaxResults(self::DELETE_CHUNK_SIZE)
                    ->setParameter('shopId', $shopId);

                if (null !== $addConditions) {
                    $addConditions($idQuery);
                }

                return $this->deleteByIds($connection, $table, $idQuery->executeQuery()->fetchFirstColumn());
            });

            $total += $deleted;
        } while ($deleted === self::DELETE_CHUNK_SIZE);

        return $total;
    }

    /**
     * @param list<mixed> $ids
     */
    private function deleteByIds(Connection $connection, string $table, array $ids): int
    {
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
     * Retries a delete a few times on a transient InnoDB deadlock / lock-wait timeout, which concurrent
     * report floods pruning the same shop's log can trigger; rethrows once the attempts are exhausted.
     *
     * @param callable(): int $operation
     */
    private function retryOnDeadlock(callable $operation): int
    {
        for ($attempt = 1;; ++$attempt) {
            try {
                return $operation();
            } catch (RetryableException $e) {
                if ($attempt >= self::DELETE_MAX_ATTEMPTS) {
                    throw $e;
                }

                usleep(self::DELETE_RETRY_BASE_DELAY_US * $attempt);
            }
        }
    }

    /**
     * Shop ids that currently have at least one log row, so a cleanup command only visits shops that need it.
     *
     * @return list<int>
     */
    public function distinctShopIds(): array
    {
        return array_map('intval', $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('DISTINCT id_shop')
            ->from($this->getClassMetadata()->getTableName())
            ->executeQuery()
            ->fetchFirstColumn());
    }
}
