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

use Cart;
use CartRule;
use Exception;
use PrestaShop\PrestaShop\Core\Configuration\IniConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\DeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartDeliverySettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductPriceInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteOrderedCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\InvalidGiftMessageException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\MinimalQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\PackOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductCustomizationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\HookableKpiRowFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\CartFilter;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    /**
     * Shows list of carts
     *
     * @param Request $request
     * @param CartFilter $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        CartFilter $filters,
        #[Autowire(service: 'prestashop.core.kpi_row.factory.carts')] HookableKpiRowFactory $cartsKpiFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.cart')] GridFactory $cartGridFactory,
    ): Response {
        $cartGrid = $cartGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_carts_export'),
                    'desc' => $this->trans('Export carts', [], 'Admin.Orderscustomers.Feature'),
                    'icon' => 'cloud_download',
                ],
            ],
            'cartGrid' => $this->presentGrid($cartGrid),
            'cartsKpi' => $cartsKpiFactory->build(),
        ]);
    }

    /**
     * Delete given cart
     *
     * @param int $cartId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteCartAction(
        int $cartId,
        IniConfiguration $iniConfiguration,
    ): RedirectResponse {
        try {
            $this->dispatchCommand(new DeleteCartCommand($cartId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CartException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration)));
        }

        return $this->redirectToRoute('admin_carts_index');
    }

    /**
     * Deletes carts on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteCartAction(
        Request $request,
        IniConfiguration $iniConfiguration,
    ): RedirectResponse {
        $cartIds = $this->getBulkActionIds($request, 'cart_bulk');

        try {
            $this->dispatchCommand(new BulkDeleteCartCommand($cartIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration)));
        }

        return $this->redirectToRoute('admin_carts_index');
    }

    /**
     * Export carts in CSV
     *
     * @param Request $request
     * @param CartFilter $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportCartAction(
        Request $request,
        CartFilter $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.cart')] GridFactory $cartGridFactory,
    ): CsvResponse {
        $filters = new CartFilter($filters->getShopConstraint(), ['limit' => null] + $filters->all());
        $cartGrid = $cartGridFactory->getGrid($filters);

        $headers = [
            'id_cart' => $this->trans('ID', [], 'Admin.Global'),
            'id_order' => $this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'),
            'customer_name' => $this->trans('Customer', [], 'Admin.Global'),
            'cart_total' => $this->trans('Total', [], 'Admin.Global'),
            'carrier_name' => $this->trans('Carrier', [], 'Admin.Global'),
            'date_add' => $this->trans('Date', [], 'Admin.Global'),
            'customer_online' => $this->trans('Online', [], 'Admin.Global'),
        ];

        $data = [];

        foreach ($cartGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_cart' => $record['id_cart'],
                'id_order' => $record['id_order'],
                'customer_name' => $record['customer_name'],
                'cart_total' => $record['cart_total'],
                'carrier_name' => $record['carrier_name'],
                'date_add' => $record['date_add'],
                'customer_online' => $record['customer_online_id'] > 0 ? 1 : 0,
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('cart_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param Request $request
     * @param int $cartId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function viewAction(
        Request $request,
        int $cartId,
        #[Autowire(service: 'prestashop.core.kpi_row.factory.cart')] HookableKpiRowFactory $kpiRowFactory,
        IniConfiguration $iniConfiguration
    ) {
        try {
            $cartView = $this->dispatchQuery(new GetCartForViewing($cartId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration)));

            return $this->redirectToRoute('admin_carts_index');
        }

        // Prepare KPI row
        $kpiRowFactory->setOptions([
            'cart_id' => $cartId,
        ]);
        $kpiRow = $kpiRowFactory->build();
        $kpiRow->setAllowRefresh(false);

        return $this->render('@PrestaShop/Admin/Sell/Order/Cart/view.html.twig', [
            'cartView' => $cartView,
            'layoutTitle' => $this->trans('Cart #%s', [$cartId], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'cartKpi' => $kpiRow,
            'createOrderFromCartLink' => $this->generateUrl('admin_orders_create', [
                'cartId' => $cartId,
            ]),
        ]);
    }

    /**
     * Gets requested cart information
     *
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function getInfoAction(
        int $cartId,
        IniConfiguration $iniConfiguration
    ) {
        try {
            $cartInfo = $this->dispatchQuery(
                (new GetCartForOrderCreation($cartId))
                    ->setHideDiscounts(true)
            );

            return $this->json($cartInfo);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Creates empty cart
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function createAction(
        Request $request,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $customerId = $request->request->getInt('customerId');
            $cartId = $this->dispatchCommand(new CreateEmptyCustomerCartCommand($customerId))->getValue();

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Changes the cart address information
     *
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editAddressesAction(
        int $cartId,
        Request $request,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        $invoiceAddressId = $request->request->getInt('invoiceAddressId');
        $deliveryAddressId = $request->request->getInt('deliveryAddressId');

        try {
            $this->dispatchCommand(new UpdateCartAddressesCommand(
                $cartId,
                $deliveryAddressId,
                $invoiceAddressId
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editCurrencyAction(
        int $cartId,
        Request $request,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $this->dispatchCommand(new UpdateCartCurrencyCommand(
                $cartId,
                $request->request->getInt('currencyId')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param int $cartId
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editLanguageAction(
        int $cartId,
        Request $request,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $this->dispatchCommand(new UpdateCartLanguageCommand(
                $cartId,
                $request->request->getInt('languageId')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editCarrierAction(
        Request $request,
        int $cartId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $carrierId = (int) $request->request->get('carrierId');
            $this->dispatchCommand(new UpdateCartCarrierCommand(
                $cartId,
                $carrierId
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function updateDeliverySettingsAction(
        Request $request,
        int $cartId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        $configuration = $this->getConfiguration();
        $recycledPackagingEnabled = (bool) $configuration->get('PS_RECYCLABLE_PACK');
        $giftSettingsEnabled = (bool) $configuration->get('PS_GIFT_WRAPPING');

        try {
            $this->dispatchCommand(new UpdateCartDeliverySettingsCommand(
                $cartId,
                $request->request->getBoolean('freeShipping'),
                $giftSettingsEnabled ? $request->request->getBoolean('isAGift') : null,
                $recycledPackagingEnabled ? $request->request->getBoolean('useRecycledPackaging', false) : null,
                $request->request->get('giftMessage')
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Adds cart rule to cart
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function addCartRuleAction(
        Request $request,
        int $cartId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        $cartRuleId = $request->request->getInt('cartRuleId');
        try {
            $this->dispatchCommand(new AddCartRuleToCartCommand($cartId, $cartRuleId));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deletes cart rule from cart
     *
     * @param int $cartId
     * @param int $cartRuleId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function deleteCartRuleAction(
        int $cartId,
        int $cartRuleId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $this->dispatchCommand(new RemoveCartRuleFromCartCommand($cartId, $cartRuleId));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Adds product to cart
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function addProductAction(
        Request $request,
        int $cartId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        $productId = $request->request->getInt('product_id');
        $quantity = $request->request->getInt('product_quantity');
        $combinationId = $request->request->getInt('combination_id');

        $textCustomizations = $request->request->all('customizations') ?: [];
        $fileCustomizations = $request->files->all('customizations') ?: [];

        $customizations = $textCustomizations + $fileCustomizations;

        try {
            $this->assertAllUploadedFilesReachedRequest($request->headers->get('file-sizes'), $fileCustomizations);

            $this->dispatchCommand(new AddProductToCartCommand(
                $cartId,
                $productId,
                $quantity,
                $combinationId ?: null,
                $customizations
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                $this->getErrorCode($e)
            );
        }
    }

    /**
     * Modifying a price for a product in the cart is actually performed by using generated specific prices,
     * that are used only for this cart and this product.
     *
     * @param Request $request
     * @param int $cartId
     * @param int $productId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editProductPriceAction(
        Request $request,
        int $cartId,
        int $productId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $addSpecificPriceCommand = new UpdateProductPriceInCartCommand(
                $cartId,
                $productId,
                $request->query->getInt('productAttributeId'),
                (float) $request->request->get('newPrice')
            );

            // add new specific price
            $this->dispatchCommand($addSpecificPriceCommand);

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Changes product in cart quantity
     *
     * @param Request $request
     * @param int $cartId
     * @param int $productId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function editProductQuantityAction(
        Request $request,
        int $cartId,
        int $productId,
        IniConfiguration $iniConfiguration
    ) {
        try {
            $newQty = $request->request->getInt('newQty');
            $attributeId = $request->request->getInt('attributeId');

            $giftedQuantity = $this->getProductGiftedQuantity($cartId, $productId, $attributeId);

            $this->dispatchCommand(new UpdateProductQuantityInCartCommand(
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
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deletes product from cart
     *
     * @param Request $request
     * @param int $cartId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function deleteProductAction(
        Request $request,
        int $cartId,
        IniConfiguration $iniConfiguration
    ): JsonResponse {
        try {
            $productId = $request->request->getInt('productId');
            $attributeId = $request->request->getInt('attributeId');
            $customizationId = $request->request->getInt('customizationId');

            $this->dispatchCommand(new RemoveProductFromCartCommand(
                $cartId,
                $productId,
                $attributeId ?: null,
                $customizationId ?: null
            ));

            return $this->json($this->getCartInfo($cartId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e, $iniConfiguration))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param int $cartId
     *
     * @return CartForOrderCreation
     *
     * @throws CartConstraintException
     */
    private function getCartInfo(int $cartId): CartForOrderCreation
    {
        return $this->dispatchQuery(
            (new GetCartForOrderCreation($cartId))
                ->setHideDiscounts(true)
        );
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
    private function getErrorMessages(
        Exception $e,
        IniConfiguration $iniConfig
    ) {
        $minimalQuantity = $e instanceof MinimalQuantityException ? $e->getMinimalQuantity() : 0;

        return [
            CartNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CartRuleValidityException::class => $e->getMessage(),
            CartConstraintException::class => [
                CartConstraintException::INVALID_QUANTITY => $this->trans(
                    'Positive product quantity is required.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CartConstraintException::UNCHANGED_QUANTITY => $this->trans(
                    'Same product quantity is already in cart',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            LanguageException::class => [
                LanguageException::NOT_ACTIVE => $this->trans(
                    'Selected language cannot be used because it is disabled',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CurrencyException::class => [
                CurrencyException::IS_DELETED => $this->trans(
                    'Selected currency cannot be used because it is deleted.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CurrencyException::IS_DISABLED => $this->trans(
                    'Selected currency cannot be used because it is disabled.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CustomizationConstraintException::class => [
                CustomizationConstraintException::FIELD_IS_REQUIRED => $this->trans(
                    'Please fill in all the required fields.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CustomizationConstraintException::FIELD_IS_TOO_LONG => $this->trans(
                    'Custom field text cannot be longer than %limit% characters.',
                    ['%limit%' => CustomizationSettings::MAX_TEXT_LENGTH],
                    'Admin.Notifications.Error'
                ),
            ],
            ProductCustomizationNotFoundException::class => $this->trans(
                'Product customization could not be found. Go to Catalog > Products to customize the product.',
                [],
                'Admin.Catalog.Notification'
            ),
            PackOutOfStockException::class => $this->trans(
                'There are not enough products in stock.',
                [],
                'Admin.Catalog.Notification'
            ),
            ProductOutOfStockException::class => $this->trans(
                'There are not enough products in stock.',
                [],
                'Admin.Catalog.Notification'
            ),
            FileUploadException::class => [
                UPLOAD_ERR_INI_SIZE => $this->trans(
                    'Max file size allowed is "%s" bytes.',
                    [
                        $iniConfig->getUploadMaxSizeInBytes(),
                    ],
                    'Admin.Notifications.Error'
                ),
                UPLOAD_ERR_EXTENSION => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            InvalidGiftMessageException::class => $this->trans(
                'Gift message not valid',
                [],
                'Admin.Notifications.Error'
            ),
            MinimalQuantityException::class => $this->trans(
                'You must add a minimum quantity of %d',
                [
                    $minimalQuantity,
                ],
                'Admin.Orderscustomers.Notification',
            ),
            CannotDeleteCartException::class => $this->trans(
                'Invalid selection',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteOrderedCartException::class => $this->trans(
                'An order has already been placed with this cart.',
                [],
                'Admin.Catalog.Notification'
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
        switch ($e::class) {
            case ProductOutOfStockException::class:
                return Response::HTTP_CONFLICT;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * This method will be removed in the next patch version. We rely on Cart ObjectModel to simplify the code
     * It returns the number of items of the specific product/attribute that are gift for the cart
     *
     * @param int $cartId
     * @param int $productId
     * @param int|null $attributeId
     *
     * @return int
     */
    private function getProductGiftedQuantity(int $cartId, int $productId, ?int $attributeId): int
    {
        $cart = new Cart($cartId);
        $giftCartRules = $cart->getCartRules(CartRule::FILTER_ACTION_GIFT, false);
        if (count($giftCartRules) <= 0) {
            return 0;
        }

        $giftedQuantity = 0;
        foreach ($giftCartRules as $giftCartRule) {
            if (
                $productId == $giftCartRule['gift_product']
                && (null === $attributeId || $attributeId == $giftCartRule['gift_product_attribute'])
            ) {
                ++$giftedQuantity;
            }
        }

        return $giftedQuantity;
    }
}
