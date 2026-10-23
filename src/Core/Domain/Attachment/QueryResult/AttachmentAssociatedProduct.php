<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

/**
 * A product an attachment is associated with
 */
class AttachmentAssociatedProduct
{
    public function __construct(
        private readonly int $productId,
        private readonly string $reference,
        private readonly string $name,
        private readonly bool $active,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
