<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Presenter;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormView;

/**
 * Class GridPresenter is responsible for presenting grid.
 */
final class GridPresenter implements GridPresenterInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function present(GridInterface $grid)
    {
        $definition = $grid->getDefinition();
        $searchCriteria = $grid->getSearchCriteria();
        $data = $grid->getData();
        $presentedGrid = [
            'id' => $definition->getId(),
            'name' => $definition->getName(),
            'filter_form' => $this->getFilterForm($grid),
            'form_prefix' => '',
            'columns' => $this->getColumns($grid),
            'column_filters' => $this->getColumnFilters($definition),
            'actions' => [
                'grid' => $definition->getGridActions()->toArray(),
                'bulk' => $definition->getBulkActions()->toArray(),
            ],
            'data' => [
                'records' => $data->getRecords(),
                'records_total' => $data->getRecordsTotal(),
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
            'filters' => $searchCriteria->getFilters(),
            'attributes' => [
                'is_empty_state' => $this->isEmptyState($grid),
            ],
            'view_options' => $definition->getViewOptions()->all(),
        ];

        if ($searchCriteria instanceof Filters) {
            $presentedGrid['form_prefix'] = $searchCriteria->getFilterId();
        }

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridPresenterModifier', [
            'presented_grid' => &$presentedGrid,
        ]);

        return $presentedGrid;
    }

    /**
     * Returns the columns formatted as array, adds an additional position handle
     * column when needed.
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    protected function getColumns(GridInterface $grid)
    {
        $columns = $grid->getDefinition()->getColumns()->toArray();

        /** @var PositionColumn|null $positionColumn */
        $positionColumn = $this->getPositionColumn($grid);
        if (null !== $positionColumn) {
            $searchCriteria = $grid->getSearchCriteria();
            $requiredFilter = $positionColumn->getOption('required_filter');

            // If the required filter is not set the position column is not displayed
            if (null !== $requiredFilter && empty($searchCriteria->getFilters()[$requiredFilter])) {
                $columns = array_filter($columns, function (array $column) use ($positionColumn) {
                    return $column['id'] !== $positionColumn->getId();
                });
            } elseif (strtolower($positionColumn->getId()) == strtolower($searchCriteria->getOrderBy())) {
                array_unshift($columns, [
                    'id' => $positionColumn->getId() . '_handle',
                    'name' => $positionColumn->getName(),
                    'type' => 'position_handle',
                    'options' => $positionColumn->getOptions(),
                ]);
            }
        }

        return $columns;
    }

    /**
     * Get filters that have associated columns.
     *
     * @param GridDefinitionInterface $definition
     *
     * @return array
     */
    protected function getColumnFilters(GridDefinitionInterface $definition)
    {
        $columnFiltersMapping = [];

        /** @var FilterInterface $filter */
        foreach ($definition->getFilters()->all() as $filter) {
            if (null !== $associatedColumn = $filter->getAssociatedColumn()) {
                $columnFiltersMapping[$associatedColumn][] = $filter->getName();
            }
        }

        return $columnFiltersMapping;
    }

    /**
     * @param GridInterface $grid
     *
     * @return FormView
     */
    protected function getFilterForm(GridInterface $grid): FormView
    {
        $filterForm = $grid->getFilterForm();

        /** @var PositionColumn|null $positionColumn */
        $positionColumn = $this->getPositionColumn($grid);
        if (null !== $positionColumn) {
            $searchCriteria = $grid->getSearchCriteria();
            $requiredFilter = $positionColumn->getOption('required_filter');
            if (null !== $requiredFilter && empty($searchCriteria->getFilters()[$requiredFilter])) {
                $definition = $grid->getDefinition();

                /** @var FilterInterface $filter */
                foreach ($definition->getFilters()->all() as $filter) {
                    // When position column is not displayed we don't display the filter either
                    if ($filter->getAssociatedColumn() === $positionColumn->getId()) {
                        $filterForm->remove($filter->getName());
                    }
                }
            }
        }

        return $filterForm->createView();
    }

    /**
     * @param GridInterface $grid
     *
     * @return PositionColumn|null
     */
    protected function getPositionColumn(GridInterface $grid)
    {
        /** @var ColumnInterface $column */
        foreach ($grid->getDefinition()->getColumns() as $column) {
            if ($column instanceof PositionColumn) {
                return $column;
            }
        }

        return null;
    }

    /**
     * @param GridInterface $grid
     *
     * @return bool
     */
    private function isEmptyState(GridInterface $grid)
    {
        $filterFormData = $grid->getFilterForm()->getData();
        $dataRecordsTotal = $grid->getData()->getRecordsTotal();
        if (empty($filterFormData) && 0 === $dataRecordsTotal) {
            return true;
        }

        $definitionFiltersKeys = array_keys($grid->getDefinition()->getFilters()->all());
        foreach ($filterFormData as $key => $value) {
            if (in_array($key, $definitionFiltersKeys, true)) {
                return false;
            }
        }

        return 0 === $dataRecordsTotal;
    }
}
