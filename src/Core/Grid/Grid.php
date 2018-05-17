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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\MissingColumnInRowException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\View\GridView;
use Symfony\Component\Form\FormInterface;

/**
 * Class Grid is responsible for holding final Grid data
 */
final class Grid
{
    /**
     * @var GridDefinitionInterface
     */
    private $definition;

    /**
     * @var GridDataInterface
     */
    private $data;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var FormInterface
     */
    private $filterForm;

    /**
     * @param GridDefinitionInterface $definition
     * @param GridDataInterface       $data
     * @param SearchCriteriaInterface $searchCriteria
     * @param FormInterface           $filterForm
     */
    public function __construct(
        GridDefinitionInterface $definition,
        GridDataInterface $data,
        SearchCriteriaInterface $searchCriteria,
        FormInterface $filterForm
    ) {
        $this->definition = $definition;
        $this->data = $data;
        $this->searchCriteria = $searchCriteria;
        $this->filterForm = $filterForm;
    }

    /**
     * @return GridView
     */
    public function createView()
    {
        $view = new GridView(
            $this->definition->getIdentifier(),
            $this->definition->getName(),
            $this->createColumnsView(),
            $this->filterForm->createView()
        );
        $view->setBulkActions($this->createBulkActionsView());
        $view->setGridActions($this->createGridActionsView());
        $view->setData([
            'rows' => $this->createRowsView(),
            'rows_total' => $this->data->getRowsTotal(),
            'query' => $this->data->getQuery(),
        ]);
        $view->setPagination([
            'offset' => $this->searchCriteria->getOffset(),
            'limit' => $this->searchCriteria->getLimit(),
        ]);
        $view->setSorting([
            'order_by' => $this->searchCriteria->getOrderBy(),
            'order_way' => $this->searchCriteria->getOrderWay(),
        ]);

        return $view;
    }

    /**
     * Get rows data ready for rendering in template
     *
     * @return array
     */
    private function createRowsView()
    {
        $rows = $this->data->getRows();

        $rowsView = [];
        foreach ($rows as $row) {
            $rowActions = $this->getRowActions($row, $this->definition->getRowActions());
            $rowData = $this->applyColumnModifications($row, $this->definition->getColumns());

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
     * Some columns may modify row data
     *
     * @param array                     $row
     * @param ColumnCollectionInterface $columns
     *
     * @return array
     */
    private function applyColumnModifications(array $row, ColumnCollectionInterface $columns)
    {
        foreach ($columns as $column) {
            // if for some reason column does not exist in a row
            // and it doesn't have modifier
            // then let developer know that something is wrong
            if (!isset($row[$column->getIdentifier()]) && !is_callable($column->getModifier())) {
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
     * Create columns view ready for rendering
     *
     * @return array
     */
    private function createColumnsView()
    {
        $columns = $this->definition->getColumns();
        $columnsView = [];
        $positions = [];

        /** @var ColumnInterface $column */
        foreach ($columns as $key => $column) {
            $columnsView[] = [
                'identifier' => $column->getIdentifier(),
                'name' => $column->getName(),
                'is_sortable' => $column->isSortable(),
                'is_filterable' => $column->isFilterable(),
                'is_raw' => $column->isRawContent(),
            ];

            $positions[$key] = $column->getPosition();
        }

        array_multisort($positions, SORT_ASC, $columnsView);

        return $columnsView;
    }

    /**
     * Create bulk actions view ready for rendering
     *
     * @return array
     */
    private function createBulkActionsView()
    {
        $bulkActionsView = [];

        foreach ($this->definition->getBulkActions() as $bulkAction) {
            $bulkActionsView[] = [
                'identifier' => $bulkAction->getIdentifier(),
                'name' => $bulkAction->getName(),
                'icon' => $bulkAction->getIcon(),
            ];
        }

        return $bulkActionsView;
    }

    /**
     * Prepare grid actions for rendering
     *
     * @return array
     */
    private function createGridActionsView()
    {
        $gridActionsView = [];

        /** @var GridActionInterface $gridAction */
        foreach ($this->definition->getGridActions() as $gridAction) {
            $actionView = [
                'identifier' => $gridAction->getIdentifier(),
                'name' => $gridAction->getName(),
                'icon' => $gridAction->getIcon(),
                'is_rendered' => false,
            ];

            $renderer = $gridAction->getRenderer();
            if (is_callable($renderer)) {
                $actionView['content'] = call_user_func($renderer);
                $actionView['is_rendered'] = true;
            }

            $gridActionsView[] = $actionView;
        }

        return $gridActionsView;
    }
}
