<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use Attachment;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler\GetAttachmentForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;
use PrestaShopException;
use SplFileInfo;

/**
 * Handles command that gets attachment for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetAttachmentForEditingHandler implements GetAttachmentForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttachmentNotFoundException
     */
    public function handle(GetAttachmentForEditing $query): EditableAttachment
    {
        $attachmentIdValue = $query->getAttachmentId()->getValue();

        try {
            $attachment = new Attachment($attachmentIdValue);
        } catch (PrestaShopException) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        if ($attachment->id !== $attachmentIdValue) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        $filePath = _PS_DOWNLOAD_DIR_ . $attachment->file;
        $file = file_exists($filePath) ? new SplFileInfo($filePath) : null;

        $editableAttachment = new EditableAttachment(
            $attachment->file_name,
            $attachment->name,
            $attachment->description
        );

        if (null !== $file) {
            $editableAttachment->setFile($file);
        }

        return $editableAttachment;
    }
}
