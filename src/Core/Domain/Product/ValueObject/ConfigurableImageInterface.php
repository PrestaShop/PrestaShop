<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Defines contract for image which can be configured.
 */
interface ConfigurableImageInterface
{
    /**
     * Gets the position of an image.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Determines if the image is used as cover image
     *
     * @return bool
     */
    public function isCover(): bool;

    /**
     * Gets captions which has key id language language and string value.
     *
     * @return string[]
     */
    public function getLocalizedCaptions(): array;
}
