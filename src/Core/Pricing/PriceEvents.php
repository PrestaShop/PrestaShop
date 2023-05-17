<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

class PriceEvents
{
    public const BEFORE_TAX = 'onBeforeTax';
    public const AFTER_TAX = 'onAfterTax';
    public const PRICE_CALCULATED = 'onPriceCalculated';
    public const BEFORE_PRICE = 'onBeforePrice';
    public const AFTER_PRICE = 'onAfterPrice';
}
