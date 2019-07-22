<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

class ProductConstraintException extends ProductException
{
    public const INVALID_NAME = 1;
    public const NAME_TOO_LONG = 2;
}
