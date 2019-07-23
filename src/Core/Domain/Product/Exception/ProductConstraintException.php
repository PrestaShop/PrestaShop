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
    public const INVALID_CUSTOMIZABLE_FEATURE_VALUE = 7;
    public const CUSTOMIZABLE_FEATURE_VALUE_TOO_LONG = 8;
    public const INVALID_META_TITLE = 9;
    public const META_TITLE_NAME_TOO_LONG = 10;
    public const INVALID_META_KEYWORDS = 11;
    public const META_KEYWORDS_NAME_TOO_LONG = 12;
    public const INVALID_META_DESCRIPTION = 13;
    public const META_DESCRIPTION_NAME_TOO_LONG = 14;
    public const FRIENDLY_URL_TOO_LONG = 15;
}
