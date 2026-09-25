<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobOptions;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\ImportJob;

/**
 * Rebuilds the engine's runtime context from a job row, once per request.
 *
 * Progress is replayed with restoreProgress() rather than by entering the phase: enterPhase()
 * rewinds the offset and the cursor, which is right on first entry and wrong on resume.
 */
final class ImportJobContextFactory
{
    public function __construct(
        private readonly ImportDirectory $importDirectory,
    ) {
    }

    public function createFrom(ImportJob $importJob): ImportJobContext
    {
        $storedContext = $importJob->getContext();

        $context = new ImportJobContext(
            $importJob->getEntityType(),
            $this->importDirectory->getWorkingFile($importJob->getUuid()),
            $importJob->getDataRecordCount(),
            (string) ($storedContext['langIso'] ?? ''),
            (string) ($storedContext['multipleValueSeparator'] ?? ','),
            $storedContext['fieldMapping'] ?? [],
            ImportJobOptions::fromArray($importJob->getOptions()),
            ShopConstraint::shop($importJob->getShopId()),
        );

        $currentPhaseId = $importJob->getCurrentPhaseId();
        if (null !== $currentPhaseId) {
            $context->restoreProgress(
                $currentPhaseId,
                $importJob->getPhaseTotals()[$currentPhaseId] ?? 0,
                $importJob->getCurrentOffset(),
                $importJob->getResumeCursor(),
                $importJob->getSkippedRows(),
            );
        }

        return $context;
    }
}
