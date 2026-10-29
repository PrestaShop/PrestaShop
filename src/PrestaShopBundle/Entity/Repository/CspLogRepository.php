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
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
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
     * Records one violation for (context, shop, directive, source): inserts on first sight, else bumps hits/date_upd.
     * Uses INSERT ... ON DUPLICATE KEY UPDATE since a SELECT-then-write races under a flood (MySQL/MariaDB-specific).
     *
     * @return bool true on insert, false when an existing row was bumped, so a batch caller can skip the row-cap COUNT
     */
    public function upsert(CspContext $context, int $shopId, string $directive, string $source, string $documentUri, ?string $sample = null, ?string $sourceFile = null, ?int $lineNumber = null): bool
    {
        $table = $this->getClassMetadata()->getTableName();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        // sample/source_file/line_number are informational (not in the unique key); refresh them to the
        // latest report that carries one, but keep the last known value when a later report arrives
        // without it (COALESCE) so an example captured once is not wiped by a plain re-report. VALUES()
        // is the established core upsert idiom; the row-alias form is MySQL 8.0.19+ only and breaks the
        // MariaDB / older MySQL that PrestaShop still supports.
        $sql = 'INSERT INTO ' . $table . ' (id_shop, context, directive, source, document_uri, sample, source_file, line_number, hits, date_add, date_upd)'
            . ' VALUES (:shopId, :context, :directive, :source, :documentUri, :sample, :sourceFile, :lineNumber, 1, :dateAdd, :dateUpd)'
            . ' ON DUPLICATE KEY UPDATE hits = hits + 1, date_upd = :dateUpdOnDuplicate,'
            . ' sample = COALESCE(VALUES(sample), sample),'
            . ' source_file = COALESCE(VALUES(source_file), source_file),'
            . ' line_number = COALESCE(VALUES(line_number), line_number)';

        $affectedRows = $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'shopId' => $shopId,
            'context' => $context->value,
            'directive' => $directive,
            'source' => $source,
            'documentUri' => $documentUri,
            'sample' => $sample,
            'sourceFile' => $sourceFile,
            'lineNumber' => $lineNumber,
            'dateAdd' => $now,
            'dateUpd' => $now,
            'dateUpdOnDuplicate' => $now,
        ]);

        // MySQL returns 1 affected row for a fresh INSERT, 2 when ON DUPLICATE KEY UPDATE fires.
        return 1 === $affectedRows;
    }

    /**
     * Bumps hits/date_upd (and refreshes the sample when the report carries one) for a source already in
     * the log, without inserting anything. Returns true when a row was updated, false when the source is
     * not yet recorded. Used at the row cap: a known source keeps counting, a brand-new one is refused.
     */
    public function bumpIfExists(CspContext $context, int $shopId, string $directive, string $source, string $documentUri, ?string $sample, ?string $sourceFile, ?int $lineNumber): bool
    {
        $table = $this->getClassMetadata()->getTableName();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $sql = 'UPDATE ' . $table . ' SET hits = hits + 1, date_upd = :dateUpd,'
            . ' sample = COALESCE(:sample, sample),'
            . ' source_file = COALESCE(:sourceFile, source_file),'
            . ' line_number = COALESCE(:lineNumber, line_number)'
            . ' WHERE id_shop = :shopId AND context = :context AND directive = :directive AND source = :source AND document_uri = :documentUri';

        return $this->getEntityManager()->getConnection()->executeStatement($sql, [
            'dateUpd' => $now,
            'sample' => $sample,
            'sourceFile' => $sourceFile,
            'lineNumber' => $lineNumber,
            'shopId' => $shopId,
            'context' => $context->value,
            'directive' => $directive,
            'source' => $source,
            'documentUri' => $documentUri,
        ]) > 0;
    }

    /**
     * Whether the scope already holds at least $cap rows, using a bounded LIMIT/OFFSET probe (it stops at
     * the cap) rather than a full COUNT. Non-positive $cap means "no cap", so it always returns false.
     */
    public function hasAtLeast(CspContext $context, int $shopId, int $cap): bool
    {
        if ($cap <= 0) {
            return false;
        }

        return false !== $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->setFirstResult($cap - 1)
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /** Whether this source is already recorded for the scope (on any page), via the unique index prefix. */
    public function sourceExists(CspContext $context, int $shopId, string $directive, string $source): bool
    {
        return false !== $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->andWhere('directive = :directive')
            ->andWhere('source = :source')
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->setParameter('directive', $directive)
            ->setParameter('source', $source)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Whether this source already has at least $maxPages distinct example pages recorded (the folded
     * "other pages" row, document_uri = '', is not an example page). Bounded LIMIT/OFFSET probe.
     */
    public function hasAtLeastPages(CspContext $context, int $shopId, string $directive, string $source, int $maxPages): bool
    {
        if ($maxPages <= 0) {
            return true;
        }

        return false !== $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->andWhere('directive = :directive')
            ->andWhere('source = :source')
            ->andWhere("document_uri <> ''")
            ->setFirstResult($maxPages - 1)
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->setParameter('directive', $directive)
            ->setParameter('source', $source)
            ->executeQuery()
            ->fetchOne();
    }

    /** Deletes every log row for one source (all its pages); used when the source is allow-listed. Returns the count removed. */
    public function deleteByShopDirectiveSource(CspContext $context, int $shopId, string $directive, string $source): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->delete($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->andWhere('directive = :directive')
            ->andWhere('source = :source')
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->setParameter('directive', $directive)
            ->setParameter('source', $source)
            ->executeStatement();
    }

    public function countByShop(CspContext $context, int $shopId): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(1)')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Counts a scope's reported sources that are not yet on the allow-list (no matching rule). Drives the
     * pre-enforcement nudge: these are exactly what would be blocked if the surface switched to enforcing.
     */
    public function countUnreviewedByShop(CspContext $context, int $shopId): int
    {
        $ruleTable = $this->getEntityManager()->getClassMetadata(CspRule::class)->getTableName();

        // The log holds one row per source per page; the nudge counts distinct sources, not pages.
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(DISTINCT l.directive, l.source)')
            ->from($this->getClassMetadata()->getTableName(), 'l')
            ->leftJoin('l', $ruleTable, 'r', 'r.id_shop = l.id_shop AND r.context = l.context AND r.directive = l.directive AND r.source = l.source')
            ->where('l.id_shop = :shopId')
            ->andWhere('l.context = :context')
            ->andWhere('r.id_csp_rule IS NULL')
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Like countUnreviewedByShop() but summed over several shops in one query, for the all-shops page view
     * (each shop curates independently, so a source reported on two shops counts twice). Avoids a count per
     * shop.
     *
     * @param list<int> $shopIds
     */
    public function countUnreviewedByShops(CspContext $context, array $shopIds): int
    {
        if ($shopIds === []) {
            return 0;
        }

        $ruleTable = $this->getEntityManager()->getClassMetadata(CspRule::class)->getTableName();

        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(DISTINCT l.id_shop, l.directive, l.source)')
            ->from($this->getClassMetadata()->getTableName(), 'l')
            ->leftJoin('l', $ruleTable, 'r', 'r.id_shop = l.id_shop AND r.context = l.context AND r.directive = l.directive AND r.source = l.source')
            ->where('l.id_shop IN (:shopIds)')
            ->andWhere('l.context = :context')
            ->andWhere('r.id_csp_rule IS NULL')
            ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Whether any of the given shops has a log at or over the row cap, in one grouped query. Drives the
     * "log full" page notice (at the cap, new sources are no longer recorded).
     *
     * @param list<int> $shopIds
     */
    public function anyShopAtCap(CspContext $context, array $shopIds, int $cap): bool
    {
        if ($cap <= 0 || $shopIds === []) {
            return false;
        }

        return false !== $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop IN (:shopIds)')
            ->andWhere('context = :context')
            ->groupBy('id_shop')
            ->having('COUNT(*) >= :cap')
            ->setMaxResults(1)
            ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
            ->setParameter('context', $context->value)
            ->setParameter('cap', $cap)
            ->executeQuery()
            ->fetchOne();
    }

    /** Whether the scope has any log row; cheaper than countByShop() when only existence matters (the baseline check). */
    public function existsByShop(CspContext $context, int $shopId): bool
    {
        return (bool) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /** Prunes a scope's lowest-hit, oldest rows to keep the table bounded. Returns the count removed. */
    public function deleteLeastReportedByShop(CspContext $context, int $shopId, int $limit): int
    {
        if ($limit <= 0) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $table = $this->getClassMetadata()->getTableName();

        // Re-select the victims inside the retry so a deadlock rollback never reuses stale ids.
        return $this->retryOnDeadlock(function () use ($connection, $table, $context, $shopId, $limit): int {
            $ids = $connection->createQueryBuilder()
                ->select('id_csp_log')
                ->from($table)
                ->where('id_shop = :shopId')
                ->andWhere('context = :context')
                ->orderBy('hits', 'ASC')
                ->addOrderBy('date_upd', 'ASC')
                ->addOrderBy('id_csp_log', 'ASC')
                ->setMaxResults($limit)
                ->setParameter('shopId', $shopId)
                ->setParameter('context', $context->value)
                ->executeQuery()
                ->fetchFirstColumn();

            return $this->deleteByIds($connection, $table, $ids);
        });
    }

    /** Clears a scope's whole violation log. The allow-list (csp_rule) is a separate table, so it is unaffected. Returns the count removed. */
    public function deleteByShop(CspContext $context, int $shopId): int
    {
        return $this->deleteRowsInChunks($context, $shopId);
    }

    /** Deletes a scope's reports older than $before. Returns the count removed. */
    public function deleteOlderThanByShop(CspContext $context, int $shopId, DateTimeInterface $before): int
    {
        return $this->deleteRowsInChunks($context, $shopId, static function (QueryBuilder $qb) use ($before): void {
            $qb->andWhere('date_add < :before')
                ->setParameter('before', $before->format('Y-m-d H:i:s'));
        });
    }

    /**
     * Shop ids that currently have at least one log row in the scope, so a cleanup command only visits
     * shops that need it. The global (admin) context always uses shop id 0.
     *
     * @return list<int>
     */
    public function distinctShopIds(CspContext $context): array
    {
        return array_map('intval', $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('DISTINCT id_shop')
            ->from($this->getClassMetadata()->getTableName())
            ->where('context = :context')
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchFirstColumn());
    }

    /**
     * Deletes a scope's rows in bounded chunks, so a large log never builds one oversized IN() statement
     * or holds a single long delete. Each chunk retries on a transient deadlock.
     *
     * @param (callable(QueryBuilder): void)|null $addConditions extra WHERE on the id selection
     */
    private function deleteRowsInChunks(CspContext $context, int $shopId, ?callable $addConditions = null): int
    {
        $connection = $this->getEntityManager()->getConnection();
        $table = $this->getClassMetadata()->getTableName();

        $total = 0;
        do {
            $deleted = $this->retryOnDeadlock(function () use ($connection, $table, $context, $shopId, $addConditions): int {
                $idQuery = $connection->createQueryBuilder()
                    ->select('id_csp_log')
                    ->from($table)
                    ->where('id_shop = :shopId')
                    ->andWhere('context = :context')
                    ->orderBy('id_csp_log', 'ASC')
                    ->setMaxResults(self::DELETE_CHUNK_SIZE)
                    ->setParameter('shopId', $shopId)
                    ->setParameter('context', $context->value);

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
}
