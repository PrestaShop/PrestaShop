<?php

class Adapter_PricePresenter
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
