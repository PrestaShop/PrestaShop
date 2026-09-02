<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

class ProductForAssociation
{
    public function __construct(
        private readonly int $productId,
        private readonly string $name,
        private readonly string $reference,
        private readonly string $imageUrl,
        private readonly string $productType,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }
}
