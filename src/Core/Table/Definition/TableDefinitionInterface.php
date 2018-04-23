<?php

namespace PrestaShop\PrestaShop\Core\Table\Definition;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\RowAction;

/**
 * Interface TableDefinitionInterface exposes contract table definition
 */
interface TableDefinitionInterface
{
    /**
     * Get table name
     *
     * @return string
     */
    public function getName();

    /**
     * Get unique table indentifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get default order by
     *
     * @return string
     */
    public function getDefaultOrderBy();

    /**
     * Get default order way
     *
     * @return string
     */
    public function getDefaultOrderWay();

    /**
     * Get table columns
     *
     * @return Column[]
     */
    public function getColumns();

    /**
     * Get table row actions
     *
     * @return RowAction[]
     */
    public function getRowActions();

    /**
     * Add column to table definition
     *
     * @param Column $column
     */
    public function addColumn(Column $column);

    /**
     * Add row action to table definition
     *
     * @param RowAction $rowAction
     */
    public function addRowAction(RowAction $rowAction);
}
