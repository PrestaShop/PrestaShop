<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Command that edits image type
 */
class EditImageTypeCommand
{
    private ImageTypeId $imageTypeId;
    private ?string $name = null;
    private ?int $width = null;
    private ?int $height = null;

    /** @var value-of<ImageFitment::AVAILABLE_VALUES>|null */
    private ?string $imageFitment = null;
    private ?bool $products = null;
    private ?bool $categories = null;
    private ?bool $manufacturers = null;
    private ?bool $suppliers = null;
    private ?bool $stores = null;

    public function __construct(int $imageTypeId)
    {
        $this->imageTypeId = new ImageTypeId($imageTypeId);
    }

    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Gets the image fitment used when generating thumbnails.
     *
     * @return value-of<ImageFitment::AVAILABLE_VALUES>|null
     */
    public function getImageFitment(): ?string
    {
        return $this->imageFitment;
    }

    /**
     * Sets the image fitment used when generating thumbnails.
     *
     * @param value-of<ImageFitment::AVAILABLE_VALUES> $imageFitment
     */
    public function setImageFitment(string $imageFitment): self
    {
        ImageFitment::assertIsValid($imageFitment);

        $this->imageFitment = $imageFitment;

        return $this;
    }

    public function isProducts(): ?bool
    {
        return $this->products;
    }

    public function setProducts(bool $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function isCategories(): ?bool
    {
        return $this->categories;
    }

    public function setCategories(bool $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function isManufacturers(): ?bool
    {
        return $this->manufacturers;
    }

    public function setManufacturers(bool $manufacturers): self
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    public function isSuppliers(): ?bool
    {
        return $this->suppliers;
    }

    public function setSuppliers(bool $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    public function isStores(): ?bool
    {
        return $this->stores;
    }

    public function setStores(bool $stores): self
    {
        $this->stores = $stores;

        return $this;
    }
}
