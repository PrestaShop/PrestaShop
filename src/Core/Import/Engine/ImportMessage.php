<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * One structured message produced while processing an import run.
 *
 * Row indexes are 0-based data-record indexes in the working file (skip rows
 * were already stripped at normalization); presenters add the run's skip
 * count back to display source-file line numbers. An empty row list means a
 * file-level message. The list is cumulative: coalesce() merges messages that
 * are equal on every field except the rows, so one message may carry the row
 * indexes of every occurrence. The message text is already translated by the
 * importer.
 */
class ImportMessage
{
    public const SEVERITY_NOTICE = 'notice';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';

    /**
     * @param list<int> $rows
     */
    public function __construct(
        public readonly string $severity,
        public readonly string $phase,
        public readonly string $message,
        public readonly array $rows = [],
        public readonly ?string $field = null,
    ) {
    }

    /**
     * Identity of the message for coalescing purposes: every field except the
     * rows. The batch sequencer persisting messages appends the incoming rows
     * to an already-stored message with the same key instead of storing a
     * duplicate — cross-batch coalescing must use this exact key so both
     * levels always agree.
     */
    public function coalesceKey(): string
    {
        return implode("\x1f", [$this->severity, $this->phase, $this->field ?? '', $this->message]);
    }

    /**
     * Merges messages sharing a coalesceKey() into one message whose rows are
     * the deduplicated, ascending union of the merged rows. Order of first
     * occurrence is preserved. Caps are not this method's concern (the
     * persistence layer owns them).
     *
     * @param list<ImportMessage> $messages
     *
     * @return list<ImportMessage>
     */
    public static function coalesce(array $messages): array
    {
        /** @var array<string, ImportMessage> $coalesced */
        $coalesced = [];
        foreach ($messages as $message) {
            $key = $message->coalesceKey();
            if (!isset($coalesced[$key])) {
                $coalesced[$key] = $message;
                continue;
            }

            $known = $coalesced[$key];
            $rows = array_unique(array_merge($known->rows, $message->rows));
            sort($rows);
            $coalesced[$key] = new self($known->severity, $known->phase, $known->message, $rows, $known->field);
        }

        return array_values($coalesced);
    }
}
