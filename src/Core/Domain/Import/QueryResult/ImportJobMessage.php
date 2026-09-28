<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * One reported message of an import job, coalesced across every batch that produced it.
 *
 * A domain shape rather than the engine's ImportMessage: that object is built by importers,
 * modules included, and letting it into a query result would make it part of the read contract.
 */
final class ImportJobMessage
{
    /**
     * @param list<int> $rows 0-based record indexes in the working file, capped; a presenter adds
     *                        the job's skip count back to show source line numbers
     * @param int $rowCount rows ever matched, not capped, so always >= count($rows)
     */
    public function __construct(
        private readonly string $severity,
        private readonly string $phase,
        private readonly string $message,
        private readonly ?string $field,
        private readonly array $rows,
        private readonly int $rowCount,
    ) {
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getPhase(): string
    {
        return $this->phase;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @return list<int>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Whether the row list is a sample rather than the whole truth.
     */
    public function hasTruncatedRows(): bool
    {
        return $this->rowCount > count($this->rows);
    }
}
