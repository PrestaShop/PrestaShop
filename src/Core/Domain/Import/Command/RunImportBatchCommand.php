<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId;

/**
 * Runs one batch (offset/limit) of an import run.
 *
 * The handler returns an {@see PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportBatchReport}: returning a DTO from a
 * command is intentional here — the batch report is the façade's contract and mirrors the payload
 * the legacy processImportAction returns to the progress modal after each AJAX call.
 */
final class RunImportBatchCommand
{
    /**
     * @var string
     */
    private $importRunId;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @throws ImportRunConstraintException
     */
    public function __construct(string $importRunId, int $offset, int $limit)
    {
        $this->importRunId = $importRunId;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getImportRunId(): ImportRunId
    {
        return new ImportRunId($this->importRunId);
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
