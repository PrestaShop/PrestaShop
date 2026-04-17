<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Query;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

/**
 * Get attachment query
 */
class GetAttachment
{
    /**
     * @var AttachmentId
     */
    private $attachmentId;

    /**
     * @param int $attachmentId
     */
    public function __construct(int $attachmentId)
    {
        $this->attachmentId = new AttachmentId($attachmentId);
    }

    /**
     * @return AttachmentId
     */
    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
    }
}
