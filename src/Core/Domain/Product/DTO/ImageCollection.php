<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\DTO;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeletedImage;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ExistingConfigurableImage;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\NewConfigurableImage;

/**
 * Holds all product image state cases.
 */
class ImageCollection
{
    /** @var NewConfigurableImage[] */
    private $newImages;

    /** @var ExistingConfigurableImage[] */
    private $existingImages;

    /** @var DeletedImage[] */
    private $deletesImages;

    /**
     * @return NewConfigurableImage[]
     */
    public function getNewImages(): array
    {
        return $this->newImages;
    }

    /**
     * @param NewConfigurableImage[] $newImages
     *
     * @return self
     */
    public function setNewImages(array $newImages): self
    {
        $this->newImages = $newImages;

        return $this;
    }

    /**
     * @return ExistingConfigurableImage[]
     */
    public function getExistingImages(): array
    {
        return $this->existingImages;
    }

    /**
     * @param ExistingConfigurableImage[] $existingImages
     *
     * @return self
     */
    public function setExistingImages(array $existingImages): self
    {
        $this->existingImages = $existingImages;

        return $this;
    }

    /**
     * @return DeletedImage[]
     */
    public function getDeletesImages(): array
    {
        return $this->deletesImages;
    }

    /**
     * @param DeletedImage[] $deletesImages
     *
     * @return self
     */
    public function setDeletesImages(array $deletesImages): self
    {
        $this->deletesImages = $deletesImages;

        return $this;
    }
}
