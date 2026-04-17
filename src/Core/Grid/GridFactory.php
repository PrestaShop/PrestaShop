<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        protected readonly GridDefinitionFactoryInterface $definitionFactory,
        protected readonly GridDataFactoryInterface $dataFactory,
        protected readonly GridFilterFormFactoryInterface $filterFormFactory,
        protected readonly HookDispatcherInterface $hookDispatcher
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
