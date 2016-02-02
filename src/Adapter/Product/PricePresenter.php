<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use Tools;

class PricePresenter
{
    public function convertAmount($price)
    {
        return Tools::convertPrice($price);
    }

    public function format($price)
    {
        return Tools::displayPrice($price);
    }

    public function convertAndFormat($price)
    {
        return $this->format($this->convertAmount($price));
    }
}
