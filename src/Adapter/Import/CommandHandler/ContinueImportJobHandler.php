<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobLock;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobSequencer;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobStateFactory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ContinueImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\ContinueImportJobHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobAlreadyRunningException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobStatusException;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;

/**
 * Handles @see ContinueImportJobCommand.
 *
 * Thin on purpose: lock, check, delegate, report. The loop belongs to the sequencer.
 *
 * The lock comes before the status probe. Probed first, the status could be stale by the time the
 * lock is held, and a Cancel that squeezed in between would have removed the working file the
 * sequencer is about to read.
 */
#[AsCommandHandler]
final class ContinueImportJobHandler implements ContinueImportJobHandlerInterface
{
    public function __construct(
        private readonly ImportJobRepository $importJobRepository,
        private readonly ImportJobLock $importJobLock,
        private readonly ImportJobSequencer $sequencer,
        private readonly ImportJobStateFactory $stateFactory,
    ) {
    }

    public function handle(ContinueImportJobCommand $command): ImportJobState
    {
        $importJobUuid = $command->getImportJobUuid()->getValue();

        $importJob = $this->importJobRepository->findByUuid($importJobUuid);
        if (null === $importJob) {
            throw new ImportJobNotFoundException(sprintf('Import job "%s" was not found.', $importJobUuid));
        }

        $lock = $this->importJobLock->acquire($importJobUuid);
        if (null === $lock) {
            throw new ImportJobAlreadyRunningException(sprintf(
                'Import job "%s" already has a batch in progress.',
                $importJobUuid
            ));
        }

        try {
            // the status the database holds, not the one this request happens to have loaded
            $status = $this->importJobRepository->readStatus($importJobUuid);
            if (null === $status) {
                throw new ImportJobNotFoundException(sprintf('Import job "%s" was deleted.', $importJobUuid));
            }
            if ($status->isTerminal()) {
                throw new ImportJobStatusException(sprintf(
                    'Import job "%s" is %s and cannot be continued.',
                    $importJobUuid,
                    $status->value
                ));
            }

            // the loaded entity may predate a status another request wrote, and the sequencer
            // decides what to do from it
            $importJob->setStatus($status);

            $this->sequencer->run($importJob, $command->getBatchLimit());
        } finally {
            $lock->release();
        }

        return $this->stateFactory->createFrom($importJob);
    }
}
