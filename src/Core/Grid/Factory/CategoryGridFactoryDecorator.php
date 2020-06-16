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

use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

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
     * @param GridFactoryInterface $categoryGridFactory
     */
    public function __construct(GridFactoryInterface $categoryGridFactory)
    {
        $this->categoryGridFactory = $categoryGridFactory;
    }

    /**
     * Position can only be changed when grid is
     * ordered by "position" in "asc" way.
     *
     * {@inheritdoc}
     */
    public function getGrid(SearchCriteriaInterface $searchCriteria)
    {
        $categoryGrid = $this->categoryGridFactory->getGrid($searchCriteria);

        if ('position' !== $searchCriteria->getOrderBy() ||
            'asc' !== $searchCriteria->getOrderWay()) {
            $categoryGrid->getDefinition()
                ->getColumns()
                ->remove('position_drag')
            ;
        }

        return $categoryGrid;
    }
}
