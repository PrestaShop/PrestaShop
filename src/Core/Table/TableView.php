<?php

namespace PrestaShop\PrestaShop\Core\Table;

class TableView
{
    private $columns = [];

    public function __construct(array $columnViews)
    {
        $this->columns = $columnViews;
    }

    public function getColumns()
    {
        return $this->columns;
    }
}