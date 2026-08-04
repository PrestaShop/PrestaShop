<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\RunImportBatchCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\RunImportBatchHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunAlreadyRunningException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunStatusException;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportBatchReport;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunStatus;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfig;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfig;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerFinderInterface;
use PrestaShop\PrestaShop\Core\Import\ImporterInterface;
use PrestaShopBundle\Entity\ImportRun;
use PrestaShopBundle\Entity\Repository\ImportRunRepository;
use Symfony\Component\Lock\LockFactory;

/**
 * Runs the next batch of a persisted import run, reusing the existing {@see ImporterInterface}
 * + per-entity handlers. Offset/limit/sharedData are read from and written back to the run, so the
 * AJAX payload no longer carries them.
 *
 * A per-run, non-blocking lock serializes batches of the same run: a concurrent second batch fails
 * fast (it does not queue) so the offset can never be advanced twice in parallel. Two different runs
 * use different keys and run in parallel.
 *
 * Only entity types with a modern import handler (today: products, categories) are executable here;
 * the {@see ImportHandlerFinderInterface} throws for the others, which still run through the legacy
 * controller until their handlers are ported.
 *
 * @internal
 */
#[AsCommandHandler]
final class RunImportBatchHandler implements RunImportBatchHandlerInterface
{
    public function __construct(
        private readonly ImportRunRepository $importRunRepository,
        private readonly ImporterInterface $importer,
        private readonly ImportHandlerFinderInterface $importHandlerFinder,
        private readonly LockFactory $lockFactory
    ) {
    }

    public function handle(RunImportBatchCommand $command): ImportBatchReport
    {
        $importRun = $this->importRunRepository->getById($command->getImportRunId());

        if (ImportRunStatus::CANCELLED === $importRun->getStatus()) {
            throw new ImportRunStatusException(sprintf('Import run "%s" was cancelled and cannot run a batch.', $importRun->getId()));
        }

        if (ImportRunStatus::FINISHED === $importRun->getStatus()) {
            return $this->buildReport($importRun, true);
        }

        $lock = $this->lockFactory->createLock('import-run-' . $importRun->getId());
        if (!$lock->acquire()) {
            throw new ImportRunAlreadyRunningException(sprintf('Import run "%s" already has a batch in progress.', $importRun->getId()));
        }

        try {
            if (ImportRunStatus::PENDING === $importRun->getStatus()) {
                $importRun->markRunning();
            }

            $runtimeConfig = new ImportRuntimeConfig(
                $importRun->isValidateOnly(),
                $importRun->getOffset(),
                $importRun->getBatchLimit(),
                $importRun->getSharedData(),
                $importRun->getFieldMap()
            );

            $this->importer->import(
                $this->buildImportConfig($importRun),
                $runtimeConfig,
                $this->importHandlerFinder->find($importRun->getEntityType())
            );

            $result = $runtimeConfig->toArray();
            $sharedData = is_array($result['crossStepsVariables'] ?? null) ? $result['crossStepsVariables'] : [];
            $finished = (bool) ($result['isFinished'] ?? true);

            $importRun->recordBatch(
                $runtimeConfig->getNumberOfProcessedRows(),
                $sharedData,
                $result['errors'] ?? [],
                $result['warnings'] ?? [],
                $result['notices'] ?? []
            );

            if ($finished) {
                $importRun->markFinished();
            }

            $this->importRunRepository->save($importRun);

            return $this->buildReport($importRun, $finished);
        } finally {
            $lock->release();
        }
    }

    private function buildImportConfig(ImportRun $importRun): ImportConfig
    {
        $options = $importRun->getOptions();

        return new ImportConfig(
            $importRun->getFilename(),
            $importRun->getEntityType(),
            $importRun->getLangIso(),
            $importRun->getSeparator(),
            $importRun->getMultipleValueSeparator(),
            (bool) ($options['truncate'] ?? false),
            (bool) ($options['regenerate'] ?? false),
            (bool) ($options['match_ref'] ?? false),
            (bool) ($options['forceIDs'] ?? false),
            (bool) ($options['sendemail'] ?? true),
            $importRun->getSkipRows()
        );
    }

    private function buildReport(ImportRun $importRun, bool $finished): ImportBatchReport
    {
        return new ImportBatchReport(
            $importRun->getOffset(),
            $importRun->getTotalRows(),
            $finished || ImportRunStatus::FINISHED === $importRun->getStatus(),
            $importRun->getErrors(),
            $importRun->getWarnings(),
            $importRun->getNotices()
        );
    }
}
