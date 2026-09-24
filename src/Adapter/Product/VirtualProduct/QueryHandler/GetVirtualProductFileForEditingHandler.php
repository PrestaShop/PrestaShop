<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Query\GetVirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryHandler\GetVirtualProductFileForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Handles @see GetVirtualProductFileForEditing query
 */
#[AsQueryHandler]
class GetVirtualProductFileForEditingHandler implements GetVirtualProductFileForEditingHandlerInterface
{
    public function __construct(
        private readonly VirtualProductFileRepository $virtualProductFileRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetVirtualProductFileForEditing $query): VirtualProductFileForEditing
    {
        $virtualProductFile = $this->virtualProductFileRepository->get($query->getVirtualProductFileId());

        return new VirtualProductFileForEditing(
            (int) $virtualProductFile->id,
            $virtualProductFile->filename,
            $virtualProductFile->display_filename,
            (int) $virtualProductFile->nb_days_accessible,
            (int) $virtualProductFile->nb_downloadable,
            DateTimeUtil::buildDateTimeOrNull($virtualProductFile->date_expiration),
            (int) $virtualProductFile->id_product
        );
    }
}
