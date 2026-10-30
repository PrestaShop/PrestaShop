<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\RemoveProductsFromAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\RemoveProductsFromAttachmentHandlerInterface;

#[AsCommandHandler]
final class RemoveProductsFromAttachmentHandler implements RemoveProductsFromAttachmentHandlerInterface
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {
    }

    public function handle(RemoveProductsFromAttachmentCommand $command): void
    {
        $this->attachmentRepository->assertAttachmentExists($command->getAttachmentId());
        $this->attachmentRepository->removeProducts($command->getAttachmentId(), $command->getProductIds());
    }
}
