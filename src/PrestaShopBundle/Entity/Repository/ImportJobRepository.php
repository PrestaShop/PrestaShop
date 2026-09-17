<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use DateTimeInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\ImportJobStatus;

/**
 * The only class that loads and persists {@see ImportJob}; handlers never touch the EntityManager.
 *
 * No transactions and no row locks — deferred until the generic tooling lands. readStatus() is
 * what replaces them where it matters.
 */
class ImportJobRepository extends EntityRepository
{
    public function add(ImportJob $importJob): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($importJob);
        $entityManager->flush();
    }

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
     * Deletes terminal jobs last touched before $limit.
     *
     * Rows go first and the caller then sweeps working files that match no row, so a file left by
     * a job that died before its row was ever written is collected too — which returning the
     * deleted uuids would have missed.
     */
    public function purgeTerminalOlderThan(DateTimeInterface $limit): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->delete($this->getClassMetadata()->getTableName())
            ->where('status IN (:statuses)')
            ->andWhere('date_upd < :limit')
            ->setParameter('statuses', ImportJobStatus::terminalValues(), ArrayParameterType::STRING)
            ->setParameter('limit', $limit, Types::DATETIME_MUTABLE)
            ->executeStatement();
    }
}
