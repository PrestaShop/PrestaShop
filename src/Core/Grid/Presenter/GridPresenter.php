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

namespace PrestaShop\PrestaShop\Core\Grid\Presenter;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;

/**
 * Class GridPresenter is responsible for presenting grid
 */
final class GridPresenter implements GridPresenterInterface
{
    /**
     * {@inheritdoc}
     */
    public function present(GridInterface $grid)
    {
        $definition = $grid->getDefinition();
        $searchCriteria = $grid->getSearchCriteria();
        $data = $grid->getData();

        return [
            'id' => $definition->getId(),
            'name' => $definition->getName(),
            'filter_form' => $grid->getFilterForm()->createView(),
            'columns' => $definition->getColumns()->toArray(),
            'actions' => [
//                'panel' => $definition->getPanelActions()->toArray(),
//                'bulk' => $definition->getBulkActions()->toArray(),
                'panel' => [],
                'bulk' => [],
            ],
            'data' => [
                'rows' => $this->presentRows($grid),
                'rows_total' => $data->getRowsTotal(),
                'query' => $data->getQuery(),
            ],
            'pagination' => [
                'offset' => $searchCriteria->getOffset(),
                'limit' => $searchCriteria->getLimit(),
            ],
            'sorting' => [
                'order_by' => $searchCriteria->getOrderBy(),
                'order_way' => $searchCriteria->getOrderWay(),
            ],
        ];
    }

    /**
     * Present grid data
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    private function presentRows(GridInterface $grid)
    {
        $presentedRows = [];

        $rows = $grid->getData()->getRows();
        $columns = $grid->getDefinition()->getColumns();

        foreach ($rows as $row) {
            $rowData = $this->applyColumnModifications($row, $columns);

            $presentedRows[] = $rowData;
        }

        return $presentedRows;
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
//        /** @var ColumnInterface $column */
//        foreach ($columns as $column) {
//            if (!is_callable($column->getModifier())) {
//                continue;
//            }
//
//            $row[$column->getId()] = call_user_func($column->getModifier(), $row);
//        }

        return $row;
    }
}
