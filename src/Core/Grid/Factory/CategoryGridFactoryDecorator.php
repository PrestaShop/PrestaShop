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

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Decorates Category grid factory
 */
final class CategoryGridFactoryDecorator implements GridFactoryInterface
{
    /**
     * @var GridFactoryInterface
     */
    private $categoryGridFactory;

    /**
     * @var GridFilterFormFactoryInterface
     */
    private $filterFormFactory;

    /**
     * @param GridFactoryInterface $categoryGridFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory optional
     *
     * $filterFormFactory is optional in order to comply with SemVer
     */
    public function __construct(
        GridFactoryInterface $categoryGridFactory,
        GridFilterFormFactoryInterface $filterFormFactory = null
    ) {
        $this->categoryGridFactory = $categoryGridFactory;
        $this->filterFormFactory = $filterFormFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrid(SearchCriteriaInterface $searchCriteria, array $options = [])
    {
        $categoryGrid = $this->categoryGridFactory->getGrid($searchCriteria);

        $this->removePositionDragColumnIfEligible($searchCriteria, $categoryGrid);

        $filters = $searchCriteria->getFilters();
        if ($this->isHomeCategory($filters) && ($this->filterFormFactory !== null)) {
            return $categoryGrid;
        }

        $this->injectCategoryIdIntoSearchTypeOptions($categoryGrid, $filters);

        $filterForm = $this->rebuildFilterForm($searchCriteria, $categoryGrid);

        return new Grid(
            $categoryGrid->getDefinition(),
            $categoryGrid->getData(),
            $searchCriteria,
            $filterForm
        );
    }

    /**
     * @param array $filters
     *
     * @return bool
     */
    private function isHomeCategory(array $filters)
    {
        return isset($filters['is_home_category'])
            && $filters['is_home_category'] === true;
    }

    /**
     * Position can only be changed when grid is
     * ordered by "position" in "asc" way.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param GridInterface $categoryGrid
     */
    private function removePositionDragColumnIfEligible(
        SearchCriteriaInterface $searchCriteria,
        $categoryGrid
    ) {
        if ('position' !== $searchCriteria->getOrderBy() ||
            'asc' !== $searchCriteria->getOrderWay()) {
            $categoryGrid->getDefinition()
                ->getColumns()
                ->remove('position_drag');
        }
    }

    /**
     * @param GridInterface $categoryGrid
     * @param array $filters
     */
    private function injectCategoryIdIntoSearchTypeOptions($categoryGrid, array $filters)
    {
        /** @var FilterCollection $filtersCollection */
        $filtersCollection = $categoryGrid->getDefinition()->getFilters();
        /** @var FilterInterface $actionsFilter */
        $actionsFilter = $filtersCollection->get('actions');

        $typeOptions = $actionsFilter->getTypeOptions();
        $typeOptions['redirect_route_params'] = ['categoryId' => $filters['id_category_parent']];
        $actionsFilter->setTypeOptions($typeOptions);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param GridInterface $categoryGrid
     *
     * @return FormInterface
     */
    private function rebuildFilterForm(
        SearchCriteriaInterface $searchCriteria,
        GridInterface $categoryGrid)
    {
        $filterForm = $this->filterFormFactory->create($categoryGrid->getDefinition());
        $filterForm->setData($searchCriteria->getFilters());

        return $filterForm;
    }
}
