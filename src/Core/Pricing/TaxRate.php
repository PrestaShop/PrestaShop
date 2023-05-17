<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

use PrestaShop\Decimal\DecimalNumber;

class TaxRate
{
    /**
     * @var DecimalNumber
     */
    private $rate;

    public function __construct(DecimalNumber $rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return DecimalNumber
     */
    public function getRate(): DecimalNumber
    {
        return $this->rate;
    }
}
