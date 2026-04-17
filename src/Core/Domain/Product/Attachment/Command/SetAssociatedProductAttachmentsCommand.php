<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use RuntimeException;

/**
 * Replaces previous product attachments association with the provided one.
 */
class SetAssociatedProductAttachmentsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var AttachmentId[]
     */
    private $attachmentIds;

    /**
     * @param int $productId
     * @param int[] $attachmentIds
     */
    public function __construct(int $productId, array $attachmentIds)
    {
        $this->productId = new ProductId($productId);
        $this->setAttachmentIds($attachmentIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return AttachmentId[]
     */
    public function getAttachmentIds(): array
    {
        return $this->attachmentIds;
    }

    /**
     * @param int[] $attachmentIds
     */
    private function setAttachmentIds(array $attachmentIds): void
    {
        if (empty($attachmentIds)) {
            throw new RuntimeException(sprintf(
                'Empty array of product attachments provided in %s. To remove all product attachments use %s.',
                self::class,
                RemoveAllAssociatedProductAttachmentsCommand::class
            ));
        }

        foreach ($attachmentIds as $attachmentId) {
            $this->attachmentIds[] = new AttachmentId($attachmentId);
        }
    }
}
