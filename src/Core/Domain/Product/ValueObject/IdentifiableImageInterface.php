<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Image which can be identified and found in the system by its id.
 */
interface IdentifiableImageInterface
{
    /**
     * @return ImageId
     */
    public function getId(): ImageId;
}
