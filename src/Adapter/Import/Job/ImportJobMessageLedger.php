<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;

/**
 * Accumulates the messages of an import job across batches, and is the serialization boundary for
 * the entity's "messages" column.
 *
 * Engine objects in, domain objects out: it is the point where a per-batch ImportMessage becomes
 * part of the CQRS read contract.
 *
 * Two caps bound a column that is decoded on every batch request. Rows are sampled per message —
 * no report lists a hundred row numbers, it shows a few and a total — while rowCount keeps
 * counting, so a notice matching two hundred thousand rows still reports the true figure. Distinct
 * messages are capped per severity, and what the cap discards is counted rather than forgotten.
 */
final class ImportJobMessageLedger
{
    public const MAX_DISTINCT_MESSAGES_PER_SEVERITY = 1000;
    public const MAX_ROWS_PER_MESSAGE = 100;

    /**
     * @var array<string, ImportMessage> keyed by ImportMessage::coalesceKey()
     */
    private array $messagesByKey = [];

    /**
     * @var array<string, int> rows ever matched, keyed like
     */
    private array $rowCountsByKey = [];

    /**
     * @var array<string, int> severity => distinct messages the cap discarded
     */
    private array $droppedMessageCounts = [];

    /**
     * @param array{items?: list<array<string, mixed>>, droppedMessages?: array<string, int>} $stored
     */
    public static function fromArray(array $stored): self
    {
        $ledger = new self();

        foreach ($stored['items'] ?? [] as $item) {
            $message = new ImportMessage(
                (string) ($item['severity'] ?? ImportMessage::SEVERITY_ERROR),
                (string) ($item['phase'] ?? ''),
                (string) ($item['message'] ?? ''),
                array_values(array_map('intval', $item['rows'] ?? [])),
                isset($item['field']) ? (string) $item['field'] : null,
            );

            $key = $message->coalesceKey();
            $ledger->messagesByKey[$key] = $message;
            $ledger->rowCountsByKey[$key] = (int) ($item['rowCount'] ?? count($message->rows));
        }

        foreach ($stored['droppedMessages'] ?? [] as $severity => $count) {
            $ledger->droppedMessageCounts[(string) $severity] = (int) $count;
        }

        return $ledger;
    }

    /**
     * @return array{items: list<array<string, mixed>>, droppedMessages: array<string, int>}
     */
    public function toArray(): array
    {
        $items = [];
        foreach ($this->messagesByKey as $key => $message) {
            $items[] = [
                'severity' => $message->severity,
                'phase' => $message->phase,
                'message' => $message->message,
                'field' => $message->field,
                'rows' => $message->rows,
                'rowCount' => $this->rowCountsByKey[$key],
            ];
        }

        return ['items' => $items, 'droppedMessages' => $this->droppedMessageCounts];
    }

    /**
     * Merges one batch into the ledger.
     *
     * The merge key is ImportMessage::coalesceKey() and nothing else: PhaseBatchResult already
     * coalesced within the batch with that key, and re-deriving it here would let the two levels
     * disagree and fuse distinct report lines.
     *
     * @param list<ImportMessage> $messages
     */
    public function addAll(array $messages): void
    {
        foreach ($messages as $message) {
            $key = $message->coalesceKey();

            if (!isset($this->messagesByKey[$key])) {
                if ($this->countStoredBySeverity($message->severity) >= self::MAX_DISTINCT_MESSAGES_PER_SEVERITY) {
                    ++$this->droppedMessageCounts[$message->severity];

                    continue;
                }

                $this->messagesByKey[$key] = $this->withCappedRows($message, []);
                $this->rowCountsByKey[$key] = count($message->rows);
                $this->droppedMessageCounts[$message->severity] ??= 0;

                continue;
            }

            $stored = $this->messagesByKey[$key];
            $this->messagesByKey[$key] = $this->withCappedRows($message, $stored->rows);
            // a row is visited once per phase and the phase is part of the key, so incoming rows
            // are always new — counting them is exact even past the row cap
            $this->rowCountsByKey[$key] += count($message->rows);
        }
    }

    /**
     * @return list<ImportJobMessage>
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->messagesByKey as $key => $message) {
            $messages[] = new ImportJobMessage(
                $message->severity,
                $message->phase,
                $message->message,
                $message->field,
                $message->rows,
                $this->rowCountsByKey[$key],
            );
        }

        return $messages;
    }

    /**
     * Distinct messages of that severity, optionally restricted to one phase — what the pause
     * predicate is built from.
     */
    public function countBySeverity(string $severity, ?string $phaseId = null): int
    {
        $count = 0;
        foreach ($this->messagesByKey as $message) {
            if ($message->severity === $severity && (null === $phaseId || $message->phase === $phaseId)) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @return array<string, int>
     */
    public function getDroppedMessageCounts(): array
    {
        return array_filter($this->droppedMessageCounts);
    }

    /**
     * @param list<int> $storedRows
     */
    private function withCappedRows(ImportMessage $message, array $storedRows): ImportMessage
    {
        $rows = $storedRows;
        foreach ($message->rows as $row) {
            if (count($rows) >= self::MAX_ROWS_PER_MESSAGE) {
                break;
            }
            $rows[] = $row;
        }

        return new ImportMessage($message->severity, $message->phase, $message->message, $rows, $message->field);
    }

    private function countStoredBySeverity(string $severity): int
    {
        return $this->countBySeverity($severity);
    }
}
