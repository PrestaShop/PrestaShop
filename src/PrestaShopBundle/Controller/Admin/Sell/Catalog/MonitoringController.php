<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring\EmptyCategoryGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\EmptyCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Category\DeleteCategoriesType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Monitoring page
 */
class MonitoringController extends FrameworkBundleAdminController
{
    /**
     * Shows Monitoring listing page
     *
     * @param Request $request
     * @param EmptyCategoryFilters $emptyCategoryFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EmptyCategoryFilters $emptyCategoryFilters
    ) {
        $emptyCategoryGridFactory = $this->get('prestashop.core.grid.factory.monitoring.empty_category');
        $emptyCategoryGrid = $emptyCategoryGridFactory->getGrid($emptyCategoryFilters);
        $deleteCategoriesForm = $this->createForm(DeleteCategoriesType::class);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Monitoring/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'empty_category_grid' => $this->presentGrid($emptyCategoryGrid),
            'deleteCategoriesForm' => $deleteCategoriesForm->createView(),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchEmptyCategoryAction(Request $request)
    {
        $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.monitoring.empty_category';
        $filterId = EmptyCategoryGridDefinitionFactory::GRID_ID;

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'admin_monitoring_index'
        );
    }
}
