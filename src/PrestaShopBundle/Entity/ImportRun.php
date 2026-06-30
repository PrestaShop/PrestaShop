<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunStatus;

/**
 * Server-side state of an import run: the frozen Step-1/Step-2 config, the column mapping, the
 * server-tracked offset and the cross-batch shared data. Created when the user clicks "Start
 * import"; advanced by each batch. Replaces the session-serialized runtime config + the
 * `crossStepsVars` POST blob.
 *
 * The identity is the opaque UUID surfaced in URLs/AJAX (assigned by the application, not
 * auto-incremented) so a sequential database id never leaks.
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ImportRunRepository")
 *
 * @ORM\Table()
 */
class ImportRun
{
    /**
     * @var string opaque UUID, also the public identifier
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_import_run", type="string", length=36)
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="entity_type", type="integer")
     */
    private $entityType;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="lang_iso", type="string", length=8)
     */
    private $langIso;

    /**
     * @var string
     *
     * @ORM\Column(name="csv_separator", type="string", length=8)
     */
    private $separator;

    /**
     * @var string
     *
     * @ORM\Column(name="multiple_value_separator", type="string", length=8)
     */
    private $multipleValueSeparator;

    /**
     * @var int
     *
     * @ORM\Column(name="skip_rows", type="integer")
     */
    private $skipRows;

    /**
     * @var array<int, string> frozen column index => field name mapping
     *
     * @ORM\Column(name="field_map", type="json")
     */
    private $fieldMap;

    /**
     * @var array<string, mixed> truncate / match_ref / forceIDs / regenerate / sendemail
     *
     * @ORM\Column(name="options", type="json")
     */
    private $options;

    /**
     * @var bool
     *
     * @ORM\Column(name="validate_only", type="boolean")
     */
    private $validateOnly;

    /**
     * @var int batch size frozen at start
     *
     * @ORM\Column(name="batch_limit", type="integer")
     */
    private $batchLimit;

    /**
     * @var int server-tracked progress, written after every batch
     *
     * @ORM\Column(name="current_offset", type="integer")
     */
    private $offset = 0;

    /**
     * @var int total rows to process; completion = offset >= totalRows
     *
     * @ORM\Column(name="total_rows", type="integer")
     */
    private $totalRows = 0;

    /**
     * @var array<string, mixed> the crossStepsVars/sharedData accumulated across batches
     *
     * @ORM\Column(name="shared_data", type="json")
     */
    private $sharedData = [];

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16)
     */
    private $status;

    /**
     * @var string[]
     *
     * @ORM\Column(name="errors", type="json")
     */
    private $errors = [];

    /**
     * @var string[]
     *
     * @ORM\Column(name="warnings", type="json")
     */
    private $warnings = [];

    /**
     * @var string[]
     *
     * @ORM\Column(name="notices", type="json")
     */
    private $notices = [];

    /**
     * @var int|null single-shop scope; null = inherit the BO context (multistore enforcement deferred)
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=true)
     */
    private $shopId;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private $dateUpd;

    /**
     * @param array<int, string> $fieldMap
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $id,
        int $entityType,
        string $filename,
        string $langIso,
        string $separator,
        string $multipleValueSeparator,
        int $skipRows,
        array $fieldMap,
        array $options,
        bool $validateOnly,
        int $batchLimit,
        ?int $shopId = null
    ) {
        $this->id = $id;
        $this->entityType = $entityType;
        $this->filename = $filename;
        $this->langIso = $langIso;
        $this->separator = $separator;
        $this->multipleValueSeparator = $multipleValueSeparator;
        $this->skipRows = $skipRows;
        $this->fieldMap = $fieldMap;
        $this->options = $options;
        $this->validateOnly = $validateOnly;
        $this->batchLimit = $batchLimit;
        $this->shopId = $shopId;
        $this->status = ImportRunStatus::PENDING;
        $this->dateAdd = new DateTime();
        $this->dateUpd = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEntityType(): int
    {
        return $this->entityType;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getLangIso(): string
    {
        return $this->langIso;
    }

    public function getSeparator(): string
    {
        return $this->separator;
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
    public function getFieldMap(): array
    {
        return $this->fieldMap;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function isValidateOnly(): bool
    {
        return $this->validateOnly;
    }

    public function getBatchLimit(): int
    {
        return $this->batchLimit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSharedData(): array
    {
        return $this->sharedData;
    }

    public function getStatus(): string
    {
        return $this->status;
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

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function getDateUpd(): DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setTotalRows(int $totalRows): void
    {
        $this->totalRows = $totalRows;
        $this->touch();
    }

    public function markRunning(): void
    {
        $this->status = ImportRunStatus::RUNNING;
        $this->touch();
    }

    public function markFinished(): void
    {
        $this->status = ImportRunStatus::FINISHED;
        $this->touch();
    }

    public function cancel(): void
    {
        $this->status = ImportRunStatus::CANCELLED;
        $this->touch();
    }

    /**
     * Records the outcome of one batch: advances the offset, replaces the shared data and appends
     * the batch messages to the accumulated lists.
     *
     * @param array<string, mixed> $sharedData
     * @param string[] $errors
     * @param string[] $warnings
     * @param string[] $notices
     */
    public function recordBatch(int $processedRows, array $sharedData, array $errors, array $warnings, array $notices): void
    {
        $this->offset += $processedRows;
        $this->sharedData = $sharedData;
        $this->errors = array_merge($this->errors, array_values($errors));
        $this->warnings = array_merge($this->warnings, array_values($warnings));
        $this->notices = array_merge($this->notices, array_values($notices));
        $this->touch();
    }

    private function touch(): void
    {
        $this->dateUpd = new DateTime();
    }
}
