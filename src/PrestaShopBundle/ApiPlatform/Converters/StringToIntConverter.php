<?php

namespace PrestaShopBundle\ApiPlatform\Converters;

class StringToIntConverter implements ConverterInterface
{
    public function convert($value): int
    {
        return (int) $value;
    }
}
