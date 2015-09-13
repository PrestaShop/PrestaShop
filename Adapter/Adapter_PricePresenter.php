<?php

use PrestaShop\PrestaShop\Core\Business\Price\PricePresenterInterface;

class Adapter_PricePresenter implements PricePresenterInterface
{
    public function convertAmount($price)
    {
        return Tools::convertPrice($price);
    }

    public function format($price)
    {
        return Tools::displayPrice($price);
    }
}
