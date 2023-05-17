<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

use PrestaShop\Decimal\DecimalNumber;

class TaxAmount
{
    /**
     * @var DecimalNumber
     */
    private $amount;

    /**
     * @var TaxRate
     */
    private $rate;

    public function __construct(DecimalNumber $amount, TaxRate $rate)
    {
        $this->amount = $amount;
        $this->rate = $rate;
    }

    /**
     * @return TaxRate
     */
    public function getRate(): TaxRate
    {
        return $this->rate;
    }

    /**
     * @return DecimalNumber
     */
    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }
}
