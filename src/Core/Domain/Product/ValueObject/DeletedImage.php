<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Image which is about to be deleted.
 */
final class DeletedImage
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @param int $imageId
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $imageId)
    {
        $this->imageId = new ImageId($imageId);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ImageId
    {
        return $this->imageId;
    }
}
