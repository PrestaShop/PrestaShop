<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;

/**
 * Class provides attachment id
 */
class AttachmentId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     *
     * @throws AttachmentConstraintException]
     */
    public function __construct(int $id)
    {
        $this->assertIsValidId($id);

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * @param int $attachmentId
     *
     * @throws AttachmentConstraintException
     */
    private function assertIsValidId(int $attachmentId): void
    {
        if (0 >= $attachmentId) {
            throw new AttachmentConstraintException(sprintf('Invalid Attachment id %s supplied', var_export($attachmentId, true)), AttachmentConstraintException::INVALID_ID);
        }
    }
}
