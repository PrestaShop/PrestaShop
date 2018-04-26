<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Column;
use PrestaShop\PrestaShop\Core\Grid\Exception\MissingColumnInRowException;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\GridView;

/**
 * Class GridViewFactory is responsible for creating grid view data that is passed to template for rendering
 */
final class GridViewFactory implements GridViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createView(Grid $grid)
    {
        $gridView = new GridView(
            $grid->getIdentifier(),
            $grid->getName(),
            $this->getColumnsView($grid),
            $this->getRowsView($grid),
            $grid->getRowsTotal(),
            $grid->getFilterForm()->createView()
        );

        return $gridView;
    }

    /**
     * Get rows data ready for rendering in template
     *
     * @param Grid $grid
     *
     * @return array
     */
    private function getRowsView(Grid $grid)
    {
        $rowsView = [];

        foreach ($grid->getRows() as $row) {
            $rowActions = $this->getRowActions($row, $grid->getRowActions());
            $rowData = $this->applyColumnModifications($row, $grid->getColumns());

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
     * @param array                        $row
     * @param RowActionCollectionInterface $rowActions
     *
     * @return array
     */
    private function getRowActions(array $row, RowActionCollectionInterface $rowActions)
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
     * @param Grid $grid
     *
     * @return array
     */
    private function getColumnsView(Grid $grid)
    {
        $columnsView = [];

        foreach ($grid->getColumns() as $column) {
            $columnsView[] = [
                'identifier' => $column->getIdentifier(),
                'name' => $column->getName(),
                'is_sortable' => $column->isSortable(),
                'is_filterable' => $column->isFilterable(),
            ];
        }

        return $columnsView;
    }
}
