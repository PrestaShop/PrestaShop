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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnProductsFilters;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Gets data for order return products grid
 */
class OrderReturnProductsGridFactory extends GridFactory
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param GridDataFactoryInterface $dataFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataFactoryInterface $dataFactory,
        GridFilterFormFactoryInterface $filterFormFactory,
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
    ) {
        parent::__construct($definitionFactory, $dataFactory, $filterFormFactory, $hookDispatcher);
        $this->translator = $translator;
    }

    public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface
    {
        if (!$searchCriteria instanceof OrderReturnProductsFilters) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria. Expected "%s"', OrderReturnProductsFilters::class));
        }

        /** @var GridDefinition $definition */
        $definition = $this->definitionFactory->getDefinition();
        $this->modifyDefinition($definition, $searchCriteria);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridDefinitionModifier', [
            'definition' => $definition,
        ]);

        $data = $this->dataFactory->getData($searchCriteria);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridDataModifier', [
            'data' => &$data,
        ]);

        $filterForm = $this->filterFormFactory->create($definition);
        $filterForm->setData($searchCriteria->getFilters());

        return new Grid(
            $definition,
            $data,
            $searchCriteria,
            $filterForm
        );
    }

    /**
     * @param GridDefinition $definition
     * @param OrderReturnProductsFilters $searchCriteria
     *
     * @return GridDefinition
     */
    private function modifyDefinition(GridDefinition $definition, OrderReturnProductsFilters $searchCriteria): GridDefinition
    {
        $definition->getFilters()->add(
            (new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => $definition->getId(),
                        'orderReturnId' => $searchCriteria->getOrderReturnId(),
                    ],
                    'redirect_route' => 'admin_order_returns_edit',
                    'redirect_route_params' => [
                        'orderReturnId' => $searchCriteria->getOrderReturnId(),
                    ],
                ])
                ->setAssociatedColumn('actions')
        );

        $definition->getBulkActions()->add(
            (new SubmitBulkAction('delete_selection'))
                ->setName($this->translator->trans('Delete selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_order_returns_delete_product_bulk',
                    'route_params' => [
                        'orderReturnId' => $searchCriteria->getOrderReturnId(),
                    ],
                    'confirm_message' => $this->translator->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                    'modal_options' => new ModalOptions([
                        'title' => $this->translator->trans('Delete selection', [], 'Admin.Actions'),
                        'confirm_button_label' => $this->translator->trans('Delete', [], 'Admin.Actions'),
                        'confirm_button_class' => 'btn-danger',
                    ]),
                ])
        );

        return $definition;
    }
}
