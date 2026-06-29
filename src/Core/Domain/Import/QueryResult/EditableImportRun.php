<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * Editable state of an import run, used to render the data-matching step.
 */
final class EditableImportRun
{
    /**
     * @var string
     */
    private $importRunId;

    /**
     * @var int
     */
    private $entityType;

    /**
     * @var string
     */
    private $langIso;

    /**
     * @var array<string, mixed>
     */
    private $options;

    /**
     * @var array<int, string>
     */
    private $columnMapping;

    /**
     * @var int
     */
    private $skipRows;

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $validateOnly;

    /**
     * @param array<string, mixed> $options
     * @param array<int, string> $columnMapping
     */
    public function __construct(
        string $importRunId,
        int $entityType,
        string $langIso,
        array $options,
        array $columnMapping,
        int $skipRows,
        string $status,
        bool $validateOnly
    ) {
        $this->importRunId = $importRunId;
        $this->entityType = $entityType;
        $this->langIso = $langIso;
        $this->options = $options;
        $this->columnMapping = $columnMapping;
        $this->skipRows = $skipRows;
        $this->status = $status;
        $this->validateOnly = $validateOnly;
    }

    public function getImportRunId(): string
    {
        return $this->importRunId;
    }

    public function getEntityType(): int
    {
        return $this->entityType;
    }

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array<int, string>
     */
    public function getColumnMapping(): array
    {
        return $this->columnMapping;
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isValidateOnly(): bool
    {
        return $this->validateOnly;
    }
}
