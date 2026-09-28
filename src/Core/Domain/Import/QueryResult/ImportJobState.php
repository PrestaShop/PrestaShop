<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Everything a client needs to know about an import job.
 *
 * Returned by both ContinueImportJob and GetImportJobState, so a polling client never needs a
 * second call per tick.
 */
final class ImportJobState
{
    /**
     * @param list<ImportJobPhaseState> $phases ordered as the importer declares them
     * @param list<ImportJobMessage> $messages
     * @param array<string, int> $droppedMessageCounts per severity, the occurrences the storage
     *                                                 cap discarded (a capped message recurring
     *                                                 in a later batch counts again); they are
     *                                                 NOT in $messages, so a report can say the
     *                                                 list is partial
     * @param array<string, mixed> $context frozen inputs the job context is rebuilt from
     * @param array<string, mixed> $options what the job does, unknown keys included
     */
    public function __construct(
        private readonly string $importJobUuid,
        private readonly string $entityType,
        private readonly ImportJobStatus $status,
        private readonly string $fileName,
        private readonly int $skipRows,
        private readonly ?string $currentPhaseId,
        private readonly array $phases,
        private readonly int $dataRecordCount,
        private readonly int $skippedRowCount,
        private readonly array $messages,
        private readonly array $droppedMessageCounts,
        private readonly array $context,
        private readonly array $options,
        private readonly ShopConstraint $shopConstraint,
        private readonly DateTimeImmutable $dateAdd,
        private readonly DateTimeImmutable $dateUpd,
    ) {
    }

    public function getImportJobUuid(): string
    {
        return $this->importJobUuid;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Header lines stripped at normalization; add it to a message's row indexes to show
     * source-file line numbers.
     */
    public function getSkipRows(): int
    {
        return $this->skipRows;
    }

    public function getStatus(): ImportJobStatus
    {
        return $this->status;
    }

    public function getCurrentPhaseId(): ?string
    {
        return $this->currentPhaseId;
    }

    /**
     * @return list<ImportJobPhaseState>
     */
    public function getPhases(): array
    {
        return $this->phases;
    }

    public function getDataRecordCount(): int
    {
        return $this->dataRecordCount;
    }

    public function getSkippedRowCount(): int
    {
        return $this->skippedRowCount;
    }

    /**
     * @return list<ImportJobMessage>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return array<string, int>
     */
    public function getDroppedMessageCounts(): array
    {
        return $this->droppedMessageCounts;
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

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getDateAdd(): DateTimeImmutable
    {
        return $this->dateAdd;
    }

    public function getDateUpd(): DateTimeImmutable
    {
        return $this->dateUpd;
    }

    /**
     * Progress measured in phases, each weighing the same, the current one contributing its
     * fraction.
     *
     * Not in rows: a phase may count something else entirely, and phases after the current one
     * have no unit count until they are entered, so a row-based figure would jump backwards on
     * every phase switch.
     */
    public function getProgressPercent(): int
    {
        if (ImportJobStatus::FINISHED === $this->status) {
            return 100;
        }
        if ([] === $this->phases) {
            return 0;
        }

        $completedPhases = 0;
        $currentFraction = 0.0;
        foreach ($this->phases as $index => $phase) {
            if ($phase->getId() !== $this->currentPhaseId) {
                continue;
            }
            $completedPhases = $index;
            $currentFraction = $phase->getProgressPercent() / 100;
            break;
        }

        return (int) min(100, floor((($completedPhases + $currentFraction) / count($this->phases)) * 100));
    }
}
