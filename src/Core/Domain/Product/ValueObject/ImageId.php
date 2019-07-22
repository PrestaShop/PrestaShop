<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

class ImageId
{
    /**
     * @var int
     */
    private $imageId;

    public function __construct(int $imageId)
    {
        $this->imageId = $imageId;
    }

    public function getValue(): int
    {
        return $this->imageId;
    }
}
