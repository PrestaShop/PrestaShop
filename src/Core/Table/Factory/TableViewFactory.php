<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\Exception\MissingColumnInRowException;
use PrestaShop\PrestaShop\Core\Table\RowAction;
use PrestaShop\PrestaShop\Core\Table\Table;
use PrestaShop\PrestaShop\Core\Table\TableView;
use Symfony\Component\Form\FormInterface;

/**
 * Class TableViewFactory is responsible for creating table view data that is passed to template for table rendering
 */
final class TableViewFactory implements TableViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createViewFromTable(Table $table)
    {
        $rowsView = $this->getRowsView($table);
        $columnsView = $this->getColumnsView($table);

        $tableView = new TableView(
            $table->getIdentifier(),
            $table->getName(),
            $columnsView,
            $rowsView,
            $table->getRowsTotal()
        );

        if (($form = $table->getForm()) instanceof FormInterface) {
            $tableView->setFormView($form->createView());
        }

        return $tableView;
    }

    /**
     * Get rows data ready for rendering in template
     *
     * @param Table $table
     *
     * @return array
     */
    private function getRowsView(Table $table)
    {
        $rowsView = [];

        foreach ($table->getRows() as $row) {
            $rowActions = $this->getRowActions($row, $table->getRowActions());
            $rowData = $this->applyColumnModifications($row, $table->getColumns());

            $rowsView[] = [
                'actions' => $rowActions,
                'data' => $rowData,
            ];
        }

        return $rowsView;
    }

    /**
     * Get available actions for single row
     *
     * @param array $row
     * @param array|RowAction[] $rowActions
     *
     * @return array
     */
    private function getRowActions(array $row, array $rowActions)
    {
        $actions = [];

        foreach ($rowActions as $rowAction) {
            $url = call_user_func($rowAction->getCallback(), $row);

            // we expect URL to be returned
            // if callback does not return string
            // then assume that row action is not available for current row
            if (!is_string($url)) {
                continue;
            }

            $actions[$rowAction->getIdentifier()] = [
                'name' => $rowAction->getName(),
                'icon' => $rowAction->getIcon(),
                'url' => $url,
            ];
        }

        return $actions;
    }

    /**
     * Some columns may modify data that comes directly from database or any other data source.
     * Example scenarios: add currency prefix to price, format data as HTML content, add colors & etc.
     *
     * @param array $row
     * @param array|Column[] $columns
     *
     * @return array
     */
    private function applyColumnModifications(array $row, array $columns)
    {
        foreach ($columns as $column) {
            // if for some reason column does not exist in a row
            // then let developer know that something is wrong
            if (!isset($row[$column->getIdentifier()])) {
                throw new MissingColumnInRowException(
                    sprintf('Column "%s" does not exist in row "%s"', $column->getIdentifier(), json_encode($row))
                );
            }

            // if column does not modify data
            // then we keep original column data and skip modification
            if (!is_callable($column->getModifier())) {
                continue;
            }

            $row[$column->getIdentifier()] = call_user_func($column->getModifier(), $row);
        }

        return $row;
    }

    /**
     * @param Table $table
     *
     * @return array
     */
    private function getColumnsView(Table $table)
    {
        $columnsView = [];

        foreach ($table->getColumns() as $column) {
            $columnsView[] = [
                'identifier' => $column->getIdentifier(),
                'name' => $column->getName(),
                'is_sortable' => $column->isSortable(),
                'is_filterable' => $column->getFormType() ? true : false,
            ];
        }

        return $columnsView;
    }
}
