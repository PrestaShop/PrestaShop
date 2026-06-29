<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * Outcome of one import batch: progress counters and the messages collected so far.
 *
 * Returned by RunImportBatchCommand — the report is the façade's contract, mirroring the
 * payload the legacy processImportAction returns to the progress modal.
 */
final class ImportBatchReport
{
    /**
     * @var int
     */
    private $doneCount;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var bool
     */
    private $finished;

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
     * @param string[] $errors
     * @param string[] $warnings
     * @param string[] $notices
     */
    public function __construct(
        int $doneCount,
        int $totalCount,
        bool $finished,
        array $errors = [],
        array $warnings = [],
        array $notices = []
    ) {
        $this->doneCount = $doneCount;
        $this->totalCount = $totalCount;
        $this->finished = $finished;
        $this->errors = array_values($errors);
        $this->warnings = array_values($warnings);
        $this->notices = array_values($notices);
    }

    public function getDoneCount(): int
    {
        return $this->doneCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function isFinished(): bool
    {
        return $this->finished;
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
