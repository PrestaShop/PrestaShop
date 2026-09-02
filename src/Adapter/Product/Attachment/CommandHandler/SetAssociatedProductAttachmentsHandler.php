<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductAttachmentUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\SetAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\CommandHandler\SetAssociatedProductAttachmentsHandlerInterface;

/**
 * Handles @see SetAssociatedProductAttachmentsCommand using legacy object model
 */
#[AsCommandHandler]
final class SetAssociatedProductAttachmentsHandler implements SetAssociatedProductAttachmentsHandlerInterface
{
    /**
     * @var ProductAttachmentUpdater
     */
    private $productAttachmentUpdater;

    /**
     * @param ProductAttachmentUpdater $productUpdater
     */
    public function __construct(
        ProductAttachmentUpdater $productUpdater
    ) {
        $this->productAttachmentUpdater = $productUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetAssociatedProductAttachmentsCommand $command): void
    {
        $this->productAttachmentUpdater->setAttachments($command->getProductId(), $command->getAttachmentIds());
    }
}
