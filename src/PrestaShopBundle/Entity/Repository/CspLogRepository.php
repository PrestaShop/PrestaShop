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
