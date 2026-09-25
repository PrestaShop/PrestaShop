<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use PrestaShop\PrestaShop\Adapter\Import\ImportTruncator;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\ImportJobStatus;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Drives one Continue call: enters phases, spends the caller's unit budget on the importer, and
 * persists what happened.
 *
 * The budget is spent across several importer calls rather than one. Slicing bounds what an
 * interruption costs — at most one slice of rows written after a cancellation is observed, and at
 * most one slice of progress lost to a fatal — without shrinking the batch the caller asked for.
 *
 * There are no transactions (#42385); the database arbitrates instead. Every status change is a
 * compare-and-set (ImportJobRepository::transitionStatus()) that a concurrent Cancel wins by
 * getting there first, a progress write cannot touch the status column at all, and the status is
 * re-read between slices and right before truncating. A slice that raced a cancellation still
 * persists its progress under the status the other request chose: the rows it wrote are real.
 */
final class ImportJobSequencer
{
    /**
     * Units handed to the importer at once. Not a cap on the batch: the budget keeps being spent
     * across slices.
     */
    public const MAX_UNITS_PER_STEP = 20;

    /**
     * Past this many rejected rows the file is treated as malformed rather than merely dirty.
     */
    public const MAX_INVALID_ROWS = 10000;

    public function __construct(
        private readonly EntityImporterRegistry $importerRegistry,
        private readonly ImportJobRepository $importJobRepository,
        private readonly ImportJobContextFactory $contextFactory,
        private readonly ImportTruncator $truncator,
        private readonly ImportDirectory $importDirectory,
        private readonly Filesystem $filesystem,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function run(ImportJob $importJob, ?int $batchLimit = null): void
    {
        $ledger = ImportJobMessageLedger::fromArray($importJob->getMessages());
        $context = $this->contextFactory->createFrom($importJob);

        try {
            $this->drive($importJob, $context, $ledger, $batchLimit);
        } catch (Throwable $throwable) {
            $this->fail($importJob, $context, $ledger, $this->describe($importJob, $throwable));
        }
    }

    /**
     * Everything that can throw runs in here, so a failure has exactly one landing place.
     */
    private function drive(
        ImportJob $importJob,
        ImportJobContext $context,
        ImportJobMessageLedger $ledger,
        ?int $batchLimit,
    ): void {
        $entryStatus = $importJob->getStatus();
        $options = ImportJobOptions::fromArray($importJob->getOptions());
        $budget = $batchLimit ?? $options->batchLimit;

        if (!$this->importerRegistry->has($importJob->getEntityType())) {
            $this->fail($importJob, $context, $ledger, $this->translator->trans(
                'No importer is registered for entity type "%type%" any more; the job cannot continue.',
                ['%type%' => $importJob->getEntityType()],
                'Admin.Advparameters.Notification'
            ));

            return;
        }

        $importer = $this->importerRegistry->get($importJob->getEntityType());
        $phases = $this->effectivePhases($importer, $options);
        $currentPhaseId = $context->getCurrentPhaseId();

        // a deploy changed the importer's phases while the job was paused
        if (null !== $currentPhaseId && null === $this->indexOfPhase($phases, $currentPhaseId)) {
            $this->fail($importJob, $context, $ledger, $this->translator->trans(
                'The importer no longer declares phase "%phase%"; the job cannot continue.',
                ['%phase%' => $currentPhaseId],
                'Admin.Advparameters.Notification'
            ));

            return;
        }

        // false: a Cancel got there first and the job is already settled under it, or the row is gone
        if (!$this->transition($importJob, $context, $ledger, ImportJobStatus::RUNNING)) {
            return;
        }

        // the batch reads it from here on; a purge, or a hand, may have removed it
        if (!$this->filesystem->exists($context->getWorkingFilePath())) {
            $this->fail($importJob, $context, $ledger, $this->translator->trans(
                'The working file of this import job is gone; the job cannot continue.',
                [],
                'Admin.Advparameters.Notification'
            ));

            return;
        }

        // a paused phase is finished and was just reviewed; re-evaluating it would pause forever
        if ((null === $currentPhaseId || ImportJobStatus::AWAITING_CONFIRMATION === $entryStatus)
            && !$this->enterNextPhase($importJob, $context, $importer, $phases)) {
            $this->transition($importJob, $context, $ledger, ImportJobStatus::FINISHED);

            return;
        }

        // not "while budget": closing a phase costs no units, and a job whose last unit was the
        // caller's last unit is done now, not after one more round trip
        while (true) {
            $remaining = $context->getCurrentPhaseTotalUnits() - $context->getCurrentOffset();

            // the phase is exhausted: finish the job, pause it for review, or step into the next one
            if ($remaining <= 0) {
                if (!$this->closeCurrentPhase($importJob, $context, $ledger, $importer, $phases)) {
                    return;
                }

                continue;
            }

            // the budget is spent: leave the job where the next call can pick it up
            if ($budget <= 0) {
                $this->persist($importJob, $context, $ledger);

                return;
            }

            // once, right before the first row of the database phase is written, never in a dry
            // run — and with the last word left to the database, because nothing this destructive
            // may trust a status probed at the top of the batch
            if ($this->isAboutToTruncate($context, $options)) {
                if (!$this->transition($importJob, $context, $ledger, ImportJobStatus::RUNNING)) {
                    return;
                }

                $this->truncator->truncate($importJob->getEntityType(), $context->getShopConstraint());
            }

            // hand the importer one slice, never the whole budget
            $units = min($budget, $remaining, self::MAX_UNITS_PER_STEP);
            $result = $importer->processPhaseBatch((string) $context->getCurrentPhaseId(), $context, $units);

            if (!$this->absorbBatchResult($importJob, $context, $ledger, $result)) {
                return;
            }
            $budget -= $result->processedUnitCount;

            // the database is the arbiter: another request may have cancelled the job mid-slice
            $status = $this->importJobRepository->readStatus($importJob->getUuid());
            if (null === $status) {
                // the row is gone; saving would resurrect it
                $this->removeWorkingFile($importJob->getUuid());

                return;
            }
            if ($status->isTerminal()) {
                // the rows this slice wrote are real, so the progress is recorded under the status
                // the other request chose rather than thrown away
                $importJob->setStatus($status);
                $this->settle($importJob, $context, $ledger);

                return;
            }

            $this->persist($importJob, $context, $ledger);
        }
    }

    /**
     * @param list<ImportPhaseDefinition> $phases
     *
     * @return bool false when the job is over, and was already terminated or paused
     */
    private function closeCurrentPhase(
        ImportJob $importJob,
        ImportJobContext $context,
        ImportJobMessageLedger $ledger,
        EntityImporterInterface $importer,
        array $phases,
    ): bool {
        $phaseIndex = (int) $this->indexOfPhase($phases, (string) $context->getCurrentPhaseId());

        // the last phase ending ends the job — in a dry run that is the validation phase, so a
        // validate-only job whose last phase warns finishes instead of waiting for a confirmation
        // nobody will send
        if ($phaseIndex === count($phases) - 1) {
            $this->transition($importJob, $context, $ledger, ImportJobStatus::FINISHED);

            return false;
        }

        // a pausing phase that produced something to review stops here; continuing accepts it. A
        // lost transition means a Cancel got there first, and transition() has then already
        // persisted the progress under it — the progress is written either way, once
        if ($phases[$phaseIndex]->pausing && $this->hasBlockingMessages($ledger, $phases[$phaseIndex]->id)) {
            if ($this->transition($importJob, $context, $ledger, ImportJobStatus::AWAITING_CONFIRMATION)) {
                $this->persist($importJob, $context, $ledger);
            }

            return false;
        }

        if (!$this->enterNextPhase($importJob, $context, $importer, $phases)) {
            $this->transition($importJob, $context, $ledger, ImportJobStatus::FINISHED);

            return false;
        }

        return true;
    }

    /**
     * @return bool false when the batch failed the job
     */
    private function absorbBatchResult(
        ImportJob $importJob,
        ImportJobContext $context,
        ImportJobMessageLedger $ledger,
        PhaseBatchResult $result,
    ): bool {
        // without this a zero-progress importer would spin until the request times out
        if ($result->processedUnitCount <= 0) {
            $this->fail($importJob, $context, $ledger, $this->translator->trans(
                'The importer made no progress on phase "%phase%"; the job was stopped.',
                ['%phase%' => (string) $context->getCurrentPhaseId()],
                'Admin.Advparameters.Notification'
            ));

            return false;
        }

        $context->applyBatchResult($result);
        $ledger->addAll($result->messages);

        if (count($context->getSkippedRows()) > self::MAX_INVALID_ROWS) {
            $this->fail($importJob, $context, $ledger, $this->translator->trans(
                'More than %count% rows were rejected; the file appears malformed.',
                ['%count%' => self::MAX_INVALID_ROWS],
                'Admin.Advparameters.Notification'
            ));

            return false;
        }

        return true;
    }

    /**
     * A dry run stops after validation, so the phase list — not a flag consulted later — is what
     * decides the job is over.
     *
     * @return list<ImportPhaseDefinition>
     */
    private function effectivePhases(EntityImporterInterface $importer, ImportJobOptions $options): array
    {
        $phases = $importer->getPhases();
        if (!$options->dryRun) {
            return $phases;
        }

        $kept = [];
        foreach ($phases as $phase) {
            $kept[] = $phase;
            if (ImportPhaseDefinition::PHASE_VALIDATION === $phase->id) {
                break;
            }
        }

        return $kept;
    }

    /**
     * @param list<ImportPhaseDefinition> $phases
     *
     * @return bool false when no phase is left to run
     */
    private function enterNextPhase(
        ImportJob $importJob,
        ImportJobContext $context,
        EntityImporterInterface $importer,
        array $phases,
    ): bool {
        $currentPhaseId = $context->getCurrentPhaseId();
        $next = null === $currentPhaseId ? 0 : (int) $this->indexOfPhase($phases, $currentPhaseId) + 1;

        for ($index = $next; $index < count($phases); ++$index) {
            $phase = $phases[$index];
            $totalUnits = $importer->countPhaseUnits($phase->id, $context);

            $phaseTotals = $importJob->getPhaseTotals();
            $phaseTotals[$phase->id] = $totalUnits;
            $importJob->setPhaseTotals($phaseTotals);

            if ($totalUnits <= 0) {
                continue;
            }

            $context->enterPhase($phase->id, $totalUnits);

            return true;
        }

        return false;
    }

    private function isAboutToTruncate(ImportJobContext $context, ImportJobOptions $options): bool
    {
        return $options->truncate
            && !$options->dryRun
            && ImportPhaseDefinition::PHASE_DATABASE === $context->getCurrentPhaseId()
            && 0 === $context->getCurrentOffset();
    }

    private function hasBlockingMessages(ImportJobMessageLedger $ledger, string $phaseId): bool
    {
        return $ledger->countBySeverity(ImportMessage::SEVERITY_ERROR, $phaseId) > 0
            || $ledger->countBySeverity(ImportMessage::SEVERITY_WARNING, $phaseId) > 0;
    }

    /**
     * @param list<ImportPhaseDefinition> $phases
     */
    private function indexOfPhase(array $phases, string $phaseId): ?int
    {
        foreach ($phases as $index => $phase) {
            if ($phase->id === $phaseId) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Moves the job to $to unless another request ended it first. A terminal outcome — the one
     * asked for, or the one found — settles the job: progress persisted under it, working file
     * removed. A vanished row is left alone, since saving would resurrect it.
     *
     * @return bool true when the job now holds $to; false when it is over — already settled here
     *              under the status the other request wrote, or gone — and the caller must stop
     */
    private function transition(
        ImportJob $importJob,
        ImportJobContext $context,
        ImportJobMessageLedger $ledger,
        ImportJobStatus $to,
    ): bool {
        $status = $this->importJobRepository->transitionStatus(
            $importJob->getUuid(),
            $to,
            ImportJobStatus::nonTerminalCases()
        );
        if (null === $status) {
            $this->removeWorkingFile($importJob->getUuid());

            return false;
        }

        $importJob->setStatus($status);
        if ($status->isTerminal()) {
            $this->settle($importJob, $context, $ledger);
        }

        return $status === $to;
    }

    /**
     * The last write of a job: its progress under the status the row now holds, then the working
     * file goes with it — even when that write fails, since the row is already terminal.
     */
    private function settle(ImportJob $importJob, ImportJobContext $context, ImportJobMessageLedger $ledger): void
    {
        try {
            $this->persist($importJob, $context, $ledger);
        } finally {
            $this->removeWorkingFile($importJob->getUuid());
        }
    }

    /**
     * A finished job keeps nothing to resume from. A cancelled or failed one keeps its cursor and
     * its row list along with the messages: bounded data, and what a later retry of the rest needs
     * to tell what went in (#42424).
     */
    private function persist(ImportJob $importJob, ImportJobContext $context, ImportJobMessageLedger $ledger): void
    {
        $finished = ImportJobStatus::FINISHED === $importJob->getStatus();

        $importJob
            ->setCurrentPhaseId($context->getCurrentPhaseId())
            ->setCurrentOffset($context->getCurrentOffset())
            ->setResumeCursor($finished ? null : $context->getResumeCursor())
            ->setSkippedRows($finished ? [] : $context->getSkippedRows())
            ->setSkippedRowCount(count($context->getSkippedRows()))
            ->setMessages($ledger->toArray());

        $this->importJobRepository->save($importJob);
    }

    private function fail(
        ImportJob $importJob,
        ImportJobContext $context,
        ImportJobMessageLedger $ledger,
        string $reason,
    ): void {
        $ledger->addFailure(new ImportMessage(
            ImportMessage::SEVERITY_ERROR,
            (string) ($context->getCurrentPhaseId() ?? ''),
            $reason,
        ));

        $this->transition($importJob, $context, $ledger, ImportJobStatus::FAILED);
    }

    private function removeWorkingFile(string $importJobUuid): void
    {
        $this->filesystem->remove($this->importDirectory->getWorkingFile($importJobUuid));
    }

    /**
     * Only exceptions that speak the shop's language reach the merchant — the same two the row
     * importer quotes. Anything else is a bug whose message could expose internals, so it goes to
     * the log files and the report stays generic.
     */
    private function describe(ImportJob $importJob, Throwable $throwable): string
    {
        if ($throwable instanceof ImportEngineException || $throwable instanceof DomainException) {
            return $throwable->getMessage();
        }

        $this->logger->error(
            sprintf('Import job "%s" failed', $importJob->getUuid()),
            ['exception' => $throwable]
        );

        return $this->translator->trans(
            'The import stopped on an unexpected error. See the log files for details.',
            [],
            'Admin.Advparameters.Notification'
        );
    }
}
