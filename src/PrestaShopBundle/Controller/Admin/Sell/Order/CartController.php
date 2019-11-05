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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SetFreeShippingToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\QuantityAction;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_carts_index');
        }

        $kpiRowFactory = $this->get('prestashop.core.kpi_row.factory.cart');
        $kpiRowFactory->setOptions([
            'cart_id' => $cartId,
        ]);

        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/view.html.twig', [
            'cartView' => $cartView,
            'layoutTitle' => $this->trans('View', 'Admin.Actions'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'cartKpi' => $kpiRowFactory->build(),
        ]);
    }

    /**
     * Gets requested cart information
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function getInfoAction(int $cartId)
    {
        try {
            $cartInfo = $this->getQueryBus()->handle(new GetCartInformation($cartId));

            return $this->json($cartInfo);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Creates empty cart
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        try {
            $customerId = $request->request->getInt('customerId');
            $cartId = $this->getCommandBus()->handle(new CreateEmptyCustomerCartCommand($customerId))->getValue();

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Changes the cart address information
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editAddressesAction(int $cartId, Request $request): JsonResponse
    {
        try {
            $updateAddressCommand = new UpdateCartAddressesCommand($cartId);
            if ($deliveryAddressId = $request->request->getInt('deliveryAddressId')) {
                $updateAddressCommand->setNewDeliveryAddressId($deliveryAddressId);
            }

            if ($invoiceAddressId = $request->request->getInt('invoiceAddressId')) {
                $updateAddressCommand->setNewInvoiceAddressId($invoiceAddressId);
            }

            $this->getCommandBus()->handle($updateAddressCommand);

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editCurrencyAction(int $cartId, Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new UpdateCartCurrencyCommand(
                $cartId,
                $request->request->getInt('currencyId')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editLanguageAction(int $cartId, Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new UpdateCartLanguageCommand(
                $cartId,
                $request->request->getInt('languageId')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function editCarrierAction(Request $request, int $cartId): JsonResponse
    {
        try {
            $carrierId = (int) $request->request->get('carrierId');
            $this->getCommandBus()->handle(new UpdateCartCarrierCommand(
                $cartId,
                $carrierId
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function setFreeShippingAction(Request $request, int $cartId)
    {
        try {
            $this->getCommandBus()->handle(new SetFreeShippingToCartCommand(
                $cartId,
                $request->request->getBoolean('freeShipping')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Adds cart rule to cart
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function addCartRuleAction(Request $request, int $cartId): JsonResponse
    {
        $cartRuleId = $request->request->getInt('cartRuleId');
        try {
            $this->getCommandBus()->handle(new AddCartRuleToCartCommand($cartId, $cartRuleId));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deletes cart rule from cart
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $cartId
     * @param int $cartRuleId
     *
     * @return JsonResponse
     */
    public function deleteCartRuleAction(int $cartId, int $cartRuleId)
    {
        try {
            $this->getCommandBus()->handle(new RemoveCartRuleFromCartCommand($cartId, $cartRuleId));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Adds product to cart
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function addProductAction(Request $request, int $cartId): JsonResponse
    {
        $productId = $request->request->getInt('productId');
        $quantity = $request->request->getInt('quantity');
        $combinationId = $request->request->getInt('combinationId');
        $customizationId = null;

        try {
            if ($customizations = $request->request->get('customizations')) {
                $customizationId = $this->getCommandBus()->handle(new AddCustomizationFieldsCommand(
                    $cartId,
                    $productId,
                    $customizations
                ));
            }

            $this->getCommandBus()->handle(new UpdateProductQuantityInCartCommand(
                $cartId,
                (int) $productId,
                (int) $quantity,
                QuantityAction::INCREASE_PRODUCT_QUANTITY,
                $combinationId ?: null,
                $customizationId
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deletes product from cart
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    public function deleteProductAction(Request $request, int $cartId): JsonResponse
    {
        try {
            $productId = $request->request->getInt('productId');
            $attributeId = $request->request->getInt('attributeId');
            $customizationId = $request->request->getInt('customizationId');

            $this->getCommandBus()->handle(new RemoveProductFromCartCommand(
                $cartId,
                $productId,
                $attributeId ?: null,
                $customizationId ?: null
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param int $cartId
     *
     * @return CartInformation
     *
     * @throws CartConstraintException
     */
    private function getCartInfo(int $cartId): CartInformation
    {
        return $this->getQueryBus()->handle(new GetCartInformation($cartId));
    }

    /**
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        return [
            CartNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            CartRuleValidityException::class => $e->getMessage(),
        ];
    }
}
