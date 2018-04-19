<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\Table;
use PrestaShop\PrestaShop\Core\Table\TableView;

interface TableViewFactoryInterface
{
    /**
     * Create table view data from given table data
     *
     * @param Table $table
     *
     * @return TableView
     */
    public function createViewFromTable(Table $table);
}
