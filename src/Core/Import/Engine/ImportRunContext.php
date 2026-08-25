<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use SplFileInfo;

/**
 * The single runtime object of an import run: frozen configuration plus the
 * mutable progress of the current phase. It mirrors the ImportRun entity's
 * structure without depending on Doctrine; the adapter builds it from the
 * entity (tests build it directly).
 *
 * Importers only read from the context; the caller (batch sequencer) mutates
 * it through enterPhase() and applyBatchResult().
 *
 * Row indexes are 0-based DATA-RECORD indexes in the working file. The
 * working file is produced once by CsvImportFileNormalizer using the canonical
 * CSV dialect, with the configured skip rows already stripped: the original
 * CSV separator and the skip count are properties of the ORIGINAL upload,
 * consumed at normalization time only — the engine never sees either.
 * Presenters add the run's skip count back when they need source-file line
 * numbers.
 *
 * The ShopConstraint is the run's frozen shop scope reference: every
 * shop-sensitive read (configuration, scoped entity lookups) and every shop
 * association written during the run derives from it.
 */
class ImportRunContext
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
     * @var array<string, array<int, true>> sparse skipped row indexes, per phase id
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
        protected readonly ImportRunOptions $options,
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

    public function getOptions(): ImportRunOptions
    {
        return $this->options;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * Concrete shop id DERIVED from the constraint, for the few paths that
     * genuinely need exactly one shop (stock reads, forced-id creation).
     * Scope-aware code must use getShopConstraint() instead.
     *
     * @throws ImportEngineException when the run is not scoped to a single shop
     */
    public function getShopId(): int
    {
        if (!$this->shopConstraint->isSingleShopContext()) {
            throw new ImportEngineException('This import run is not scoped to a single shop; use getShopConstraint() instead of getShopId()');
        }

        return $this->shopConstraint->getShopId()->getValue();
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
        $this->skippedRows[$phaseId] ??= [];
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
            $this->skippedRows[$this->currentPhaseId][$rowIndex] = true;
        }
    }

    /**
     * Skipped row indexes of one phase, or of every phase when $phaseId is null.
     *
     * @return list<int>
     */
    public function getSkippedRows(?string $phaseId = null): array
    {
        if (null !== $phaseId) {
            return array_keys($this->skippedRows[$phaseId] ?? []);
        }

        $allSkippedRows = [];
        foreach ($this->skippedRows as $phaseSkippedRows) {
            $allSkippedRows += $phaseSkippedRows;
        }
        $allSkippedRows = array_keys($allSkippedRows);
        sort($allSkippedRows);

        return $allSkippedRows;
    }

    /**
     * Whether the row was skipped by any phase so far.
     */
    public function isRowSkipped(int $rowIndex): bool
    {
        foreach ($this->skippedRows as $phaseSkippedRows) {
            if (isset($phaseSkippedRows[$rowIndex])) {
                return true;
            }
        }

        return false;
    }
}
