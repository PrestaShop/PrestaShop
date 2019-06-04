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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends FrameworkBundleAdminController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/index.html.twig');
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return Response
     */
    public function viewAction(Request $request, $cartId)
    {
        try {
            $cartView = $this->getQueryBus()->handle(new GetCartForViewing((int) $cartId));
        } catch (CartNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_carts_index');
        }

        $cartKpi = $this->get('prestashop.core.kpi_row.factory.cart')->build([
            'cart_id' => $cartId,
        ]);

        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/view.html.twig', [
            'cartView' => $cartView,
            'layoutTitle' => $this->trans('View', 'Admin.Actions'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'cartKpi' => $cartKpi,
        ]);
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CartNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];
    }
}
