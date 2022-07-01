<?php

namespace PrestaShop\PrestaShop\Adapter\Image\Manipulator;

interface ManipulatorInterface
{
    public function getImageSize(string $filename): array;
}
