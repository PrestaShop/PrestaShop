<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ImportEngine;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ContinueImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobAlreadyRunningException;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\AbstractEntityImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\RowMapper;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use PrestaShop\PrestaShop\Core\Import\File\ResumableFileReaderInterface;

/**
 * Test-only importer whose every row says what should happen to it.
 *
 * It writes nothing. The generic import features are about the SEQUENCER — what ends a phase,
 * what pauses, what fails, what a cancellation does — and an importer that touched the database
 * would make every one of those failures ambiguous. Persistence is covered entity by entity, by
 * the features of the real importers.
 *
 * Each row carries a verb and, optionally, the single phase it applies in; a row with no phase
 * behaves the same way everywhere. Two verbs dispatch commands on the job that is running them,
 * which is what makes the cancellation race and the per-job lock deterministic in one process,
 * with no sleeps.
 */
final class ScriptedEntityImporter extends AbstractEntityImporter
{
    public const ENTITY_TYPE = 'scripted';

    /**
     * Gates the finalization phase. The phase is always DECLARED — getPhases() takes no context,
     * so a phase list cannot depend on one job's options — and is skipped by counting zero units
     * when the option is absent, which is how the engine skips phases anyway.
     *
     * It also proves an option the core engine knows nothing about survives every request
     * boundary: the value is read from the context rebuilt on each batch.
     */
    public const OPTION_FINALIZE = 'scriptedFinalize';

    public const VERB_OK = 'ok';
    public const VERB_NOTICE = 'notice';
    public const VERB_WARNING = 'warning';
    public const VERB_ERROR = 'error';
    public const VERB_THROW = 'throw';
    public const VERB_CANCEL = 'cancel';
    public const VERB_REENTER = 'reenter';

    public function __construct(
        ResumableFileReaderInterface $fileReader,
        RowMapper $rowMapper,
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct($fileReader, $rowMapper);
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function getLabel(): string
    {
        return 'Scripted entities';
    }

    public function getFields(): EntityFieldCollectionInterface
    {
        return (new EntityFieldCollection())
            ->addEntityField(new EntityField('verb', 'Verb', '', true))
            ->addEntityField(new EntityField('phase', 'Phase the verb applies in'))
            ->addEntityField(new EntityField('label', 'Label'));
    }

    public function getPhases(): array
    {
        return [
            new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_VALIDATION, 'Validation', true),
            new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_DATABASE, 'Import'),
            new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_FINALIZATION, 'Finalization'),
        ];
    }

    public function countPhaseUnits(string $phaseId, ImportJobContext $context): int
    {
        $this->assertKnownPhase($phaseId);

        if (ImportPhaseDefinition::PHASE_FINALIZATION === $phaseId && !$context->getOptions()->get(self::OPTION_FINALIZE, false)) {
            return 0;
        }

        return $context->getDataRecordCount();
    }

    public function processPhaseBatch(string $phaseId, ImportJobContext $context, int $limit): PhaseBatchResult
    {
        $this->assertKnownPhase($phaseId);

        return $this->iterateBatch(
            $context,
            $limit,
            fn (array $row, int $rowIndex): array => $this->processRow($row, $rowIndex, $phaseId, $context)
        );
    }

    /**
     * @param array<string, string> $row
     *
     * @return array{messages: list<ImportMessage>, skipped: bool}
     */
    private function processRow(array $row, int $rowIndex, string $phaseId, ImportJobContext $context): array
    {
        // a row validation rejected is dead for every later phase
        if ($context->isRowSkipped($rowIndex)) {
            return ['messages' => [], 'skipped' => false];
        }

        $verb = $row['verb'] ?? self::VERB_OK;
        $scriptedPhase = $row['phase'] ?? '';
        if ('' !== $scriptedPhase && $scriptedPhase !== $phaseId) {
            $verb = self::VERB_OK;
        }

        $label = $row['label'] ?? '';

        return match ($verb) {
            self::VERB_NOTICE => ['messages' => [$this->message(ImportMessage::SEVERITY_NOTICE, $phaseId, $label, $rowIndex)], 'skipped' => false],
            self::VERB_WARNING => ['messages' => [$this->message(ImportMessage::SEVERITY_WARNING, $phaseId, $label, $rowIndex)], 'skipped' => false],
            self::VERB_ERROR => ['messages' => [$this->message(ImportMessage::SEVERITY_ERROR, $phaseId, $label, $rowIndex)], 'skipped' => true],
            self::VERB_THROW => throw new ImportEngineException(sprintf('Scripted failure on row %d', $rowIndex)),
            self::VERB_CANCEL => $this->cancelOwnJob($context),
            self::VERB_REENTER => $this->reenterOwnJob($context, $phaseId, $rowIndex),
            default => ['messages' => [], 'skipped' => false],
        };
    }

    /**
     * Cancels the job from inside the batch that is running it, so the sequencer meets a
     * cancellation it did not write at its very next status probe.
     *
     * @return array{messages: list<ImportMessage>, skipped: bool}
     */
    private function cancelOwnJob(ImportJobContext $context): array
    {
        $this->commandBus->handle(new CancelImportJobCommand($this->importJobUuidOf($context)));

        return ['messages' => [], 'skipped' => false];
    }

    /**
     * Asks for another batch of the job already running, which the per-job lock must refuse. The
     * refusal is reported rather than rethrown: a scenario asserting the lock works should not
     * have to assert the job died.
     *
     * @return array{messages: list<ImportMessage>, skipped: bool}
     */
    private function reenterOwnJob(ImportJobContext $context, string $phaseId, int $rowIndex): array
    {
        try {
            $this->commandBus->handle(new ContinueImportJobCommand($this->importJobUuidOf($context)));
        } catch (ImportJobAlreadyRunningException) {
            return ['messages' => [$this->message(ImportMessage::SEVERITY_NOTICE, $phaseId, 'concurrent batch refused', $rowIndex)], 'skipped' => false];
        }

        return ['messages' => [$this->message(ImportMessage::SEVERITY_ERROR, $phaseId, 'concurrent batch was NOT refused', $rowIndex)], 'skipped' => true];
    }

    /**
     * The context carries no job identity, but the working file is named after it. Reading it
     * back keeps the scenarios free of any wiring step, at the price of depending on that
     * naming — which only a test should do, and which a test SHOULD notice if it changes.
     */
    private function importJobUuidOf(ImportJobContext $context): string
    {
        return basename($context->getWorkingFilePath(), '.csv');
    }

    private function message(string $severity, string $phaseId, string $label, int $rowIndex): ImportMessage
    {
        return new ImportMessage(
            $severity,
            $phaseId,
            '' === $label ? sprintf('Scripted %s', $severity) : $label,
            [$rowIndex]
        );
    }
}
