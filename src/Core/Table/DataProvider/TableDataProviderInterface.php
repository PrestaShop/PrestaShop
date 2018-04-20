<?php

namespace PrestaShop\PrestaShop\Core\Table\DataProvider;

interface TableDataProviderInterface
{
    public function getRows(array $filters);

    public function getRowsTotal();
}
