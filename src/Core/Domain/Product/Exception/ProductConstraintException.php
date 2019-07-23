<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

class ProductConstraintException extends ProductException
{
    public const INVALID_NAME = 1;
    public const NAME_TOO_LONG = 2;
    public const INVALID_UNIT_PRICE = 3;
    public const INVALID_IMAGE_ID = 4;
    public const INVALID_RETAIL_PRICE = 5;
    public const INVALID_COST_PRICE = 6;
}
