<?php

namespace PrestaShop\PrestaShop\Core\Business\Price;

interface PricePresenterInterface
{
    public function convertAmount($price);
    public function format($price);
}
