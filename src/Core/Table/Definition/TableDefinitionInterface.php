<?php

namespace PrestaShop\PrestaShop\Core\Table\Definition;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\RowAction;

interface TableDefinitionInterface
{
    public function addColumn(Column $column);

    public function addRowAction(RowAction $rowAction);

    public function getName();

    public function getIdentifier();

    public function getDefaultOrderBy();

    public function getDefaultOrderWay();

    /**
     * @return Column[]
     */
    public function getColumns();

    public function getRowActions();
}
