<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use SplFileInfo;

/**
 * The single runtime object of an import job: frozen configuration plus the
 * mutable progress of the current phase. It mirrors the ImportJob entity's
 * structure without depending on Doctrine; the adapter builds it from the
 * entity (tests build it directly).
 *
 * Importers only read from the context; the caller (batch sequencer) mutates
 * it through enterPhase(), applyBatchResult() and, when resuming a persisted
 * job, restoreProgress().
 *
 * Row indexes are 0-based DATA-RECORD indexes in the working file. The
 * working file is produced once by CsvImportFileNormalizer using the canonical
 * CSV dialect, with the configured skip rows already stripped: the original
 * CSV separator and the skip count are properties of the ORIGINAL upload,
 * consumed at normalization time only — the engine never sees either.
 * Presenters add the job's skip count back when they need source-file line
 * numbers.
 *
 * The ShopConstraint is the job's frozen shop scope reference and the ONLY
 * shape shop scope travels in: every shop-sensitive read (configuration,
 * scoped entity lookups) and every shop association written during the job
 * derives from it. Code that needs concrete shop ids resolves the constraint
 * through ShopListResolverInterface rather than asking the context for one, so
 * that widening a job beyond a single shop stays a question the resolver and
 * the caller answer together instead of one this object decides for them.
 */
class ImportJobContext
{
    /**
     * Field-mapping value marking a column as ignored: the mapping screen's
     * "Ignore this column" dropdown option has always used the literal value
     * 'no' (legacy available_fields['no']), which the persisted mapping
     * reuses as-is.
     */
    public const COLUMN_IGNORED = 'no';

    protected ?string $currentPhaseId = null;

    protected int $currentPhaseTotalUnits = 0;

    protected int $currentOffset = 0;

    protected ?string $resumeCursor = null;

    /**
     * Sparse set of the row indexes every phase so far has given up on, keyed
     * by index so membership is a single lookup.
     *
     * Deliberately NOT split per phase: the only thing any importer asks is
     * whether a row is dead, never which phase killed it, and the phase that
     * rejected it is already recorded on the error message it produced. A flat
     * set also makes the state one JSON array for the batch sequencer to
     * persist and hand back through restoreProgress().
     *
     * @var array<int, true>
     */
    protected array $skippedRows = [];

    /**
     * @param array<int, string> $fieldMapping column index => field name ('no' = ignored column)
     */
    public function __construct(
        protected readonly string $entityType,
        protected readonly string $workingFilePath,
        protected readonly int $dataRecordCount,
        protected readonly string $langIso,
        protected readonly string $multipleValueSeparator,
        protected readonly array $fieldMapping,
        protected readonly ImportJobOptions $options,
        protected readonly ShopConstraint $shopConstraint,
    ) {
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getWorkingFilePath(): string
    {
        return $this->workingFilePath;
    }

    public function getWorkingFile(): SplFileInfo
    {
        return new SplFileInfo($this->workingFilePath);
    }

    /**
     * Number of data records in the working file, measured once during
     * normalization (nothing ever re-reads the file just to count).
     */
    public function getDataRecordCount(): int
    {
        return $this->dataRecordCount;
    }

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    public function getMultipleValueSeparator(): string
    {
        return $this->multipleValueSeparator;
    }

    /**
     * @return array<int, string>
     */
    public function getFieldMapping(): array
    {
        return $this->fieldMapping;
    }

    public function isFieldMapped(string $field): bool
    {
        return null !== $this->getFieldColumnIndex($field);
    }

    /**
     * First column index the field is mapped to, or null when unmapped.
     */
    public function getFieldColumnIndex(string $field): ?int
    {
        if (self::COLUMN_IGNORED === $field) {
            return null;
        }
        $index = array_search($field, $this->fieldMapping, true);

        return false === $index ? null : $index;
    }

    public function getOptions(): ImportJobOptions
    {
        return $this->options;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param int $totalUnits the phase's unit count, computed ONCE here by the
     *                        caller (EntityImporterInterface::countPhaseUnits());
     *                        importers read it back instead of rescanning the
     *                        file on every batch
     */
    public function enterPhase(string $phaseId, int $totalUnits): void
    {
        $this->currentPhaseId = $phaseId;
        $this->currentPhaseTotalUnits = $totalUnits;
        $this->currentOffset = 0;
        $this->resumeCursor = null;
    }

    /**
     * Replays the progress a previous request persisted, so a job resumed from
     * the database continues exactly where it stopped.
     *
     * enterPhase() deliberately rewinds offset and cursor, which is right when
     * a phase is entered for the first time and wrong when one is resumed, so
     * the batch sequencer calls this instead of the two-step dance of entering
     * the phase and then replaying a synthetic batch result. The skipped rows
     * matter as much as the cursor: they are what later phases consult to leave
     * invalid rows alone, and a job that lost them on resume would import rows
     * its validation phase had already rejected.
     *
     * @param list<int> $skippedRows every row given up on so far, all phases together
     */
    public function restoreProgress(string $phaseId, int $totalUnits, int $offset, ?string $resumeCursor, array $skippedRows): void
    {
        $this->currentPhaseId = $phaseId;
        $this->currentPhaseTotalUnits = $totalUnits;
        $this->currentOffset = $offset;
        $this->resumeCursor = $resumeCursor;
        $this->skippedRows = [];
        foreach ($skippedRows as $rowIndex) {
            $this->skippedRows[$rowIndex] = true;
        }
    }

    public function getCurrentPhaseId(): ?string
    {
        return $this->currentPhaseId;
    }

    public function getCurrentPhaseTotalUnits(): int
    {
        return $this->currentPhaseTotalUnits;
    }

    public function getCurrentOffset(): int
    {
        return $this->currentOffset;
    }

    public function getResumeCursor(): ?string
    {
        return $this->resumeCursor;
    }

    /**
     * @throws LogicException when no phase was entered first
     */
    public function applyBatchResult(PhaseBatchResult $result): void
    {
        if (null === $this->currentPhaseId) {
            throw new LogicException('Cannot apply a batch result before entering a phase');
        }

        $this->currentOffset += $result->processedUnitCount;
        $this->resumeCursor = $result->resumeCursor;
        foreach ($result->newlySkippedRows as $rowIndex) {
            $this->skippedRows[$rowIndex] = true;
        }
    }

    /**
     * Every row index given up on so far, ascending.
     *
     * @return list<int>
     */
    public function getSkippedRows(): array
    {
        $skippedRows = array_keys($this->skippedRows);
        sort($skippedRows);

        return $skippedRows;
    }

    /**
     * Whether any phase has given up on the row.
     */
    public function isRowSkipped(int $rowIndex): bool
    {
        return isset($this->skippedRows[$rowIndex]);
    }
}
