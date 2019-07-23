<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

final class ImageId
{
    /**
     * @var int
     */
    private $imageId;

    /**
     * @param int $imageId
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $imageId)
    {
        if (0 > $imageId) {
            throw new ProductConstraintException(
                'Image id cannot be negative',
                ProductConstraintException::INVALID_IMAGE_ID
            );
        }

        $this->imageId = $imageId;
    }

    public function getValue(): int
    {
        return $this->imageId;
    }
}
