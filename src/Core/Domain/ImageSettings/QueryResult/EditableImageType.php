<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Transfers image type data for editing
 */
class EditableImageType
{
    public function __construct(
        private readonly ImageTypeId $imageTypeId,
        private readonly string $name,
        private readonly int $width,
        private readonly int $height,
        private readonly bool $products,
        private readonly bool $categories,
        private readonly bool $manufacturers,
        private readonly bool $suppliers,
        private readonly bool $stores,
    ) {
    }

    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function isProducts(): bool
    {
        return $this->products;
    }

    public function isCategories(): bool
    {
        return $this->categories;
    }

    public function isManufacturers(): bool
    {
        return $this->manufacturers;
    }

    public function isSuppliers(): bool
    {
        return $this->suppliers;
    }

    public function isStores(): bool
    {
        return $this->stores;
    }
}
