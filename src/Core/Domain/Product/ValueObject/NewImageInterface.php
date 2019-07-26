<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Only new image has actual information about file. This interface defines such page.
 */
interface NewImageInterface
{
    public function getImage(): Image;
}
