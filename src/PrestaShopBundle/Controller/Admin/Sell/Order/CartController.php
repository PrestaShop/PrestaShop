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

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\DeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\DeleteCartWithOrderException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CartGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\CartFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages page "Sell > Orders > Shopping Carts"
 */
class CartController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CartFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CartFilters $filters)
    {
        $cartGrid = $this->get('prestashop.core.grid.factory.cart')->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/index.html.twig', [
            'cartGrid' => $this->presentGrid($cartGrid),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_carts_index")
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
            $this->get('prestashop.core.grid.definition.factory.cart'),
            $request,
            CartGridDefinitionFactory::GRID_ID,
            'admin_carts_index'
        );
    }

    /**
     * Deletes given cart
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_carts_index")
     *
     * @param int $cartId
     *
     * @return RedirectResponse
     */
    public function deleteAction($cartId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteCartCommand((int) $cartId));

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CartException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carts_index');
    }

    /**
     * Bulk delete carts
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_carts_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $cartIds = $request->request->get('cart_carts_bulk');
        $cartIds = array_map(static function ($cartId) { return (int) $cartId; }, $cartIds);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCartCommand($cartIds));

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CartException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carts_index');
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            DeleteCartWithOrderException::class =>
                $this->trans('An error occurred during deletion.', 'Admin.Notifications.Error'),
        ];
    }
}
