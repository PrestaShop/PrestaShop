<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportRunState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryHandler\GetImportRunStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportRunState;
use PrestaShopBundle\Entity\Repository\ImportRunRepository;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetImportRunStateHandler implements GetImportRunStateHandlerInterface
{
    public function __construct(
        private readonly ImportRunRepository $importRunRepository
    ) {
    }

    public function handle(GetImportRunState $query): ImportRunState
    {
        $importRun = $this->importRunRepository->getById($query->getImportRunId());

        return new ImportRunState(
            $importRun->getId(),
            $importRun->getStatus(),
            $importRun->getOffset(),
            $importRun->getTotalRows(),
            $importRun->getEntityType(),
            $importRun->getFilename(),
            $importRun->isValidateOnly(),
            $importRun->getOptions(),
            $importRun->getErrors(),
            $importRun->getWarnings(),
            $importRun->getNotices()
        );
    }
}
