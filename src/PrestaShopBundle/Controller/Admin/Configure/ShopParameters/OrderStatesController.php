<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderReturnStatesGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderStatesGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnStatesFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderStatesFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Configure > Shop Parameters > Order states Settings" page.
 */
class OrderStatesController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        OrderStatesFilters $orderStatesFilters,
        OrderReturnStatesFilters $orderReturnStatesFilters
    ) {
        $orderStatesGridFactory = $this->get('prestashop.core.grid.factory.order_states');
        $orderStatesGrid = $orderStatesGridFactory->getGrid($orderStatesFilters);

        $orderReturnStatesGridFactory = $this->get('prestashop.core.grid.factory.order_return_states');
        $orderReturnStatesGrid = $orderReturnStatesGridFactory->getGrid($orderReturnStatesFilters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'orderStatesGrid' => $this->presentGrid($orderStatesGrid),
            'orderReturnStatesGrid' => $this->presentGrid($orderReturnStatesGrid),
        ]);
    }

    /**
     * Process Grid search.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchGridAction(Request $request)
    {
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.order_states';

        $filterId = OrderStatesGridDefinitionFactory::GRID_ID;
        if ($request->request->has(OrderReturnStatesGridDefinitionFactory::GRID_ID)) {
            $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.order_return_states';
            $filterId = OrderReturnStatesGridDefinitionFactory::GRID_ID;
        }

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'admin_order_states'
        );
    }
}
