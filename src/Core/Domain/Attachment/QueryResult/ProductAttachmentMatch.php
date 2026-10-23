<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

/**
 * A product matching a product search, with one of its attachments
 */
class ProductAttachmentMatch
{
    public function __construct(
        private readonly AttachmentAssociatedProduct $product,
        private readonly int $attachmentId,
        private readonly string $attachmentName,
    ) {
    }

    public function getProduct(): AttachmentAssociatedProduct
    {
        return $this->product;
    }

    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    public function getAttachmentName(): string
    {
        return $this->attachmentName;
    }
}
