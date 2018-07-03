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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\DefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class GridPresenter is responsible for presenting grid
 */
final class GridPresenter implements GridPresenterInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function present(GridInterface $grid)
    {
        $definition = $grid->getDefinition();
        $searchCriteria = $grid->getSearchCriteria();
        $data = $grid->getData();

        list(
            $columns,
            $filterForm
        ) = $this->presentColumns($definition, $grid->getSearchCriteria());

        return [
            'id' => $definition->getId(),
            'name' => $definition->getName(),
            'filter_form' => $filterForm->createView(),
            'columns' => $columns,
            'actions' => [
                'grid' => $definition->getGridActions()->toArray(),
                'bulk' => $definition->getBulkActions()->toArray(),
            ],
            'data' => [
                'rows' => $data->getRows(),
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
     * Get presented columns with filter form
     *
     * @param DefinitionInterface $definition
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    private function presentColumns(
        DefinitionInterface $definition,
        SearchCriteriaInterface $searchCriteria
    ) {
        $formBuilder = $this->formFactory->createNamedBuilder(
            $definition->getId(),
            FormType::class,
            $searchCriteria->getFilters()
        );
        $columnsArray = [];

        /** @var ColumnInterface $column */
        foreach ($definition->getColumns() as $column) {
            $resolver = new OptionsResolver();
            $column->configureOptions($resolver);
            $columnOptions = $resolver->resolve($column->getOptions());

            $columnsArray[] = [
                'id' => $column->getId(),
                'name' => $column->getName(),
                'type' => $column->getType(),
                'options' => $columnOptions,
            ];

            if (isset($columnOptions['filter'])) {
                /** @var ColumnFilterOption $columnOption */
                $columnOption = $columnOptions['filter'];
                $formBuilder->add(
                    $column->getId(),
                    $columnOption->getFilterType(),
                    $columnOption->getFilterTypeOptions()
                );
            }
        }

        return [
            $columnsArray,
            $formBuilder->getForm(),
        ];
    }
}
