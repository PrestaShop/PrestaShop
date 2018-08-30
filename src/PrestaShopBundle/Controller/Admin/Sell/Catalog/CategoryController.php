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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Search\Filters\CategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController is responsible for "Sell > Catalog > Categories" page
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Show categories listing
     *
     * @param CategoryFilters $filters
     *
     * @return Response
     */
    public function indexAction(CategoryFilters $filters)
    {
        $searchCriteriaFactory =
            $this->get('prestashop.adapter.grid.search.factory.search_criteria_with_category_parent_id');
        $searchCriteria = $searchCriteriaFactory->createFrom($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $categoryGridFactory = $this->get('prestashop.core.grid.factory.category');
        $categoryGrid = $categoryGridFactory->getGrid($searchCriteria);

        $categoriesKpiFactory = $this->get('prestashop.core.kpi_row.factory.categories');

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/categories.html.twig', [
            'categoriesGrid' => $gridPresenter->present($categoryGrid),
            'categoriesKpi' => $categoriesKpiFactory->build(),
        ]);
    }
}
