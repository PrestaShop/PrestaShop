<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

class ProductForFreeGift
{
    public function __construct(
        private readonly int $productId,
        private readonly string $name,
        private readonly string $reference,
        private readonly string $imageUrl,
        private readonly string $productType,
        private readonly bool $disabled,
        private readonly ?string $disabledReason,
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

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getDisabledReason(): ?string
    {
        return $this->disabledReason;
    }
}
