<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Result;

/**
 * Executor-agnostic report returned by ImportCsvFromFileHandler.
 *
 * Both the modern Importer and the legacy AdminImportController paths are mapped
 * into this shape so that consumers (and the Behat safety net) assert against a
 * stable contract regardless of which executor ran.
 */
final class ImportResult
{
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
     * @var int number of rows actually processed
     */
    private $doneCount;

    /**
     * @var int total number of importable rows in the file
     */
    private $totalCount;

    /**
     * @param string[] $errors
     * @param string[] $warnings
     * @param string[] $notices
     * @param int $doneCount
     * @param int $totalCount
     */
    public function __construct(
        array $errors = [],
        array $warnings = [],
        array $notices = [],
        int $doneCount = 0,
        int $totalCount = 0
    ) {
        $this->errors = array_values($errors);
        $this->warnings = array_values($warnings);
        $this->notices = array_values($notices);
        $this->doneCount = $doneCount;
        $this->totalCount = $totalCount;
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

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }

    public function getDoneCount(): int
    {
        return $this->doneCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
