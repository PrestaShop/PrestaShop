<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * Progress of one phase of an import job.
 *
 * $totalUnits is 0 until the phase is entered: the count is computed once, at entry.
 */
final class ImportJobPhaseState
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly bool $pausing,
        private readonly int $totalUnits,
        private readonly int $offset,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Whether this phase stops the job for review when it ends with warnings or errors.
     */
    public function isPausing(): bool
    {
        return $this->pausing;
    }

    public function getTotalUnits(): int
    {
        return $this->totalUnits;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getProgressPercent(): int
    {
        if ($this->totalUnits <= 0) {
            return 0;
        }

        return (int) min(100, floor(($this->offset / $this->totalUnits) * 100));
    }
}
