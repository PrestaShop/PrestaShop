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
 * The single runtime object of an import run: frozen configuration plus the
 * mutable progress of the current phase. It mirrors the ImportRun entity's
 * structure without depending on Doctrine; the adapter builds it from the
 * entity (tests build it directly).
 *
 * Importers only read from the context; the caller (batch sequencer) mutates
 * it through enterPhase() and applyBatchResult().
 *
 * Row indexes are 0-based physical record indexes in the working file. The
 * working file is produced once by ImportFileNormalizer using the canonical
 * CSV dialect: csvSeparator is the separator of the ORIGINAL upload and is
 * consumed at normalization time only — the engine reader never uses it.
 */
final class ImportRunContext
{
    /**
     * Field-mapping value marking a column as ignored.
     */
    public const COLUMN_IGNORED = 'no';

    private ?string $currentPhaseId = null;

    private int $currentOffset = 0;

    private ?string $resumeCursor = null;

    /**
     * @var array<string, array<int, true>> sparse skipped row indexes, per phase id
     */
    private array $skippedRows = [];

    /**
     * @param array<int, string> $fieldMapping column index => field name ('no' = ignored column)
     */
    public function __construct(
        private readonly string $entityType,
        private readonly string $workingFilePath,
        private readonly string $langIso,
        private readonly string $csvSeparator,
        private readonly string $multipleValueSeparator,
        private readonly int $skipRows,
        private readonly array $fieldMapping,
        private readonly ImportRunOptions $options,
        private readonly int $shopId,
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

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    public function getCsvSeparator(): string
    {
        return $this->csvSeparator;
    }

    public function getMultipleValueSeparator(): string
    {
        return $this->multipleValueSeparator;
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
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

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return ShopConstraint::shop($this->shopId);
    }

    public function enterPhase(string $phaseId): void
    {
        $this->currentPhaseId = $phaseId;
        $this->currentOffset = 0;
        $this->resumeCursor = null;
        $this->skippedRows[$phaseId] ??= [];
    }

    public function getCurrentPhaseId(): ?string
    {
        return $this->currentPhaseId;
    }

    public function getCurrentOffset(): int
    {
        return $this->currentOffset;
    }

    public function getResumeCursor(): ?string
    {
        return $this->resumeCursor;
    }

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
