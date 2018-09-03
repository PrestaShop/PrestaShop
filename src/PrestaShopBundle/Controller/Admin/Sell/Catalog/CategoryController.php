<?php
/**
 * 2007-2018 PrestaShop.
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController is responsible for "Sell > Catalog > Categories" page.
 */
class CategoryController extends FrameworkBundleAdminController
{
    /**
     * Show categories listing.
     *
     * @param Request $request
     * @param CategoryFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CategoryFilters $filters)
    {
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $categoryGridFactory = $this->get('prestashop.core.grid.factory.category');
        $categoryGrid = $categoryGridFactory->getGrid($filters);

        $categoriesKpiFactory = $this->get('prestashop.core.kpi_row.factory.categories');

        $currentCategoryId = $filters->getFilters()['id_category_parent'];
        $categoryViewDataProvider = $this->get('prestashop.adapter.category.category_view_data_provider');

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Categories/categories.html.twig', [
            'categoriesGrid' => $gridPresenter->present($categoryGrid),
            'categoriesKpi' => $categoriesKpiFactory->build(),
            'layoutHeaderToolbarBtn' => $this->getCategoryToolbarButtons($request),
            'currentCategoryTree' => $categoryViewDataProvider->getTreeView($currentCategoryId),
            'currentCategoryId' => $currentCategoryId,
        ]);
    }

    /**
     * Show category for editing.
     *
     * @param int $categoryId
     *
     * @return Response
     */
    public function editAction($categoryId)
    {
        return $this->redirect(
            $this->getAdminLink('AdminCategories', [
                'id_category' => $categoryId,
                'updatecategory' => 1,
            ])
        );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getCategoryToolbarButtons(Request $request)
    {
        $toolbarButtons = [];

        if ($this->get('prestashop.adapter.feature.multishop')->isActive()) {
            $toolbarButtons['add_root'] = [
                'href' => $this->getAdminLink('AdminCategories', [
                    'addcategoryroot' => 1,
                ]),
                'desc' => $this->trans('Add new root category', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }

        $urlParams = [
            'addcategory' => 1,
        ];

        if ($request->query->has('id_category')) {
            $urlParams['id_parent'] = $request->query->get('id_category');
        }

        $toolbarButtons['add'] = [
            'href' => $this->getAdminLink('AdminCategories', $urlParams),
            'desc' => $this->trans('Add new category', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }
}
