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

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

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
     * @var GridDataFactoryInterface
     */
    private $dataFactory;

    /**
     * @var FilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param GridDataFactoryInterface      $dataFactory
     * @param FilterFormFactoryInterface     $filterFormFactory
     */
    public function __construct(
        GridDefinitionFactoryInterface $definitionFactory,
        GridDataFactoryInterface $dataFactory,
        FilterFormFactoryInterface $filterFormFactory,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->definitionFactory = $definitionFactory;
        $this->dataFactory = $dataFactory;
        $this->filterFormFactory = $filterFormFactory;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrid(SearchCriteriaInterface $searchCriteria)
    {
        $definition = $this->definitionFactory->getDefinition();

        $this->hookDispatcher->dispatchForParameters('action'.$definition->getId().'GridDefinitionModifier', [
            'definition' => &$definition,
        ]);

        $data = $this->dataFactory->getData($searchCriteria);

        $filterForm = $this->filterFormFactory->create($definition);
        $this->hookDispatcher->dispatchForParameters('action'.$definition->getId().'GridFilterFormModifier', [
            'filterForm' => &$filterForm,
        ]);

        $filterForm->setData($searchCriteria->getFilters());

        return new Grid(
            $definition,
            $data,
            $searchCriteria,
            $filterForm
        );
    }
}
