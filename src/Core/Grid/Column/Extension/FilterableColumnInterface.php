<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Extension;

interface FilterableColumnInterface
{
    public function getFilterTypeOptionName();

    public function getFilterTypeOptionsOptionName();
}
