<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Exception;

use Exception;

/**
 * Exception is thrown on attachments bulk delete failure
 */
class BulkDeleteAttachmentsException extends AttachmentException
{
    /**
     * @var int[]
     */
    private $attachmentIds;

    /**
     * @param int[] $attachmentIds
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(array $attachmentIds, $message = '', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->attachmentIds = $attachmentIds;
    }

    /**
     * @return int[]
     */
    public function getAttachmentIds(): array
    {
        return $this->attachmentIds;
    }
}
