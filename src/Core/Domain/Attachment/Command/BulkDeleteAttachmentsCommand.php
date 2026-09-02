<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

/**
 * Bulk delete attachment command is responsible for deleting Attachment
 */
class BulkDeleteAttachmentsCommand
{
    /**
     * @var AttachmentId[]
     */
    private $attachmentIds;

    /**
     * @param int[] $attachmentIds
     */
    public function __construct(array $attachmentIds)
    {
        $this->setAttachmentIds($attachmentIds);
    }

    /**
     * @return AttachmentId[]
     */
    public function getAttachmentIds(): array
    {
        return $this->attachmentIds;
    }

    /**
     * @param array $attachmentIds
     */
    private function setAttachmentIds(array $attachmentIds)
    {
        foreach ($attachmentIds as $attachmentId) {
            $this->attachmentIds[] = new AttachmentId($attachmentId);
        }
    }
}
