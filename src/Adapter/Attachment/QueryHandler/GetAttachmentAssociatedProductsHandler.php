<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentAssociatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentAssociatedProduct;

/**
 * Handles @see GetAttachmentAssociatedProducts query
 */
#[AsQueryHandler]
final class GetAttachmentAssociatedProductsHandler
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {
    }

    /**
     * @return AttachmentAssociatedProduct[]
     */
    public function handle(GetAttachmentAssociatedProducts $query): array
    {
        return $this->attachmentRepository->getAssociatedProducts(
            $query->getAttachmentId(),
            $query->getLanguageId(),
            $query->getShopConstraint()
        );
    }
}
