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

/**
 * Persisted state of an import job: the configuration frozen at start, and the progress each batch
 * advances. A job outlives its request, so this row is all the next batch knows about the previous.
 *
 * Where a value lives follows three rules:
 *   - a COLUMN when it is identity, scope, indexed, written by every batch, or a scalar the report
 *     shows;
 *   - "context" when it is a frozen input ImportJobContext is rebuilt from;
 *   - "options" when it says what the job DOES — the only half modules may extend, which is why
 *     nothing core-defined about the file lives there and can be shadowed.
 *
 * Progress has to stay in columns for a second reason: a JSON blob would be rewritten whole on
 * every batch, and it is Doctrine's changed-fields-only UPDATE — a progress write never touching
 * the status column — that keeps a cancellation landing mid-batch from being erased by the batch
 * still running. There are no transactions (see ImportJobSequencer).
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ImportJobRepository")
 *
 * @ORM\Table(indexes={@ORM\Index(name="status_date_upd", columns={"status", "date_upd"})})
 *
 * @ORM\HasLifecycleCallbacks
 */
class ImportJob
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="import_job_uuid", type="string", length=36, options={"fixed": true})
     */
    private string $uuid;

    /**
     * @ORM\Column(name="entity_type", type="string", length=64)
     */
    private string $entityType;

    /**
     * Only single-shop scopes are accepted, so the resolved id is enough and the context factory
     * rebuilds the ShopConstraint from it.
     *
     * @ORM\Column(name="id_shop", type="integer", options={"unsigned": true})
     */
    private int $shopId;

    /**
     * @ORM\Column(name="status", type="string", length=32, enumType="PrestaShopBundle\Entity\ImportJobStatus")
     */
    private ImportJobStatus $status = ImportJobStatus::PENDING;

    /**
     * Original upload name. The source is never read again — and may have been deleted after
     * normalization — so the report needs it here.
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private string $filename;

    /**
     * Header lines stripped at normalization. Kept because a presenter adds it back to record
     * indexes to show source-file line numbers.
     *
     * @ORM\Column(name="skip_rows", type="integer", options={"default": 0})
     */
    private int $skipRows = 0;

    /**
     * Data records in the working file, measured once during normalization. Not "rows": the header
     * is already gone.
     *
     * @ORM\Column(name="data_record_count", type="integer", options={"default": 0})
     */
    private int $dataRecordCount = 0;

    /**
     * Kept after a terminal status, so the report can name the phase the job stopped in.
     *
     * @ORM\Column(name="current_phase_id", type="string", length=64, nullable=true)
     */
    private ?string $currentPhaseId = null;

    /**
     * Units consumed in the current phase, NOT a position in the file — it accumulates the count
     * each batch reports, and rewinds to zero on phase entry. The file position is resume_cursor.
     *
     * @ORM\Column(name="current_offset", type="integer", options={"default": 0})
     */
    private int $currentOffset = 0;

    /**
     * Where the reader resumes: opaque, meaningful only to the reader that produced it (a byte
     * offset for CSV). Stored verbatim and never parsed.
     *
     * @ORM\Column(name="resume_cursor", type="string", length=255, nullable=true)
     */
    private ?string $resumeCursor = null;

    /**
     * Survives terminal cleanup, unlike skipped_rows, so the final report can still report it.
     *
     * @ORM\Column(name="skipped_row_count", type="integer", options={"default": 0})
     */
    private int $skippedRowCount = 0;

    /**
     * Filled progressively, one entry per phase entered: a phase's unit count depends on what
     * earlier phases skipped, so it cannot be computed upfront. An unentered phase is absent.
     *
     * @var array<string, int> phase id => unit count
     *
     * @ORM\Column(name="phase_totals", type="json")
     */
    private array $phaseTotals = [];

    /**
     * Cumulative across phases rather than a per-phase map: later phases ask whether a row is
     * dead, never which phase killed it (that is on the error message the row produced).
     *
     * @var list<int>
     *
     * @ORM\Column(name="skipped_rows", type="json")
     */
    private array $skippedRows = [];

    /**
     * @var array{items?: list<array<string, mixed>>, droppedMessages?: array<string, int>}
     *
     * @ORM\Column(name="messages", type="json")
     */
    private array $messages = [];

    /**
     * The frozen inputs ImportJobContext is rebuilt from on every batch: langIso,
     * multipleValueSeparator, fieldMapping.
     *
     * csvSeparator is NOT here: it is consumed once, normalizing the source, and has no reader
     * afterwards.
     *
     * @var array<string, mixed>
     *
     * @ORM\Column(name="context", type="json")
     */
    private array $context;

    /**
     * ImportJobOptions::toArray(): truncate, forceIds, matchRef, sendEmail, dryRun, keepSourceFile,
     * batchLimit, plus any key a module importer declared, round-tripped untouched.
     *
     * @var array<string, mixed>
     *
     * @ORM\Column(name="options", type="json")
     */
    private array $options;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTimeInterface $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTimeInterface $dateUpd;

    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $uuid,
        string $entityType,
        int $shopId,
        string $filename,
        int $skipRows,
        array $context,
        array $options,
    ) {
        $this->uuid = $uuid;
        $this->entityType = $entityType;
        $this->shopId = $shopId;
        $this->filename = $filename;
        $this->skipRows = $skipRows;
        $this->context = $context;
        $this->options = $options;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->dateAdd = new DateTime();
        $this->dateUpd = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->dateUpd = new DateTime();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getStatus(): ImportJobStatus
    {
        return $this->status;
    }

    public function setStatus(ImportJobStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }

    public function getDataRecordCount(): int
    {
        return $this->dataRecordCount;
    }

    public function setDataRecordCount(int $dataRecordCount): self
    {
        $this->dataRecordCount = $dataRecordCount;

        return $this;
    }

    public function getCurrentPhaseId(): ?string
    {
        return $this->currentPhaseId;
    }

    public function setCurrentPhaseId(?string $currentPhaseId): self
    {
        $this->currentPhaseId = $currentPhaseId;

        return $this;
    }

    public function getCurrentOffset(): int
    {
        return $this->currentOffset;
    }

    public function setCurrentOffset(int $currentOffset): self
    {
        $this->currentOffset = $currentOffset;

        return $this;
    }

    public function getResumeCursor(): ?string
    {
        return $this->resumeCursor;
    }

    public function setResumeCursor(?string $resumeCursor): self
    {
        $this->resumeCursor = $resumeCursor;

        return $this;
    }

    public function getSkippedRowCount(): int
    {
        return $this->skippedRowCount;
    }

    public function setSkippedRowCount(int $skippedRowCount): self
    {
        $this->skippedRowCount = $skippedRowCount;

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function getPhaseTotals(): array
    {
        return $this->phaseTotals;
    }

    /**
     * @param array<string, int> $phaseTotals
     */
    public function setPhaseTotals(array $phaseTotals): self
    {
        $this->phaseTotals = $phaseTotals;

        return $this;
    }

    /**
     * @return list<int>
     */
    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }

    /**
     * @param list<int> $skippedRows
     */
    public function setSkippedRows(array $skippedRows): self
    {
        $this->skippedRows = $skippedRows;

        return $this;
    }

    /**
     * @return array{items?: list<array<string, mixed>>, droppedMessages?: array<string, int>}
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array{items?: list<array<string, mixed>>, droppedMessages?: array<string, int>} $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function getDateUpd(): DateTimeInterface
    {
        return $this->dateUpd;
    }
}
