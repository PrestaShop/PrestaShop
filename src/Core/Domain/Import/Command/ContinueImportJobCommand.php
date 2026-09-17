<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;

/**
 * Processes the next batch of an import job and returns its new state.
 *
 * Also how a pause is accepted: a job in AWAITING_CONFIRMATION resumes on the next Continue.
 */
final class ContinueImportJobCommand
{
    private readonly ImportJobUuid $importJobUuid;

    /**
     * @param int|null $batchLimit unit budget for this call, null uses the job's frozen batchLimit
     *                             option. Bounds the call, not one importer call — the sequencer
     *                             still slices internally
     *
     * @throws ImportJobConstraintException
     */
    public function __construct(string $importJobUuid, private readonly ?int $batchLimit = null)
    {
        if (null !== $batchLimit && $batchLimit < 1) {
            throw new ImportJobConstraintException(
                'Import batch limit must be at least one unit.',
                ImportJobConstraintException::INVALID_BATCH_LIMIT
            );
        }

        $this->importJobUuid = new ImportJobUuid($importJobUuid);
    }

    public function getImportJobUuid(): ImportJobUuid
    {
        return $this->importJobUuid;
    }

    public function getBatchLimit(): ?int
    {
        return $this->batchLimit;
    }
}
