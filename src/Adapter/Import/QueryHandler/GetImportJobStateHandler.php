<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobStateFactory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryHandler\GetImportJobStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;

/**
 * Handles @see GetImportJobState.
 */
#[AsQueryHandler]
final class GetImportJobStateHandler implements GetImportJobStateHandlerInterface
{
    public function __construct(
        private readonly ImportJobRepository $importJobRepository,
        private readonly ImportJobStateFactory $stateFactory,
    ) {
    }

    public function handle(GetImportJobState $query): ImportJobState
    {
        $importJobUuid = $query->getImportJobUuid()->getValue();

        $importJob = $this->importJobRepository->findByUuid($importJobUuid);
        if (null === $importJob) {
            throw new ImportJobNotFoundException(sprintf('Import job "%s" was not found.', $importJobUuid));
        }

        return $this->stateFactory->createFrom($importJob);
    }
}
