<?php

namespace PrestaShop\PrestaShop\Core\Table;

final class Table
{
    /**
     * @var array|Column[]
     */
    private $columns = [];

    /**
     * @var array|RowAction[]
     */
    private $rowActions = [];

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var int
     */
    private $rowsTotal = 0;

    public function addColumn(Column $column)
    {
        $this->columns[$column->getIdentifier()] = $column;

        return $this;
    }

    public function addRowAction(RowAction $rowAction)
    {
        $this->rowActions[$rowAction->getAction()] = $rowAction;

        return $this;
    }

    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return TableView
     */
    public function createView()
    {
        $formattedRows = [];
        foreach ($this->rows as $row) {
            foreach ($row as $key => $rowColumn) {

            }
        }

        $columnsView = [];
        foreach ($this->columns as $column) {
            $columnsView[] = [
                'identifier' => $column->getIdentifier(),
                'name' => $column->getName(),
                'is_sortable' => $column->getFormType() ? true : false,
            ];
        }

        $tableView = new TableView($columnsView);

        return $tableView;
    }

    public function setRowsTotal($rowsTotal)
    {
        $this->rowsTotal = $rowsTotal;
    }
}