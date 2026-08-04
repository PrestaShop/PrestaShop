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
 * Runs the next batch of an import run.
 *
 * The payload is just the run id: the offset (server-tracked progress) and the limit (batch size
 * frozen at start) live on the persisted run, so they are no longer re-posted on every AJAX call —
 * the handler reads and advances them itself.
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
     * @throws ImportRunConstraintException
     */
    public function __construct(string $importRunId)
    {
        $this->importRunId = $importRunId;
    }

    /**
     * @throws ImportRunConstraintException
     */
    public function getImportRunId(): ImportRunId
    {
        return new ImportRunId($this->importRunId);
    }
}
