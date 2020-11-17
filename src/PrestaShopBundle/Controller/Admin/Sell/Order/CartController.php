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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartDeliverySettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\InvalidGiftMessageException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\MinimalQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command\DeleteSpecificPriceByCartProductCommand;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends FrameworkBundleAdminController
{
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

            return $this->redirect($this->getAdminLink('AdminCarts', [], true));
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
            'createOrderFromCartLink' => $this->generateUrl('admin_orders_create', [
                'cartId' => $cartId,
            ]),
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
            $cartInfo = $this->getQueryBus()->handle(new GetCartInformation($cartId, true));

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
        $invoiceAddressId = $request->request->getInt('invoiceAddressId');
        $deliveryAddressId = $request->request->getInt('deliveryAddressId');

        try {
            $this->getCommandBus()->handle(new UpdateCartAddressesCommand(
                $cartId,
                $deliveryAddressId,
                $invoiceAddressId
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
    public function updateDeliverySettingsAction(Request $request, int $cartId)
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $recycledPackagingEnabled = (bool) $configuration->get('PS_RECYCLABLE_PACK');
        $giftSettingsEnabled = (bool) $configuration->get('PS_GIFT_WRAPPING');

        try {
            $this->getCommandBus()->handle(new UpdateCartDeliverySettingsCommand(
                $cartId,
                $request->request->getBoolean('freeShipping'),
                ($giftSettingsEnabled ? $request->request->getBoolean('isAGift', null) : null),
                ($recycledPackagingEnabled ? $request->request->getBoolean('useRecycledPackaging', null) : null),
                ((!empty($request->request->get('giftMessage', null))) ? $request->request->get('giftMessage', null) : null)
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
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
        $productId = $request->request->getInt('product_id');
        $quantity = $request->request->getInt('product_quantity');
        $combinationId = $request->request->getInt('combination_id');

        $textCustomizations = $request->request->get('customizations') ?: [];
        $fileCustomizations = $request->files->get('customizations') ?: [];

        $customizations = $textCustomizations + $fileCustomizations;

        try {
            $this->assertAllUploadedFilesReachedRequest($request->headers->get('file-sizes'), $fileCustomizations);

            $this->getCommandBus()->handle(new AddProductToCartCommand(
                $cartId,
                $productId,
                $quantity,
                $combinationId ?: null,
                $customizations
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                $this->getErrorCode($e)
            );
        }
    }

    /**
     * Modifying a price for a product in the cart is actually performed by using generated specific prices,
     * that are used only for this cart and this product.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function editProductPriceAction(Request $request, int $cartId, int $productId): JsonResponse
    {
        $commandBus = $this->getCommandBus();

        try {
            $deleteSpecificPriceCommand = new DeleteSpecificPriceByCartProductCommand($cartId, $productId);

            $addSpecificPriceCommand = new AddSpecificPriceCommand(
                $productId,
                Reduction::TYPE_AMOUNT,
                0,
                true,
                (float) $request->request->get('newPrice'),
                1
            );
            $addSpecificPriceCommand->setCartId($cartId);
            $addSpecificPriceCommand->setCustomerId($request->request->getInt('customerId'));

            if ($attributeId = $request->query->getInt('productAttributeId')) {
                $deleteSpecificPriceCommand->setProductAttributeId($attributeId);
                $addSpecificPriceCommand->setProductAttributeId($attributeId);
            }

            // delete previous specific prices
            $commandBus->handle($deleteSpecificPriceCommand);
            // add new specific price
            $commandBus->handle($addSpecificPriceCommand);

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Changes product in cart quantity
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     * @param int $cartId
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function editProductQuantityAction(Request $request, int $cartId, int $productId)
    {
        try {
            $newQty = $request->request->getInt('newQty');
            $attributeId = $request->request->getInt('attributeId');

            $giftedQuantity = $this->getProductGiftedQuantity($cartId, $productId, $attributeId);

            $this->getCommandBus()->handle(new UpdateProductQuantityInCartCommand(
                $cartId,
                $productId,
                $newQty + $giftedQuantity,
                $attributeId ?: null,
                $request->request->getInt('customizationId') ?: null
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            if ($e instanceof CartConstraintException && $e->getCode() === CartConstraintException::UNCHANGED_QUANTITY) {
                return $this->json($this->getCartInfo($cartId));
            }

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
        return $this->getQueryBus()->handle(new GetCartInformation($cartId, true));
    }

    /**
     * Checks if all submitted files reached the request.
     * If submitted form size exceeds php.ini post_max_size setting the $_FILES global doesn't contain the file.
     * For this reason custom headers where passed containing submitted file sizes
     *  to check if request contains all files that were submitted in browser
     *
     * @param string $fileSizeHeaders
     * @param array $fileCustomizations
     *
     * @throws FileUploadException
     */
    private function assertAllUploadedFilesReachedRequest(string $fileSizeHeaders, array $fileCustomizations): void
    {
        if (!empty($fileSizeHeaders)) {
            $fileSizesByInputName = json_decode($fileSizeHeaders, true);
            foreach ($fileSizesByInputName as $name => $size) {
                if (!isset($fileCustomizations[$name])) {
                    throw new FileUploadException('Some files were possibly not uploaded due to post_max_size limit', UPLOAD_ERR_INI_SIZE);
                }
            }
        }
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        $iniConfig = $this->get('prestashop.core.configuration.ini_configuration');
        $minimalQuantity = $e instanceof MinimalQuantityException ? $e->getMinimalQuantity() : 0;

        return [
            CartNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            CartRuleValidityException::class => $e->getMessage(),
            CartConstraintException::class => [
                CartConstraintException::INVALID_QUANTITY => $this->trans(
                    'Positive product quantity is required.',
                    'Admin.Notifications.Error'
                ),
                CartConstraintException::UNCHANGED_QUANTITY => $this->trans(
                    'Same product quantity is already in cart',
                    'Admin.Notifications.Error'
                ),
            ],
            LanguageException::class => [
                LanguageException::NOT_ACTIVE => $this->trans(
                    'Selected language cannot be used because it is disabled',
                    'Admin.Notifications.Error'
                ),
            ],
            CurrencyException::class => [
                CurrencyException::IS_DELETED => $this->trans(
                    'Selected currency cannot be used because it is deleted.',
                    'Admin.Notifications.Error'
                ),
                CurrencyException::IS_DISABLED => $this->trans(
                    'Selected currency cannot be used because it is disabled.',
                    'Admin.Notifications.Error'
                ),
            ],
            CustomizationConstraintException::class => [
                CustomizationConstraintException::FIELD_IS_REQUIRED => $this->trans(
                    'Please fill in all the required fields.',
                    'Admin.Notifications.Error'
                ),
                CustomizationConstraintException::FIELD_IS_TOO_LONG => $this->trans(
                    'Custom field text cannot be longer than %limit% characters.',
                    'Admin.Notifications.Error',
                    ['%limit%' => CustomizationSettings::MAX_TEXT_LENGTH]
                ),
            ],
            ProductOutOfStockException::class => $this->trans(
                'There are not enough products in stock.',
                'Admin.Catalog.Notification'
            ),
            FileUploadException::class => [
                UPLOAD_ERR_INI_SIZE => $this->trans(
                    'Max file size allowed is "%s" bytes.', 'Admin.Notifications.Error', [
                    $iniConfig->getUploadMaxSizeInBytes(),
                ]),
                UPLOAD_ERR_EXTENSION => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    'Admin.Notifications.Error'
                ),
            ],
            InvalidGiftMessageException::class => $this->trans(
                'Gift message not valid',
                'Admin.Notifications.Error'
            ),
            MinimalQuantityException::class => $this->trans(
                'You must add a minimum quantity of %d',
                'Admin.Orderscustomers.Notification',
                [
                    $minimalQuantity,
                ]
            ),
        ];
    }

    /**
     * @param Exception $e
     *
     * @return int
     */
    private function getErrorCode(Exception $e): int
    {
        switch (get_class($e)) {
            case ProductOutOfStockException::class:
                return Response::HTTP_CONFLICT;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * This method will be removed in the next patch version. We rely on Cart ObjectModel to simplify the code
     *
     * @param int $cartId
     * @param int $productId
     * @param int|null $attributeId
     *
     * @return int
     */
    private function getProductGiftedQuantity(int $cartId, int $productId, ?int $attributeId): int
    {
        $cart = new \Cart($cartId);
        $giftCartRules = $cart->getCartRules(\CartRule::FILTER_ACTION_GIFT, false);
        if (count($giftCartRules) <= 0) {
            return 0;
        }

        $giftedQuantity = 0;
        foreach ($giftCartRules as $giftCartRule) {
            if (
                $productId == $giftCartRule['gift_product'] &&
                (null === $attributeId || $attributeId == $giftCartRule['gift_product_attribute'])
            ) {
                ++$giftedQuantity;
            }
        }

        return $giftedQuantity;
    }
}
