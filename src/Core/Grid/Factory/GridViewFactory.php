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
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\MissingColumnInRowException;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\View\GridView;
use PrestaShop\PrestaShop\Core\Grid\View\ColumnView;
use PrestaShop\PrestaShop\Core\Grid\View\RowActionView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

/**
 * Class GridViewFactory is responsible for creating grid view data that is passed to template for rendering
 */
final class GridViewFactory implements GridViewFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createView(Grid $grid)
    {
        $gridView = new GridView(
            $grid->getDefinition()->getIdentifier(),
            $grid->getDefinition()->getName(),
            $this->createColumnsView($grid),
            $this->createRowsView($grid),
            $grid->getData()->getRowsTotal(),
            $this->createFilterFormView($grid)
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
    private function createRowsView(Grid $grid)
    {
        $definition = $grid->getDefinition();

        $rowActionViews = [];
        foreach ($grid->getData()->getRows() as $row) {
            $rowActions = $this->getRowActions($row, $definition->getRowActions());
            $rowData = $this->applyColumnModifications($row, $definition->getColumns());

            $rowActionViews[] = [
                'actions' => $rowActions,
                'data' => $rowData,
            ];
        }

        return $rowActionViews;
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

            $rowActionView = new RowActionView(
                $rowAction->getName(),
                $rowAction->getIcon(),
                $url
            );

            $actions[$rowAction->getIdentifier()] = $rowActionView;
        }

        return $actions;
    }

    /**
     * Some columns may modify data that comes directly from database or any other data source.
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
     * @return array|ColumnView[]
     */
    private function createColumnsView(Grid $grid)
    {
        $columnsView = [];

        foreach ($grid->getDefinition()->getColumns() as $column) {
            $columnsView[] = ColumnView::fromColumn($column);
        }

        return $columnsView;
    }


    /**
     * Builds filters form for grid
     *
     * @param Grid $grid
     *
     * @return FormView
     */
    private function createFilterFormView(Grid $grid)
    {
        $definition = $grid->getDefinition();

        $formBuilder = $this->formFactory->createNamedBuilder($definition->getIdentifier());

        foreach ($definition->getColumns() as $column) {
            if ($formType = $column->getFilterFormType()) {
                $options = $column->getFilterFormTypeOptions();

                if (!isset($options['required'])) {
                    $options['required'] = false;
                }

                $formBuilder->add(
                    $column->getIdentifier(),
                    $formType,
                    $options
                );
            }
        }

        $form = $formBuilder
            ->setData($grid->getSearchParameters()->getFilters())
            ->getForm()
        ;

        return $form->createView();
    }
}
