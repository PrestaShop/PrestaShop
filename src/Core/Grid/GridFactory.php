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

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class GridFactory is responsible for creating final Grid instance
 */
final class GridFactory implements GridFactoryInterface
{
    /**
     * @var GridDefinitionFactoryInterface
     */
    private $definitionFactory;

    /**
     * @var GridDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    public function __construct(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataProviderInterface $dataProvider,
        FormFactoryInterface $formFactory,
        HookDispatcher $dispatcher
    ) {

        $this->definitionFactory = $definitionFactory;
        $this->dataProvider = $dataProvider;
        $this->formFactory = $formFactory;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createUsingSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $definition = $this->definitionFactory->create();

        $this->dispatcher->dispatchForParameters('modifyGridDefinition', [
            'definition' => $definition,
        ]);

        $data = new GridData(
            $this->dataProvider->getRows($searchCriteria),
            $this->dataProvider->getRowsTotal()
        );

        $this->dispatcher->dispatchForParameters('modifyGridData', [
            'data' => $data,
            'definition' => $definition,
        ]);

        $filterForm = $this->createFilterFormFromDefinition($definition);
        $filterForm->setData($searchCriteria->getFilters());

        $grid = new Grid(
            $definition,
            $data,
            $searchCriteria,
            $filterForm
        );

        return $grid;
    }

    /**
     * Create filter form from grid definition columns
     *
     * @param GridDefinitionInterface $definition
     *
     * @return FormInterface
     */
    private function createFilterFormFromDefinition(GridDefinitionInterface $definition)
    {
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

        $form = $formBuilder->getForm();

        return $form;
    }
}
