<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportRunCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\CancelImportRunHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunStatusException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunStatus;
use PrestaShopBundle\Entity\Repository\ImportRunRepository;

/**
 * @internal
 */
#[AsCommandHandler]
final class CancelImportRunHandler implements CancelImportRunHandlerInterface
{
    public function __construct(
        private readonly ImportRunRepository $importRunRepository
    ) {
    }

    public function handle(CancelImportRunCommand $command): void
    {
        $importRun = $this->importRunRepository->getById($command->getImportRunId());

        if (ImportRunStatus::FINISHED === $importRun->getStatus()) {
            throw new ImportRunStatusException(sprintf('Import run "%s" is already finished and cannot be cancelled.', $importRun->getId()));
        }

        $importRun->cancel();
        $this->importRunRepository->save($importRun);
    }
}
