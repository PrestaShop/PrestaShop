<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Removes the file from the given products, the products and the file themselves are kept
 */
class RemoveProductsFromAttachmentCommand
{
    private AttachmentId $attachmentId;

    /**
     * @var ProductId[]
     */
    private array $productIds;

    /**
     * @param int[] $productIds
     */
    public function __construct(int $attachmentId, array $productIds)
    {
        $this->attachmentId = new AttachmentId($attachmentId);
        $this->productIds = array_map(static fn (int $productId): ProductId => new ProductId($productId), $productIds);
    }

    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
    }

    /**
     * @return ProductId[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
