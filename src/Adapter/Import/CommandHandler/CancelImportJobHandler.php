<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobLock;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\CancelImportJobHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobStatusException;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\ImportJobStatus;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Handles @see CancelImportJobCommand.
 *
 * One conditional write and nothing else, so a batch running right now can neither erase it nor be
 * erased by it. That batch owns the working file until it notices, which is why the lock is taken
 * opportunistically: not getting it means someone else cleans up. Cancelling twice is not an
 * error — the second call finds what the first wrote.
 */
#[AsCommandHandler]
final class CancelImportJobHandler implements CancelImportJobHandlerInterface
{
    public function __construct(
        private readonly ImportJobRepository $importJobRepository,
        private readonly ImportJobLock $importJobLock,
        private readonly ImportDirectory $importDirectory,
        private readonly Filesystem $filesystem,
    ) {
    }

    public function handle(CancelImportJobCommand $command): void
    {
        $importJobUuid = $command->getImportJobUuid()->getValue();

        $importJob = $this->importJobRepository->findByUuid($importJobUuid);
        if (null === $importJob) {
            throw new ImportJobNotFoundException(sprintf('Import job "%s" was not found.', $importJobUuid));
        }

        $status = $this->importJobRepository->transitionStatus(
            $importJobUuid,
            ImportJobStatus::CANCELLED,
            ImportJobStatus::nonTerminalCases()
        );
        if (null === $status) {
            throw new ImportJobNotFoundException(sprintf('Import job "%s" was deleted.', $importJobUuid));
        }
        if (ImportJobStatus::CANCELLED !== $status) {
            throw new ImportJobStatusException(sprintf(
                'Import job "%s" is already %s and cannot be cancelled.',
                $importJobUuid,
                $status->value
            ));
        }

        // the same instance a batch running in this process may be holding
        $importJob->setStatus(ImportJobStatus::CANCELLED);

        // getting the lock means no batch is running, so the working file is ours to remove;
        // failing to get it means a Continue owns it and will clean up at its next status probe
        $lock = $this->importJobLock->acquire($importJobUuid);
        if (null !== $lock) {
            try {
                $this->filesystem->remove($this->importDirectory->getWorkingFile($importJobUuid));
            } finally {
                $lock->release();
            }
        }
    }
}
