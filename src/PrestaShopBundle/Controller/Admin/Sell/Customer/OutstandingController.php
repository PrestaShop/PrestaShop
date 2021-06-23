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

namespace PrestaShopBundle\Controller\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OutstandingGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\OutstandingFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OutstandingController manages "Sell > Customers > Outstandings" page.
 */
class OutstandingController extends FrameworkBundleAdminController
{
    /**
     * Show list of outstandings.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param OutstandingFilters $filters
     */
    public function indexAction(Request $request, OutstandingFilters $filters)
    {
        $grid = $this->get('prestashop.core.grid.factory.outstanding')->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Sell/Outstanding/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [],
                'outstandingGrid' => $this->presentGrid($grid),
                'layoutTitle' => $this->trans('Outstanding', 'Admin.Navigation.Menu'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            ]
        );
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_outstanding_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.outstanding'),
            $request,
            OutstandingGridDefinitionFactory::GRID_ID,
            'admin_outstanding_index'
        );
    }
}
