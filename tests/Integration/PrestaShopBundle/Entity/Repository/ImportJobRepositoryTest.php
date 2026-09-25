<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\ImportJobStatus;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The methods that are SQL rather than ORM, checked at the level they are written at.
 *
 * They are here because no Behat scenario can cover them: a Continue and the Cancel it races share
 * a single EntityManager there, so both are handed the same entity instance and the staleness the
 * probe and the compare-and-set exist to defeat never happens. Only a test that changes the row
 * behind Doctrine's back can tell whether they read the database or repeat what the request
 * already loaded — and if they stopped doing so, a cancellation would be silently ignored by the
 * batch it was meant to stop.
 *
 * The purge is covered by Behat too, but only through the retention window the purger applies;
 * here the boundary is exercised directly.
 */
class ImportJobRepositoryTest extends KernelTestCase
{
    private ImportJobRepository $repository;

    private EntityManagerInterface $entityManager;

    /**
     * @var list<string> uuids this test created, removed whatever the test did with them
     */
    private array $createdUuids = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->repository = self::getContainer()->get(ImportJobRepository::class);
        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        foreach ($this->createdUuids as $importJobUuid) {
            $this->removeImportJob($importJobUuid);
        }
        $this->createdUuids = [];

        parent::tearDown();
    }

    public function testEveryJobUntouchedSinceTheBoundaryIsCollectedWhateverItsStatus(): void
    {
        $staleFinished = $this->createImportJob(ImportJobStatus::FINISHED, 10);
        $staleRunning = $this->createImportJob(ImportJobStatus::RUNNING, 10);
        $stalePaused = $this->createImportJob(ImportJobStatus::AWAITING_CONFIRMATION, 10);
        $recentFinished = $this->createImportJob(ImportJobStatus::FINISHED, 1);
        $recentRunning = $this->createImportJob(ImportJobStatus::RUNNING, 1);

        $purged = $this->repository->purgeUntouchedSince(new DateTimeImmutable('-7 days'));

        $this->assertSame(3, $purged);
        $this->assertNull($this->repository->readStatus($staleFinished));
        $this->assertNull($this->repository->readStatus($staleRunning), 'A job nobody continued for a week was abandoned, not paused');
        $this->assertNull($this->repository->readStatus($stalePaused));
        $this->assertNotNull($this->repository->readStatus($recentFinished), 'Inside the window, however terminal');
        $this->assertNotNull($this->repository->readStatus($recentRunning));
    }

    public function testAPurgeThatMatchesNothingDeletesNothing(): void
    {
        $this->createImportJob(ImportJobStatus::FINISHED, 1);

        $this->assertSame(0, $this->repository->purgeUntouchedSince(new DateTimeImmutable('-7 days')));
    }

    public function testTheStatusProbeSeesAWriteTheLoadedEntityKnowsNothingAbout(): void
    {
        $importJobUuid = $this->createImportJob();

        // the entity is now in the identity map, holding PENDING
        $loaded = $this->repository->findByUuid($importJobUuid);
        $this->assertSame(ImportJobStatus::PENDING, $loaded->getStatus());

        // another request cancels the job: only the status column changes
        $this->writeStatusBehindDoctrine($importJobUuid, ImportJobStatus::CANCELLED);

        $this->assertSame(
            ImportJobStatus::PENDING,
            $loaded->getStatus(),
            'The loaded entity cannot know: this is precisely why the probe exists'
        );
        $this->assertSame(
            ImportJobStatus::CANCELLED,
            $this->repository->readStatus($importJobUuid),
            'readStatus() must read the database, not the identity map'
        );
    }

    public function testTheStatusProbeReportsNothingForAJobThatIsGone(): void
    {
        $importJobUuid = $this->createImportJob();
        $this->removeImportJob($importJobUuid);

        $this->assertNull(
            $this->repository->readStatus($importJobUuid),
            'A vanished row must be distinguishable from a job that simply is not terminal'
        );
    }

    /**
     * The value object canonicalises to lowercase; the column's binary collation is the second
     * line, for a raw string that bypassed it. Two spellings must never name one row when the lock
     * key and the working file would not agree.
     */
    public function testTheSameUuidSpelledDifferentlyNamesNoRow(): void
    {
        $importJobUuid = $this->createImportJob();
        $otherSpelling = strtoupper($importJobUuid);
        $this->assertNotSame($importJobUuid, $otherSpelling);

        $this->assertNull($this->repository->findByUuid($otherSpelling));
        $this->assertNull($this->repository->readStatus($otherSpelling));
    }

    public function testATransitionFromAnAllowedStatusIsWon(): void
    {
        $importJobUuid = $this->createImportJob();

        $this->assertSame(
            ImportJobStatus::RUNNING,
            $this->repository->transitionStatus($importJobUuid, ImportJobStatus::RUNNING, ImportJobStatus::nonTerminalCases())
        );
        $this->assertSame(ImportJobStatus::RUNNING, $this->repository->readStatus($importJobUuid));
    }

    /**
     * The compare half: a status another request wrote first is left alone and handed back, so
     * the loser knows what to adopt.
     */
    public function testATransitionLostToAStatusWrittenFirstChangesNothing(): void
    {
        $importJobUuid = $this->createImportJob();
        $this->writeStatusBehindDoctrine($importJobUuid, ImportJobStatus::CANCELLED);

        $this->assertSame(
            ImportJobStatus::CANCELLED,
            $this->repository->transitionStatus($importJobUuid, ImportJobStatus::RUNNING, ImportJobStatus::nonTerminalCases())
        );
        $this->assertSame(ImportJobStatus::CANCELLED, $this->repository->readStatus($importJobUuid));
    }

    public function testATransitionOnAVanishedRowReportsNothing(): void
    {
        $importJobUuid = $this->createImportJob();
        $this->removeImportJob($importJobUuid);

        $this->assertNull(
            $this->repository->transitionStatus($importJobUuid, ImportJobStatus::RUNNING, ImportJobStatus::nonTerminalCases())
        );
    }

    /**
     * What makes the whole arbitration hold without transactions: whatever the in-memory copy
     * believes, a progress write leaves the status column alone.
     */
    public function testAProgressWriteCannotTouchTheStatusColumn(): void
    {
        $importJobUuid = $this->createImportJob();
        $loaded = $this->repository->findByUuid($importJobUuid);

        // this request believes it moved the job to RUNNING; another request cancelled it meanwhile
        $loaded->setStatus(ImportJobStatus::RUNNING);
        $this->writeStatusBehindDoctrine($importJobUuid, ImportJobStatus::CANCELLED);

        $loaded->setCurrentPhaseId('validation')->setCurrentOffset(20)->setResumeCursor('byte-420');
        $this->repository->save($loaded);

        $this->assertSame(ImportJobStatus::CANCELLED, $this->repository->readStatus($importJobUuid), 'The progress UPDATE carried no status');
        $this->entityManager->clear();
        $this->assertSame(20, $this->repository->findByUuid($importJobUuid)->getCurrentOffset(), 'The progress itself went in');
    }

    private function createImportJob(?ImportJobStatus $status = null, ?int $ageInDays = null): string
    {
        $importJob = new ImportJob(
            ImportJobUuid::generate()->getValue(),
            'product',
            1,
            'probe.csv',
            1,
            3,
            ['langIso' => 'en', 'multipleValueSeparator' => ',', 'fieldMapping' => [0 => 'name']],
            []
        );
        $this->repository->save($importJob);
        $this->createdUuids[] = $importJob->getUuid();

        if (null !== $status) {
            $this->writeStatusBehindDoctrine($importJob->getUuid(), $status);
        }
        if (null !== $ageInDays) {
            $this->ageImportJob($importJob->getUuid(), $ageInDays);
        }

        return $importJob->getUuid();
    }

    /**
     * date_upd is maintained by a lifecycle callback, so an old row can only be made through SQL.
     */
    private function ageImportJob(string $importJobUuid, int $ageInDays): void
    {
        $this->entityManager->getConnection()->executeStatement(
            sprintf(
                'UPDATE %s SET date_upd = :dateUpd WHERE import_job_uuid = :uuid',
                $this->entityManager->getClassMetadata(ImportJob::class)->getTableName()
            ),
            [
                'dateUpd' => (new DateTimeImmutable(sprintf('-%d days', $ageInDays)))->format('Y-m-d H:i:s'),
                'uuid' => $importJobUuid,
            ]
        );
    }

    private function writeStatusBehindDoctrine(string $importJobUuid, ImportJobStatus $status): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->createQueryBuilder()
            ->update($this->entityManager->getClassMetadata(ImportJob::class)->getTableName())
            ->set('status', ':status')
            ->where('import_job_uuid = :uuid')
            ->setParameter('status', $status->value)
            ->setParameter('uuid', $importJobUuid)
            ->executeStatement();
    }

    private function removeImportJob(string $importJobUuid): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->createQueryBuilder()
            ->delete($this->entityManager->getClassMetadata(ImportJob::class)->getTableName())
            ->where('import_job_uuid = :uuid')
            ->setParameter('uuid', $importJobUuid)
            ->executeStatement();
        $this->entityManager->clear();
    }
}
