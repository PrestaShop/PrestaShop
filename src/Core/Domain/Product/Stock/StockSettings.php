<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock;

class StockSettings
{
    /**
     * this is the biggest int number that can be saved in database, bigger than this will throw error
     */
    public const INT_32_MAX_POSITIVE = 2147483647;

    /**
     * this is the smallest int number that can be saved in database, smaller than this will throw error
     */
    public const INT_32_MAX_NEGATIVE = -2147483648;
}
