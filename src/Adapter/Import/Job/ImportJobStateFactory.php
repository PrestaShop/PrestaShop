<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobPhaseState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShopBundle\Entity\ImportJob;

/**
 * Builds the single read shape of the domain from a job row.
 *
 * Also the seam between the two status enums: the entity has its own so persistence does not
 * depend on the CQRS layer, and they share their values.
 */
final class ImportJobStateFactory
{
    public function __construct(
        private readonly EntityImporterRegistry $importerRegistry,
    ) {
    }

    public function createFrom(ImportJob $importJob): ImportJobState
    {
        $ledger = ImportJobMessageLedger::fromArray($importJob->getMessages());

        return new ImportJobState(
            $importJob->getUuid(),
            $importJob->getEntityType(),
            ImportJobStatus::from($importJob->getStatus()->value),
            $importJob->getFileName(),
            $importJob->getSkipRows(),
            $importJob->getCurrentPhaseId(),
            $this->buildPhases($importJob),
            $importJob->getDataRecordCount(),
            $importJob->getSkippedRowCount(),
            $ledger->getMessages(),
            $ledger->getDroppedMessageCounts(),
            $importJob->getContext(),
            $importJob->getOptions(),
            ShopConstraint::shop($importJob->getShopId()),
            DateTimeImmutable::createFromInterface($importJob->getDateAdd()),
            DateTimeImmutable::createFromInterface($importJob->getDateUpd()),
        );
    }

    /**
     * Phases come from the importer, so a job whose module was uninstalled reports none rather
     * than failing the read — the status and the messages still have to reach the report. Only a
     * Continue turns that job into a failure.
     *
     * A phase before the current one is complete by definition, so its offset is its total; the
     * phases after it have not been counted yet.
     *
     * @return list<ImportJobPhaseState>
     */
    private function buildPhases(ImportJob $importJob): array
    {
        if (!$this->importerRegistry->has($importJob->getEntityType())) {
            return [];
        }

        $phases = $this->importerRegistry->get($importJob->getEntityType())->getPhases();
        $phaseTotals = $importJob->getPhaseTotals();
        $currentPhaseId = $importJob->getCurrentPhaseId();

        $currentIndex = null;
        foreach ($phases as $index => $phase) {
            if ($phase->id === $currentPhaseId) {
                $currentIndex = $index;

                break;
            }
        }

        $states = [];
        foreach ($phases as $index => $phase) {
            $totalUnits = $phaseTotals[$phase->id] ?? 0;

            if (null === $currentIndex || $index > $currentIndex) {
                $offset = 0;
            } elseif ($index < $currentIndex) {
                $offset = $totalUnits;
            } else {
                $offset = $importJob->getCurrentOffset();
            }

            $states[] = new ImportJobPhaseState($phase->id, $phase->label, $phase->pausing, $totalUnits, $offset);
        }

        return $states;
    }
}
