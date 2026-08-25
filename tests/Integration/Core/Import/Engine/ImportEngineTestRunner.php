<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use RuntimeException;

/**
 * Minimal phase sequencer for tests, mirroring the batch loop the PR2
 * RunImportBatchHandler will implement: enter each phase, recompute its unit
 * count, process batches until the offset reaches the total. The default
 * batch limit is deliberately small so every test exercises cursor-based
 * resuming across batches.
 */
final class ImportEngineTestRunner
{
    /**
     * @param list<string>|null $phaseIds only run these phases (null = all)
     *
     * @return list<ImportMessage>
     */
    public function run(EntityImporterInterface $importer, ImportRunContext $context, int $batchLimit = 2, ?array $phaseIds = null): array
    {
        $messages = [];

        foreach ($importer->getPhases() as $phase) {
            if (null !== $phaseIds && !in_array($phase->id, $phaseIds, true)) {
                continue;
            }

            // count ONCE per phase entry; the context carries the total so
            // importers never rescan the file per batch
            $context->enterPhase($phase->id, $importer->countPhaseUnits($phase->id, $context));

            while ($context->getCurrentOffset() < $context->getCurrentPhaseTotalUnits()) {
                $result = $importer->processPhaseBatch(
                    $phase->id,
                    $context,
                    min($batchLimit, $context->getCurrentPhaseTotalUnits() - $context->getCurrentOffset())
                );
                $context->applyBatchResult($result);
                $messages = array_merge($messages, $result->messages);

                if (0 === $result->processedUnitCount) {
                    throw new RuntimeException(sprintf('Import made no progress in phase "%s" at offset %d/%d', $phase->id, $context->getCurrentOffset(), $context->getCurrentPhaseTotalUnits()));
                }
            }
        }

        return $messages;
    }
}
