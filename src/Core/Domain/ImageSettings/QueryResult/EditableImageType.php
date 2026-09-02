<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Transfers image type data for editing
 */
class EditableImageType
{
    /**
     * @param ImageTypeId $imageTypeId ID of the image type
     * @param string $name Name of the image type
     * @param int $width Width of the image
     * @param int $height Height of the image
     * @param bool $products Whether the image type is used for products
     * @param bool $categories Whether the image type is used for categories
     * @param bool $manufacturers Whether the image type is used for manufacturers
     * @param bool $suppliers Whether the image type is used for suppliers
     * @param bool $stores Whether the image type is used for stores
     * @param value-of<ImageFitment::AVAILABLE_VALUES> $imageFitment
     */
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
        private readonly string $imageFitment = ImageFitment::FIT,
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

    /**
     * Gets the image fitment used when generating thumbnails.
     *
     * @return value-of<ImageFitment::AVAILABLE_VALUES>
     */
    public function getImageFitment(): string
    {
        return $this->imageFitment;
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
