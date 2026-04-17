<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Query;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

/**
 * Gets attachment information for editing.
 */
class GetAttachmentForEditing
{
    /**
     * @var AttachmentId
     */
    private $attachmentId;

    /**
     * @param int $attachmentIdValue
     *
     * @throws AttachmentConstraintException
     */
    public function __construct(int $attachmentIdValue)
    {
        $this->attachmentId = new AttachmentId($attachmentIdValue);
    }

    /**
     * @return AttachmentId
     */
    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
    }
}
