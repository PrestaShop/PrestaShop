<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachmentsByProduct;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\ProductAttachmentMatch;

/**
 * Handles @see SearchAttachmentsByProduct query
 */
#[AsQueryHandler]
final class SearchAttachmentsByProductHandler
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {
    }

    /**
     * @return ProductAttachmentMatch[]
     */
    public function handle(SearchAttachmentsByProduct $query): array
    {
        return $this->attachmentRepository->searchByProduct(
            $query->getPhrase(),
            $query->getLanguageId(),
            $query->getShopConstraint(),
            SearchAttachmentsByProduct::MAX_RESULTS
        );
    }
}
