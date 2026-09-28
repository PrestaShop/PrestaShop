<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\BatchLimit;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;

/**
 * Processes the next batch of an import job and returns its new state.
 *
 * Also how a pause is accepted: a job in AWAITING_CONFIRMATION resumes on the next Continue.
 */
final class ContinueImportJobCommand
{
    private readonly ImportJobUuid $importJobUuid;

    private readonly ?BatchLimit $batchLimit;

    /**
     * @param int|null $batchLimit unit budget for this call, null uses the job's frozen batchLimit
     *                             option. Bounds the call, not one importer call — the sequencer
     *                             still slices internally
     *
     * @throws ImportJobConstraintException
     */
    public function __construct(string $importJobUuid, ?int $batchLimit = null)
    {
        $this->importJobUuid = new ImportJobUuid($importJobUuid);
        $this->batchLimit = null === $batchLimit ? null : new BatchLimit($batchLimit);
    }

    public function getImportJobUuid(): ImportJobUuid
    {
        return $this->importJobUuid;
    }

    public function getBatchLimit(): ?int
    {
        return $this->batchLimit?->getValue();
    }
}
