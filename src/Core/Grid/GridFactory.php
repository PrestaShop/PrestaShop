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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\DefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * @var HookDispatcher
     */
    private $dispatcher;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param GridDataProviderInterface $dataProvider
     * @param HookDispatcher $dispatcher
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataProviderInterface $dataProvider,
        HookDispatcher $dispatcher,
        FormFactoryInterface $formFactory
    ) {
        $this->definitionFactory = $definitionFactory;
        $this->dataProvider = $dataProvider;
        $this->dispatcher = $dispatcher;
        $this->formFactory = $formFactory;
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

        $data = $this->dataProvider->getData($searchCriteria);
        $filtersForm = $this->getFiltersForm($definition, $searchCriteria);

        return new Grid(
            $definition,
            $data,
            $searchCriteria,
            $filtersForm
        );
    }

    /**
     * Get presented columns with filter form
     *
     * @param DefinitionInterface $definition
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return FormInterface
     */
    private function getFiltersForm(DefinitionInterface $definition, SearchCriteriaInterface $searchCriteria)
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            $definition->getId(),
            FormType::class,
            $searchCriteria->getFilters()
        );

        /** @var ColumnInterface $column */
        foreach ($definition->getColumns() as $column) {
            $options = $column->getOptions();

            if (isset($options['filter'])) {
                /** @var ColumnFilterOption $columnOption */
                $columnOption = $options['filter'];
                $formBuilder->add(
                    $column->getId(),
                    $columnOption->getFilterType(),
                    $columnOption->getFilterTypeOptions()
                );
            }
        }

        return $formBuilder->getForm();
    }
}
