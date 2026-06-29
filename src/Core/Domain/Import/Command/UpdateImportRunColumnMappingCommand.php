<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ColumnMapping;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId;

/**
 * Updates the column mapping (and rows-to-skip) of an existing import run, before execution.
 */
final class UpdateImportRunColumnMappingCommand
{
    /**
     * @var string
     */
    private $importRunId;

    /**
     * @var array<int, string>
     */
    private $dataMapping;

    /**
     * @var int
     */
    private $skipRows;

    /**
     * @param array<int, string> $dataMapping
     *
     * @throws ImportRunConstraintException
     */
    public function __construct(string $importRunId, array $dataMapping, int $skipRows = 0)
    {
        $this->importRunId = $importRunId;
        $this->dataMapping = $dataMapping;
        $this->skipRows = $skipRows;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getImportRunId(): ImportRunId
    {
        return new ImportRunId($this->importRunId);
    }

    /**
     * @return array<int, string>
     */
    public function getDataMapping(): array
    {
        return $this->dataMapping;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getColumnMapping(): ColumnMapping
    {
        return new ColumnMapping($this->dataMapping);
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }
}
