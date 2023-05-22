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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class GridFactory is responsible for creating final Grid instance.
 */
class GridFactory implements GridFactoryInterface
{
    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param GridDataFactoryInterface $dataFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        protected GridDefinitionFactoryInterface $definitionFactory,
        protected GridDataFactoryInterface $dataFactory,
        protected GridFilterFormFactoryInterface $filterFormFactory,
        protected HookDispatcherInterface $hookDispatcher
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface
    {
        $definition = $this->definitionFactory->getDefinition();
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
}
