<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductAttachmentUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\CommandHandler\RemoveAllAssociatedProductAttachmentsHandlerInterface;

/**
 * Removes all product-attachment associations for given product
 */
#[AsCommandHandler]
final class RemoveAllAssociatedProductAttachmentsHandler implements RemoveAllAssociatedProductAttachmentsHandlerInterface
{
    /**
     * @var ProductAttachmentUpdater
     */
    private $productAttachmentUpdater;

    /**
     * @param ProductAttachmentUpdater $productAttachmentUpdater
     */
    public function __construct(
        ProductAttachmentUpdater $productAttachmentUpdater
    ) {
        $this->productAttachmentUpdater = $productAttachmentUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllAssociatedProductAttachmentsCommand $command): void
    {
        $this->productAttachmentUpdater->setAttachments($command->getProductId(), []);
    }
}
