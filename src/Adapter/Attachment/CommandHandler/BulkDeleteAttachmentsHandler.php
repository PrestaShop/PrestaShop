<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\BulkDeleteAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\BulkDeleteAttachmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\BulkDeleteAttachmentsException;

/**
 * Bulk delete attachments handler
 */
#[AsCommandHandler]
final class BulkDeleteAttachmentsHandler extends AbstractAttachmentHandler implements BulkDeleteAttachmentsHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteAttachmentsException
     */
    public function handle(BulkDeleteAttachmentsCommand $command)
    {
        $errors = [];

        foreach ($command->getAttachmentIds() as $attachmentId) {
            try {
                $attachment = $this->getAttachment($attachmentId);

                if (!$this->deleteAttachment($attachment)) {
                    $errors[] = $attachment->id;
                }
            } catch (AttachmentException) {
                $errors[] = $attachmentId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteAttachmentsException($errors, 'Failed to delete all of selected attachments');
        }
    }
}
