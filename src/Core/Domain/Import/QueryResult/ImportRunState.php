<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * Full state of an import run for the wizard: status, progression and a summary of the frozen config.
 *
 * Backs the progress modal on load / refresh / resume. Collapses the previously-separate
 * EditableImportRun (config) and ImportRunProgress (progression) into one DTO.
 */
final class ImportRunState
{
    /**
     * @var string
     */
    private $importRunId;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int server-tracked progress (rows processed so far)
     */
    private $offset;

    /**
     * @var int total rows to process
     */
    private $total;

    /**
     * @var int
     */
    private $entityType;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var bool
     */
    private $validateOnly;

    /**
     * @var array<string, mixed>
     */
    private $options;

    /**
     * @var string[]
     */
    private $errors;

    /**
     * @var string[]
     */
    private $warnings;

    /**
     * @var string[]
     */
    private $notices;

    /**
     * @param array<string, mixed> $options
     * @param string[] $errors
     * @param string[] $warnings
     * @param string[] $notices
     */
    public function __construct(
        string $importRunId,
        string $status,
        int $offset,
        int $total,
        int $entityType,
        string $filename,
        bool $validateOnly,
        array $options = [],
        array $errors = [],
        array $warnings = [],
        array $notices = []
    ) {
        $this->importRunId = $importRunId;
        $this->status = $status;
        $this->offset = $offset;
        $this->total = $total;
        $this->entityType = $entityType;
        $this->filename = $filename;
        $this->validateOnly = $validateOnly;
        $this->options = $options;
        $this->errors = array_values($errors);
        $this->warnings = array_values($warnings);
        $this->notices = array_values($notices);
    }

    public function getImportRunId(): string
    {
        return $this->importRunId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getProgressPercentage(): int
    {
        if ($this->total <= 0) {
            return 0;
        }

        return (int) min(100, floor(($this->offset / $this->total) * 100));
    }

    public function getEntityType(): int
    {
        return $this->entityType;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function isValidateOnly(): bool
    {
        return $this->validateOnly;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return string[]
     */
    public function getNotices(): array
    {
        return $this->notices;
    }
}
