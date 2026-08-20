<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\UnknownPhaseException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use PrestaShop\PrestaShop\Core\Import\File\ResumableFileReaderInterface;

/**
 * Recommended base class for entity importers (core and modules): it owns
 * the mechanics every importer would otherwise duplicate — cursor-resumable
 * batch iteration, phase-id validation, the default unit count — so a
 * concrete importer only declares its fields/phases and processes mapped
 * rows. Implementing EntityImporterInterface directly remains supported for
 * importers needing full control.
 */
abstract class AbstractEntityImporter implements EntityImporterInterface
{
    /**
     * @var list<string>|null memoized getPhases() ids (assertKnownPhase runs on every batch)
     */
    protected ?array $knownPhaseIds = null;

    /**
     * @param ResumableFileReaderInterface $fileReader reads the run's working file
     * @param RowMapper $rowMapper applies the run's column-to-field mapping
     */
    public function __construct(
        protected readonly ResumableFileReaderInterface $fileReader,
        protected readonly RowMapper $rowMapper,
    ) {
    }

    /**
     * Default unit count: one unit per data record in the working file — the
     * count was measured at normalization and travels with the context, so
     * this reads nothing. Override to skip phases cheaply (return 0) or
     * count different units.
     */
    public function countPhaseUnits(string $phaseId, ImportRunContext $context): int
    {
        $this->assertKnownPhase($phaseId);

        return $context->getDataRecordCount();
    }

    /**
     * Shared batch iteration: resumes at the persisted cursor, processes up
     * to $limit data rows and reports the cursor of the last consumed row so
     * the next batch resumes in O(1).
     *
     * @param callable(array<string, string>, int): array{messages: list<ImportMessage>, skipped: bool} $rowProcessor receives the MAPPED row and the 0-based data-record index
     */
    protected function iterateBatch(ImportRunContext $context, int $limit, callable $rowProcessor): PhaseBatchResult
    {
        $messages = [];
        $newlySkippedRows = [];
        $processed = 0;
        $cursor = $context->getResumeCursor();

        if ($limit <= 0) {
            return new PhaseBatchResult(0, [], [], $cursor);
        }

        foreach ($this->fileReader->readFrom($context->getWorkingFile(), $cursor) as $rowCursor => $record) {
            $rowIndex = $context->getCurrentOffset() + $processed;
            $outcome = $rowProcessor($this->rowMapper->map($record, $context), $rowIndex);

            $messages = array_merge($messages, $outcome['messages']);
            if ($outcome['skipped']) {
                $newlySkippedRows[] = $rowIndex;
            }

            ++$processed;
            $cursor = $rowCursor;

            // checked AFTER consuming, not at loop top: re-entering the
            // generator would read (and discard) one record past the batch
            if ($processed >= $limit) {
                break;
            }
        }

        return new PhaseBatchResult($processed, $messages, $newlySkippedRows, $cursor);
    }

    /**
     * @throws UnknownPhaseException when the phase id is not one of getPhases()
     */
    protected function assertKnownPhase(string $phaseId): void
    {
        $this->knownPhaseIds ??= array_map(static fn (ImportPhaseDefinition $definition): string => $definition->id, $this->getPhases());
        if (!in_array($phaseId, $this->knownPhaseIds, true)) {
            throw new UnknownPhaseException(sprintf('Unknown phase "%s" for the %s importer', $phaseId, $this->getEntityType()));
        }
    }

    /**
     * @param list<ImportMessage> $messages
     */
    protected function containsError(array $messages): bool
    {
        foreach ($messages as $message) {
            if (ImportMessage::SEVERITY_ERROR === $message->severity) {
                return true;
            }
        }

        return false;
    }

    /**
     * "Empty" from the import's point of view: no mapped column carries a
     * value (blank source lines always qualify).
     *
     * @param array<string, string> $mappedRow
     */
    protected static function isEmptyMappedRow(array $mappedRow): bool
    {
        return [] === array_filter($mappedRow, static fn (string $value): bool => '' !== $value);
    }
}
