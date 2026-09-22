<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\ImportJobStatus;

/**
 * The only class that loads and persists {@see ImportJob}; handlers never touch the EntityManager.
 *
 * No transactions and no row locks (deferred to the generic tooling, #42385). Two scalar queries
 * stand in for them: readStatus() sees what another request wrote, transitionStatus() moves the
 * status in one statement so two requests cannot both believe they won.
 */
class ImportJobRepository extends EntityRepository
{
    public function save(ImportJob $importJob): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($importJob);
        $entityManager->flush();
    }

    public function findByUuid(string $importJobUuid): ?ImportJob
    {
        return $this->find($importJobUuid);
    }

    /**
     * The status as the database has it now, bypassing hydration and the identity map — how a
     * running batch learns another request cancelled it.
     *
     * Null means the row is gone: a reason to stop, not "not cancelled".
     */
    public function readStatus(string $importJobUuid): ?ImportJobStatus
    {
        $status = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('status')
            ->from($this->getClassMetadata()->getTableName())
            ->where('import_job_uuid = :uuid')
            ->setParameter('uuid', $importJobUuid)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();

        return false === $status || null === $status ? null : ImportJobStatus::tryFrom((string) $status);
    }

    /**
     * Compare-and-set: the WHERE is the compare, the SET the swap, and the row lock of a single
     * UPDATE makes the pair atomic without a transaction. A request that lost the race writes
     * nothing.
     *
     * Returns the status the row holds afterwards — $to when this call won, what the other request
     * wrote when it did not, null when the row is gone. Read back rather than inferred from the
     * affected-row count: PDO reports CHANGED rows, and date_upd has second precision.
     *
     * @param list<ImportJobStatus> $expectedStatuses the statuses the row must currently hold for
     *                                                the write to happen
     */
    public function transitionStatus(string $importJobUuid, ImportJobStatus $to, array $expectedStatuses): ?ImportJobStatus
    {
        $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->update($this->getClassMetadata()->getTableName())
            ->set('status', ':to')
            ->set('date_upd', ':now')
            ->where('import_job_uuid = :uuid')
            ->andWhere('status IN (:expected)')
            ->setParameter('to', $to->value)
            ->setParameter('now', new DateTime(), Types::DATETIME_MUTABLE)
            ->setParameter('uuid', $importJobUuid)
            ->setParameter(
                'expected',
                array_map(static fn (ImportJobStatus $status): string => $status->value, $expectedStatuses),
                ArrayParameterType::STRING
            )
            ->executeStatement();

        return $this->readStatus($importJobUuid);
    }

    /**
     * Deletes every job untouched since $limit, whatever its status: date_upd moves on every slice
     * and every transition, so a job that stopped moving was abandoned — closed tab, dead request —
     * and is not merely idle.
     *
     * Rows go first and the caller then sweeps working files that match no row, so a file left by
     * a job that died before its row was ever written is collected too — which returning the
     * deleted uuids would have missed.
     */
    public function purgeUntouchedSince(DateTimeInterface $limit): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->delete($this->getClassMetadata()->getTableName())
            ->where('date_upd < :limit')
            ->setParameter('limit', $limit, Types::DATETIME_MUTABLE)
            ->executeStatement();
    }
}
