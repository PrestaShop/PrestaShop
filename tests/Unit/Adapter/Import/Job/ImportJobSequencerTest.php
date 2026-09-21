<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Import\Job;

use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Import\ImportTruncator;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobContextFactory;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobSequencer;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\ImportJobStatus;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The batch loop, against a scripted importer: what ends a phase, what pauses, what fails, and
 * what a cancellation landing mid-batch does.
 */
class ImportJobSequencerTest extends TestCase
{
    private const UUID = '0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f';

    private ?ImportEntityDeleterInterface $entityDeleter = null;

    public function testItWalksEveryPhaseAndFinishes(): void
    {
        $job = $this->buildJob();
        $seen = [];

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 10, ImportPhaseDefinition::PHASE_DATABASE => 10],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$seen): PhaseBatchResult {
                $seen[] = $phaseId;

                return new PhaseBatchResult($limit, [], [], 'cursor');
            }
        ));

        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
        $this->assertSame([ImportPhaseDefinition::PHASE_VALIDATION, ImportPhaseDefinition::PHASE_DATABASE], $seen, 'A 10-unit phase fits in one slice, so one call each');
        $this->assertSame([ImportPhaseDefinition::PHASE_VALIDATION => 10, ImportPhaseDefinition::PHASE_DATABASE => 10], $job->getPhaseTotals());
        $this->assertNull($job->getResumeCursor(), 'A terminal job keeps nothing to resume from');
        $this->assertSame([], $job->getSkippedRows());
    }

    public function testAPausingPhaseEndingWithAWarningStopsForConfirmation(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION, true), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult(
                $limit,
                ImportPhaseDefinition::PHASE_VALIDATION === $phaseId ? [new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, 'Price rounded', [1])] : [],
                [],
                'cursor'
            )
        ));

        $this->assertSame(ImportJobStatus::AWAITING_CONFIRMATION, $job->getStatus());
        $this->assertSame(ImportPhaseDefinition::PHASE_VALIDATION, $job->getCurrentPhaseId(), 'The job stops ON the phase it wants reviewed');
    }

    public function testAPausingPhaseEndingWithOnlyNoticesDoesNotStop(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION, true), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult(
                $limit,
                [new ImportMessage(ImportMessage::SEVERITY_NOTICE, $phaseId, 'Category created', [1])],
                [],
                'cursor'
            )
        ));

        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus(), 'Notices are information, not something to confirm');
    }

    /**
     * Continuing IS the confirmation. Re-evaluating the phase the job stopped on would find the
     * same messages and pause again, forever.
     */
    public function testContinuingAPausedJobMovesPastThePhaseInsteadOfPausingAgain(): void
    {
        $job = $this->buildJob(status: ImportJobStatus::AWAITING_CONFIRMATION, currentPhaseId: ImportPhaseDefinition::PHASE_VALIDATION);
        $job->setPhaseTotals([ImportPhaseDefinition::PHASE_VALIDATION => 5])->setCurrentOffset(5)
            ->setMessages(['items' => [[
                'severity' => 'warning', 'phase' => ImportPhaseDefinition::PHASE_VALIDATION, 'message' => 'Price rounded',
                'field' => null, 'rows' => [1], 'rowCount' => 1,
            ]], 'droppedMessages' => []]);

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION, true), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], 'cursor')
        ));

        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
    }

    public function testOneBudgetIsSpentAcrossAPhaseBoundary(): void
    {
        $job = $this->buildJob();
        $seen = [];

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$seen): PhaseBatchResult {
                $seen[] = $phaseId;

                return new PhaseBatchResult($limit, [], [], 'cursor');
            }
        ), batchLimit: 10);

        $this->assertSame([ImportPhaseDefinition::PHASE_VALIDATION, ImportPhaseDefinition::PHASE_DATABASE], $seen, 'A budget of 10 finishes a 5-unit phase and starts the next');
        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
    }

    public function testABudgetRunningOutMidPhaseLeavesTheJobResumable(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 100],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], 'byte-420')
        ), batchLimit: 40);

        $this->assertSame(ImportJobStatus::RUNNING, $job->getStatus());
        $this->assertSame(40, $job->getCurrentOffset());
        $this->assertSame('byte-420', $job->getResumeCursor(), 'The next request resumes from here');
    }

    public function testAPhaseCountingNoUnitIsSkipped(): void
    {
        $job = $this->buildJob();
        $seen = [];

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_ASSOCIATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 2, ImportPhaseDefinition::PHASE_ASSOCIATION => 0, ImportPhaseDefinition::PHASE_DATABASE => 2],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$seen): PhaseBatchResult {
                $seen[] = $phaseId;

                return new PhaseBatchResult($limit, [], [], null);
            }
        ));

        $this->assertSame([ImportPhaseDefinition::PHASE_VALIDATION, ImportPhaseDefinition::PHASE_DATABASE], $seen);
        $this->assertSame(0, $job->getPhaseTotals()[ImportPhaseDefinition::PHASE_ASSOCIATION], 'The count is still recorded, so the report can show the phase as empty');
    }

    /**
     * A validate-only run must end, not wait for a confirmation its API client will never send.
     */
    public function testADryRunStopsAfterValidationEvenWhenItWarns(): void
    {
        $job = $this->buildJob(options: ['dryRun' => true]);
        $seen = [];

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION, true), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 3, ImportPhaseDefinition::PHASE_DATABASE => 3],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$seen): PhaseBatchResult {
                $seen[] = $phaseId;

                return new PhaseBatchResult($limit, [new ImportMessage(ImportMessage::SEVERITY_WARNING, $phaseId, 'Odd', [0])], [], null);
            }
        ));

        $this->assertSame([ImportPhaseDefinition::PHASE_VALIDATION], $seen, 'Nothing may be written in a dry run');
        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
    }

    public function testAnImporterThrowingFailsTheJobAndKeepsAMessageTheMerchantCanRead(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn () => throw new ImportEngineException('The working file disappeared')
        ));

        $this->assertSame(ImportJobStatus::FAILED, $job->getStatus());
        $this->assertSame('The working file disappeared', $job->getMessages()['items'][0]['message']);
    }

    public function testAnUnexpectedThrowableIsNotQuotedBackToTheMerchant(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn () => throw new RuntimeException('SQLSTATE[42S02]: table ps_secret does not exist')
        ));

        $this->assertSame(ImportJobStatus::FAILED, $job->getStatus());
        $this->assertStringNotContainsString('ps_secret', $job->getMessages()['items'][0]['message']);
    }

    public function testAFileRejectingTooManyRowsIsTreatedAsMalformed(): void
    {
        $job = $this->buildJob(recordCount: 100000);
        $row = 0;

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 100000],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$row): PhaseBatchResult {
                $skipped = range($row, $row + $limit - 1);
                $row += $limit;

                return new PhaseBatchResult($limit, [], $skipped, null);
            }
        ), batchLimit: 100000);

        $this->assertSame(ImportJobStatus::FAILED, $job->getStatus());
        $this->assertGreaterThan(ImportJobSequencer::MAX_INVALID_ROWS, $job->getSkippedRowCount());
    }

    /**
     * The job was persisted mid-association, then a deploy shipped an importer that no longer
     * declares that phase — so the stored phase id matches nothing in getPhases().
     */
    public function testAJobStoppedInAPhaseTheImporterNoLongerDeclaresFails(): void
    {
        $job = $this->buildJob(status: ImportJobStatus::RUNNING, currentPhaseId: ImportPhaseDefinition::PHASE_ASSOCIATION);
        $job->setPhaseTotals([ImportPhaseDefinition::PHASE_ASSOCIATION => 5]);

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], null)
        ));

        $this->assertSame(ImportJobStatus::FAILED, $job->getStatus());
        $this->assertStringContainsString(
            ImportPhaseDefinition::PHASE_ASSOCIATION,
            $job->getMessages()['items'][0]['message'],
            'The report has to name the phase that vanished'
        );
    }

    /**
     * The database is the arbiter. The rows this slice wrote before the cancellation was observed
     * are real, so they are recorded under the status the other request chose — writing progress
     * cannot clobber it, because a progress write never touches the status column.
     */
    public function testACancellationObservedBetweenSlicesRecordsTheWorkAlreadyDone(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 100],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], 'cursor')
        ), repository: $this->repository(ImportJobStatus::CANCELLED));

        $this->assertSame(ImportJobStatus::CANCELLED, $job->getStatus(), 'The reported status is the one the database holds');
        $this->assertSame(
            ImportJobSequencer::MAX_UNITS_PER_STEP,
            $job->getCurrentOffset(),
            'The merchant has to be able to tell which rows made it in before the cancellation'
        );
        $this->assertNull($job->getResumeCursor(), 'Nothing will resume a cancelled job');
    }

    /**
     * The row was deleted under the job — by the purge, or by hand. Saving would resurrect it.
     */
    public function testAJobWhoseRowVanishedMidBatchIsNotWrittenBack(): void
    {
        $job = $this->buildJob();
        $repository = $this->createMock(ImportJobRepository::class);
        $repository->method('readStatus')->willReturn(null);
        $repository->expects($this->never())->method('save');

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 100],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], 'cursor')
        ), repository: $repository);

        $this->assertSame(ImportJobStatus::RUNNING, $job->getStatus());
    }

    public function testTheImporterIsNeverHandedMoreThanOneSlice(): void
    {
        $job = $this->buildJob();
        $limits = [];

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 100],
            function (string $phaseId, ImportJobContext $context, int $limit) use (&$limits): PhaseBatchResult {
                $limits[] = $limit;

                return new PhaseBatchResult($limit, [], [], null);
            }
        ), batchLimit: 100);

        $this->assertSame(array_fill(0, 5, ImportJobSequencer::MAX_UNITS_PER_STEP), $limits);
        $this->assertSame(100, $job->getCurrentOffset(), 'Slicing bounds the importer call, never the batch the caller asked for');
    }

    public function testAnImporterMakingNoProgressFailsInsteadOfSpinning(): void
    {
        $job = $this->buildJob();

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_DATABASE => 10],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult(0, [], [], null)
        ));

        $this->assertSame(ImportJobStatus::FAILED, $job->getStatus());
    }

    public function testTruncateFiresOnceWhenTheDatabasePhaseIsEntered(): void
    {
        $job = $this->buildJob(options: ['truncate' => true]);
        $this->entityDeleter = $this->createMock(ImportEntityDeleterInterface::class);
        $this->entityDeleter->expects($this->once())->method('deleteAll');

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 40, ImportPhaseDefinition::PHASE_DATABASE => 40],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], null)
        ), batchLimit: 200);

        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
    }

    public function testTruncateNeverFiresInADryRun(): void
    {
        $job = $this->buildJob(options: ['truncate' => true, 'dryRun' => true]);
        $this->entityDeleter = $this->createMock(ImportEntityDeleterInterface::class);
        $this->entityDeleter->expects($this->never())->method('deleteAll');

        $this->sequence($job, $this->importer(
            [$this->phase(ImportPhaseDefinition::PHASE_VALIDATION), $this->phase(ImportPhaseDefinition::PHASE_DATABASE)],
            [ImportPhaseDefinition::PHASE_VALIDATION => 5, ImportPhaseDefinition::PHASE_DATABASE => 5],
            fn (string $phaseId, ImportJobContext $context, int $limit) => new PhaseBatchResult($limit, [], [], null)
        ));

        $this->assertSame(ImportJobStatus::FINISHED, $job->getStatus());
    }

    private function sequence(
        ImportJob $importJob,
        EntityImporterInterface $importer,
        ?int $batchLimit = null,
        ?ImportJobRepository $repository = null,
    ): void {
        $importDirectory = new ImportDirectory($this->configuration());
        $deleter = $this->entityDeleter ?? $this->createMock(ImportEntityDeleterInterface::class);

        (new ImportJobSequencer(
            new EntityImporterRegistry([$importer]),
            $repository ?? $this->repository(ImportJobStatus::RUNNING),
            new ImportJobContextFactory($importDirectory),
            new ImportTruncator($deleter),
            $importDirectory,
            $this->createMock(Filesystem::class),
            $this->translator(),
            new NullLogger()
        ))->run($importJob, $batchLimit);
    }

    private function repository(ImportJobStatus $probedStatus): ImportJobRepository&MockObject
    {
        $repository = $this->createMock(ImportJobRepository::class);
        $repository->method('readStatus')->willReturn($probedStatus);

        return $repository;
    }

    private function configuration(): ConfigurationInterface
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(sys_get_temp_dir());

        return $configuration;
    }

    private function translator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []) => strtr($id, $parameters)
        );

        return $translator;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function buildJob(
        array $options = [],
        ImportJobStatus $status = ImportJobStatus::PENDING,
        ?string $currentPhaseId = null,
        int $recordCount = 100,
    ): ImportJob {
        $job = new ImportJob(
            self::UUID,
            'product',
            1,
            'products.csv',
            1,
            $recordCount,
            ['langIso' => 'en', 'multipleValueSeparator' => ',', 'fieldMapping' => [0 => 'name']],
            $options
        );
        $job->setStatus($status)->setCurrentPhaseId($currentPhaseId);

        return $job;
    }

    private function phase(string $id, bool $pausing = false): ImportPhaseDefinition
    {
        return new ImportPhaseDefinition($id, ucfirst($id), $pausing);
    }

    /**
     * @param list<ImportPhaseDefinition> $phases
     * @param array<string, int> $unitCounts
     */
    private function importer(array $phases, array $unitCounts, callable $onBatch): EntityImporterInterface
    {
        return new class($phases, $unitCounts, $onBatch) implements EntityImporterInterface {
            /**
             * @param list<ImportPhaseDefinition> $phases
             * @param array<string, int> $unitCounts
             */
            public function __construct(
                private readonly array $phases,
                private readonly array $unitCounts,
                private readonly mixed $onBatch,
            ) {
            }

            public function getEntityType(): string
            {
                return 'product';
            }

            public function getLabel(): string
            {
                return 'Products';
            }

            public function getFields(): never
            {
                throw new LogicException('The sequencer never reads the field list');
            }

            public function getPhases(): array
            {
                return $this->phases;
            }

            public function countPhaseUnits(string $phaseId, ImportJobContext $context): int
            {
                return $this->unitCounts[$phaseId] ?? 0;
            }

            public function processPhaseBatch(string $phaseId, ImportJobContext $context, int $limit): PhaseBatchResult
            {
                return ($this->onBatch)($phaseId, $context, $limit);
            }
        };
    }
}
