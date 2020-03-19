<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use Exception;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CannotSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CustomerMessageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderDeliveryAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendProcessOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\NegativePaymentAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\TransistEmailSendingException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\UpdateInvoiceNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderConstraints;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShop\PrestaShop\Core\Order\OrderSiblingProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\CommonController;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddOrderCartRuleType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddProductRowType;
use PrestaShopBundle\Form\Admin\Sell\Order\CartSummaryType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrderAddressType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrderCurrencyType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrdersStatusType;
use PrestaShopBundle\Form\Admin\Sell\Order\EditProductRowType;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderMessageType;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderPaymentType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderShippingType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderStatusType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages "Sell > Orders" page
 */
class OrderController extends CommonController
{
    /**
     * Shows list of orders
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param OrderFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, OrderFilters $filters)
    {
        $orderKpiFactory = $this->get('prestashop.core.kpi_row.factory.orders');
        $orderGrid = $this->get('prestashop.core.grid.factory.order')->getGrid($filters);

        $changeOrderStatusesForm = $this->createForm(ChangeOrdersStatusType::class);

        return $this->render(
            '@PrestaShop/Admin/Sell/Order/Order/index.html.twig',
            [
                'orderGrid' => $this->presentGrid($orderGrid),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'changeOrderStatusesForm' => $changeOrderStatusesForm->createView(),
                'orderKpi' => $orderKpiFactory->build(),
                'layoutHeaderToolbarBtn' => $this->getOrderToolbarButtons(),
            ]
        );
    }

    /**
     * @return array
     */
    private function getOrderToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_orders_create'),
            'desc' => $this->trans('Add new order', 'Admin.Orderscustomers.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Places an order from BO
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function placeAction(Request $request)
    {
        $summaryForm = $this->createForm(CartSummaryType::class);
        $summaryForm->handleRequest($request);

        if ($summaryForm->isSubmitted() && $summaryForm->isValid()) {
            $formData = $summaryForm->getData();
            try {
                $orderId = $this->getCommandBus()->handle(new AddOrderFromBackOfficeCommand(
                    (int) $formData['cart_id'],
                    $this->getContext()->employee->id,
                    $formData['order_message'],
                    $formData['payment_module'],
                    (int) $formData['order_state']
                ));

                return $this->redirectToRoute('admin_orders_view', [
                    'orderId' => $orderId->getValue(),
                ]);
            } catch (Exception $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_orders_create');
    }

    /**
     * Renders create order page.
     * Whole page dynamics are on javascript side.
     * To load specific cart pass cartId to url query params (handled by javascript)
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction()
    {
        /** @var MultistoreContextCheckerInterface $shopContextChecker */
        $shopContextChecker = $this->container->get('prestashop.adapter.shop.context');

        if (!$shopContextChecker->isSingleShopContext()) {
            $this->addFlash('error', $this->trans(
                'You have to select a shop before creating new orders.',
                'Admin.Orderscustomers.Notification'
            ));

            return $this->redirectToRoute('admin_orders_index');
        }

        $summaryForm = $this->createForm(CartSummaryType::class);
        $languages = $this->get('prestashop.core.form.choice_provider.language_by_id')->getChoices();
        $currencies = $this->get('prestashop.core.form.choice_provider.currency_by_id')->getChoices();

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/create.html.twig', [
            'currencies' => $currencies,
            'languages' => $languages,
            'summaryForm' => $summaryForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
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
            $this->get('prestashop.core.grid.definition.factory.order'),
            $request,
            OrderGridDefinitionFactory::GRID_ID,
            'admin_orders_index'
        );
    }

    /**
     * Generates invoice PDF for given order
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     */
    public function generateInvoicePdfAction($orderId)
    {
        $this->get('prestashop.adapter.pdf.order_invoice_pdf_generator')->generatePDF([$orderId]);

        // When using legacy generator,
        // we want to be sure that displaying PDF is the last thing this controller will do
        die();
    }

    /**
     * Generates delivery slip PDF for given order
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     */
    public function generateDeliverySlipPdfAction($orderId)
    {
        $this->get('prestashop.adapter.pdf.delivery_slip_pdf_generator')->generatePDF([$orderId]);

        // When using legacy generator,
        // we want to be sure that displaying PDF is the last thing this controller will do
        die();
    }

    /**
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @return RedirectResponse
     */
    public function changeOrdersStatusAction(Request $request)
    {
        $changeOrdersStatusForm = $this->createForm(ChangeOrdersStatusType::class);
        $changeOrdersStatusForm->handleRequest($request);

        $data = $changeOrdersStatusForm->getData();

        try {
            $this->getCommandBus()->handle(
                new BulkChangeOrderStatusCommand($data['order_ids'], (int) $data['new_order_status_id'])
            );

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (ChangeOrderStatusException $e) {
            $this->handleChangeOrderStatusException($e);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_index');
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param OrderFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(OrderFilters $filters)
    {
        $isB2bEnabled = $this->get('prestashop.adapter.legacy.configuration')->get('PS_B2B_ENABLE');

        $orderGrid = $this->get('prestashop.core.grid.factory.order')->getGrid($filters);

        $headers = [
            'id_order' => $this->trans('ID', 'Admin.Global'),
            'reference' => $this->trans('Reference', 'Admin.Global'),
            'new' => $this->trans('New client', 'Admin.Orderscustomers.Feature'),
            'country_name' => $this->trans('Delivery', 'Admin.Global'),
            'customer' => $this->trans('Customer', 'Admin.Global'),
            'total_paid_tax_incl' => $this->trans('Total', 'Admin.Global'),
            'payment' => $this->trans('Payment', 'Admin.Global'),
            'osname' => $this->trans('Status', 'Admin.Global'),
            'date_add' => $this->trans('Date', 'Admin.Global'),
        ];

        if ($isB2bEnabled) {
            $headers['company'] = $this->trans('Company', 'Admin.Global');
        }

        $data = [];

        foreach ($orderGrid->getData()->getRecords()->all() as $record) {
            $item = [
                'id_order' => $record['id_order'],
                'reference' => $record['reference'],
                'new' => $record['new'],
                'country_name' => $record['country_name'],
                'customer' => $record['customer'],
                'total_paid_tax_incl' => $record['total_paid_tax_incl'],
                'payment' => $record['payment'],
                'osname' => $record['osname'],
                'date_add' => $record['date_add'],
            ];

            if ($isB2bEnabled) {
                $item['company'] = $record['company'];
            }

            $data[] = $item;
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('order_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return Response
     */
    public function viewAction(int $orderId, Request $request): Response
    {
        try {
            /** @var OrderForViewing $orderForViewing */
            $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        } catch (OrderException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_orders_index');
        }

        $addOrderCartRuleForm = $this->createForm(AddOrderCartRuleType::class, [], [
            'order_id' => $orderId,
        ]);
        $updateOrderStatusForm = $this->createForm(UpdateOrderStatusType::class, [
            'new_order_status_id' => $orderForViewing->getHistory()->getCurrentOrderStatusId(),
        ]);
        $updateOrderStatusActionBarForm = $this->createForm(UpdateOrderStatusType::class, [
            'new_order_status_id' => $orderForViewing->getHistory()->getCurrentOrderStatusId(),
        ]);
        $addOrderPaymentForm = $this->createForm(OrderPaymentType::class, [
            'id_currency' => $orderForViewing->getCurrencyId(),
        ], [
            'id_order' => $orderId,
        ]);

        $orderMessageForm = $this->createForm(OrderMessageType::class, [], [
            'action' => $this->generateUrl('admin_orders_send_message', ['orderId' => $orderId]),
        ]);
        $orderMessageForm->handleRequest($request);

        $changeOrderCurrencyForm = $this->createForm(ChangeOrderCurrencyType::class, [], [
            'current_currency_id' => $orderForViewing->getCurrencyId(),
        ]);

        $changeOrderAddressForm = null;
        $privateNoteForm = null;

        if (null !== $orderForViewing->getCustomer()) {
            $changeOrderAddressForm = $this->createForm(ChangeOrderAddressType::class, [], [
                'customer_id' => $orderForViewing->getCustomer()->getId(),
            ]);

            $privateNoteForm = $this->createForm(PrivateNoteType::class, [
                'note' => $orderForViewing->getCustomer()->getPrivateNote(),
            ]);
        }

        $updateOrderShippingForm = $this->createForm(UpdateOrderShippingType::class, [
            'new_carrier_id' => $orderForViewing->getCarrierId(),
        ], [
            'order_id' => $orderId,
        ]);

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        $addProductRowForm = $this->createForm(AddProductRowType::class, [], [
            'order_id' => $orderId,
            'symbol' => $orderCurrency->symbol,
        ]);
        $editProductRowForm = $this->createForm(EditProductRowType::class, [], [
            'order_id' => $orderId,
            'symbol' => $orderCurrency->symbol,
        ]);

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $backOfficeOrderButtons = new ActionsBarButtonsCollection();

        try {
            $this->dispatchHook(
                'actionGetAdminOrderButtons', [
                'controller' => $this,
                'id_order' => $orderId,
                'actions_bar_buttons_collection' => $backOfficeOrderButtons,
            ]);

            $cancelProductForm = $formBuilder->getFormFor($orderId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_orders_index');
        }

        $this->handleOutOfStockProduct($orderForViewing);

        $merchandiseReturnEnabled = (bool) $this->configuration->get('PS_ORDER_RETURN');

        /** @var OrderSiblingProviderInterface $orderSiblingProvider */
        $orderSiblingProvider = $this->get('prestashop.adapter.order.order_sibling_provider');

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/view.html.twig', [
            'showContentHeader' => true,
            'orderCurrency' => $orderCurrency,
            'meta_title' => $this->trans('Orders', 'Admin.Orderscustomers.Feature'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'orderForViewing' => $orderForViewing,
            'addOrderCartRuleForm' => $addOrderCartRuleForm->createView(),
            'updateOrderStatusForm' => $updateOrderStatusForm->createView(),
            'updateOrderStatusActionBarForm' => $updateOrderStatusActionBarForm->createView(),
            'addOrderPaymentForm' => $addOrderPaymentForm->createView(),
            'changeOrderCurrencyForm' => $changeOrderCurrencyForm->createView(),
            'privateNoteForm' => $privateNoteForm ? $privateNoteForm->createView() : null,
            'updateOrderShippingForm' => $updateOrderShippingForm->createView(),
            'cancelProductForm' => $cancelProductForm->createView(),
            'invoiceManagementIsEnabled' => $orderForViewing->isInvoiceManagementIsEnabled(),
            'changeOrderAddressForm' => $changeOrderAddressForm ? $changeOrderAddressForm->createView() : null,
            'orderMessageForm' => $orderMessageForm->createView(),
            'addProductRowForm' => $addProductRowForm->createView(),
            'editProductRowForm' => $editProductRowForm->createView(),
            'backOfficeOrderButtons' => $backOfficeOrderButtons,
            'merchandiseReturnEnabled' => $merchandiseReturnEnabled,
            'priceSpecification' => $this->getContextLocale()->getPriceSpecification($orderCurrency->iso_code)->toArray(),
            'previousOrderId' => $orderSiblingProvider->getPreviousOrderId($orderId),
            'nextOrderId' => $orderSiblingProvider->getNextOrderId($orderId),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['update', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function partialRefundAction(int $orderId, Request $request)
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.partial_refund_form_handler');
        $form = $formBuilder->getFormFor($orderId);

        try {
            $form->handleRequest($request);
            $result = $formHandler->handleFor($orderId, $form);
            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('A partial refund was successfully created.', 'Admin.Orderscustomers.Notification'));
                } else {
                    $this->addFlashFormErrors($form);
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /***
     * @AdminSecurity("is_granted(['update', 'delete'], request.get('_legacy_controller'))")
     *
     * @param int $orderId
     * @param Request $request
     * @return RedirectResponse
     */
    public function standardRefundAction(int $orderId, Request $request)
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.standard_refund_form_handler');
        $form = $formBuilder->getFormFor($orderId);

        try {
            $form->handleRequest($request);
            $result = $formHandler->handleFor($orderId, $form);
            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('A standard refund was successfully created.', 'Admin.Orderscustomers.Notification'));
                } else {
                    $this->addFlashFormErrors($form);
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /***
     * @AdminSecurity("is_granted(['update', 'delete'], request.get('_legacy_controller'))")
     *
     * @param int $orderId
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnProductAction(int $orderId, Request $request)
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.return_product_form_handler');
        $form = $formBuilder->getFormFor($orderId);

        try {
            $form->handleRequest($request);
            $result = $formHandler->handleFor($orderId, $form);
            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('A return product was successfully created.', 'Admin.Orderscustomers.Notification'));
                } else {
                    $this->addFlashFormErrors($form);
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param OrderForViewing $orderForViewing
     */
    private function handleOutOfStockProduct(OrderForViewing $orderForViewing)
    {
        $isStockManagementEnabled = $this->configuration->get('PS_STOCK_MANAGEMENT');
        if (!$isStockManagementEnabled || $orderForViewing->isDelivered() || $orderForViewing->isShipped()) {
            return;
        }

        foreach ($orderForViewing->getProducts()->getProducts() as $product) {
            if ($product->getAvailableQuantity() <= 0) {
                $this->addFlash(
                    'warning',
                    $this->trans('This product is out of stock: ', 'Admin.Orderscustomers.Notification') . ' ' . $product->getName()
                );
            }
        }
    }

    /**
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return Response
     */
    public function addProductAction(int $orderId, Request $request): Response
    {
        $invoiceId = (int) $request->get('invoice_id');
        try {
            if ($invoiceId > 0) {
                $addProductCommand = AddProductToOrderCommand::toExistingInvoice(
                    $orderId,
                    $invoiceId,
                    (int) $request->get('product_id'),
                    (int) $request->get('combination_id'),
                    $request->get('price_tax_incl'),
                    $request->get('price_tax_excl'),
                    (int) $request->get('quantity')
                );
            } else {
                $addProductCommand = AddProductToOrderCommand::withNewInvoice(
                    $orderId,
                    (int) $request->get('product_id'),
                    (int) $request->get('combination_id'),
                    $request->get('price_tax_incl'),
                    $request->get('price_tax_excl'),
                    (int) $request->get('quantity'),
                    filter_var($request->get('free_shipping'), FILTER_VALIDATE_BOOLEAN)
                );
            }
            $this->getCommandBus()->handle($addProductCommand);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $products = $orderForViewing->getProducts()->getProducts();
        $lastProduct = $products[array_key_last($products)];

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $cancelProductForm = $formBuilder->getFormFor($orderId);

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig', [
            'orderForViewing' => $orderForViewing,
            'product' => $lastProduct,
            'isColumnLocationDisplayed' => ($lastProduct->getLocation() !== ''),
            'cancelProductForm' => $cancelProductForm->createView(),
            'orderCurrency' => $orderCurrency,
        ]);
    }

    /**
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     */
    public function getInvoicesAction(int $orderId)
    {
        /** @var ConfigurableFormChoiceProviderInterface $choiceProvider */
        $choiceProvider = $this->get('prestashop.adapter.form.choice_provider.order_invoice_by_id');
        $choices = $choiceProvider->getChoices([
            'id_order' => $orderId,
            'id_lang' => $this->getContextLangId(),
            'display_total' => false,
        ]);

        return $this->json([
            'invoices' => $choices,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updateShippingAction(int $orderId, Request $request): RedirectResponse
    {
        $form = $this->createForm(UpdateOrderShippingType::class, [], [
            'order_id' => $orderId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->getCommandBus()->handle(
                    new UpdateOrderShippingDetailsCommand(
                        $orderId,
                        (int) $data['current_order_carrier_id'],
                        (int) $data['new_carrier_id'],
                        $data['tracking_number']
                    )
                );

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            } catch (TransistEmailSendingException $e) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'An error occurred while sending an email to the customer.',
                        'Admin.Orderscustomers.Notification'
                    )
                );
            } catch (Exception $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param int $orderCartRuleId
     *
     * @return RedirectResponse
     */
    public function removeCartRuleAction(int $orderId, int $orderCartRuleId): RedirectResponse
    {
        $this->getCommandBus()->handle(
            new DeleteCartRuleFromOrderCommand($orderId, $orderCartRuleId)
        );

        $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param int $orderInvoiceId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updateInvoiceNoteAction(int $orderId, int $orderInvoiceId, Request $request): RedirectResponse
    {
        $this->getCommandBus()->handle(new UpdateInvoiceNoteCommand(
            $orderInvoiceId,
            $request->request->get('invoice_note')
        ));

        $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     * @param int $orderDetailId
     * @param Request $request
     *
     * @return Response
     */
    public function updateProductAction(int $orderId, int $orderDetailId, Request $request): Response
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateProductInOrderCommand(
                    $orderId,
                    $orderDetailId,
                    $request->get('price_tax_incl'),
                    $request->get('price_tax_excl'),
                    (int) $request->get('quantity'),
                    (int) $request->get('invoice')
                )
            );
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $products = $orderForViewing->getProducts()->getProducts();
        $product = array_reduce($products, function ($result, OrderProductForViewing $item) use ($orderDetailId) {
            return $item->getOrderDetailId() == $orderDetailId ? $item : $result;
        });

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $cancelProductForm = $formBuilder->getFormFor($orderId);

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig', [
            'cancelProductForm' => $cancelProductForm->createView(),
            'isColumnLocationDisplayed' => ($product->getLocation() !== ''),
            'orderCurrency' => $orderCurrency,
            'orderForViewing' => $orderForViewing,
            'product' => $product,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addCartRuleAction(int $orderId, Request $request): RedirectResponse
    {
        $addOrderCartRuleForm = $this->createForm(AddOrderCartRuleType::class, [], [
            'order_id' => $orderId,
        ]);
        $addOrderCartRuleForm->handleRequest($request);

        if ($addOrderCartRuleForm->isSubmitted()) {
            if ($addOrderCartRuleForm->isValid()) {
                $data = $addOrderCartRuleForm->getData();

                $invoiceId = $data['apply_on_all_invoices'] ? null : (int) $data['invoice_id'];

                try {
                    $this->getCommandBus()->handle(
                        new AddCartRuleToOrderCommand(
                            $orderId,
                            $data['name'],
                            $data['type'],
                            $data['value'] ?? null,
                            $invoiceId
                        )
                    );

                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } catch (OrderException $e) {
                    $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
                }
            } else {
                foreach ($addOrderCartRuleForm->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param int $orderId
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @return RedirectResponse
     */
    public function updateStatusAction(int $orderId, Request $request): RedirectResponse
    {
        $form = $this->createForm(UpdateOrderStatusType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleOrderStatusUpdate($orderId, (int) $form->getData()['new_order_status_id']);
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Updates order status directly from list page.
     *
     * @param int $orderId
     * @param Request $request
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @return RedirectResponse
     */
    public function updateStatusFromListAction(int $orderId, Request $request): RedirectResponse
    {
        $this->handleOrderStatusUpdate($orderId, $request->request->getInt('value'));

        return $this->redirectToRoute('admin_orders_index');
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addPaymentAction(int $orderId, Request $request): RedirectResponse
    {
        $form = $this->createForm(OrderPaymentType::class, [], [
            'id_order' => $orderId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $this->getCommandBus()->handle(
                        new AddPaymentCommand(
                            $orderId,
                            $data['date'],
                            $data['payment_method'],
                            $data['amount'],
                            $data['id_currency'],
                            $data['id_invoice'],
                            $data['transaction_id']
                        )
                    );

                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } catch (Exception $e) {
                    $this->addFlash('error', $this->getErrorMessageForException($e, $this->getPaymentErrorMessages($e)));
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $orderId
     *
     * @return JsonResponse
     */
    public function previewAction(int $orderId): JsonResponse
    {
        try {
            /** @var OrderPreview $orderPreview */
            $orderPreview = $this->getQueryBus()->handle(new GetOrderPreview($orderId));

            return $this->json([
                'preview' => $this->renderView('@PrestaShop/Admin/Sell/Order/Order/preview.html.twig', [
                    'orderPreview' => $orderPreview,
                    'productsPreviewLimit' => OrderConstraints::PRODUCTS_PREVIEW_LIMIT,
                    'orderId' => $orderId,
                ]),
            ]);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Duplicates cart from specified order
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param int $orderId
     *
     * @return JsonResponse
     */
    public function duplicateOrderCartAction(int $orderId)
    {
        $cartId = $this->getCommandBus()->handle(new DuplicateOrderCartCommand($orderId))->getValue();

        return $this->json(
            $this->getQueryBus()->handle(new GetCartInformation($cartId))
        );
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"}
     * )
     *
     * @param Request $request
     * @param int $orderId
     *
     * @return Response
     */
    public function sendMessageAction(Request $request, int $orderId): Response
    {
        $orderMessageForm = $this->createForm(OrderMessageType::class);

        $orderMessageForm->handleRequest($request);

        if ($orderMessageForm->isSubmitted() && $orderMessageForm->isValid()) {
            $data = $orderMessageForm->getData();

            try {
                $this->getCommandBus()->handle(new AddOrderCustomerMessageCommand(
                    $orderId,
                    $data['message'],
                    !$data['is_displayed_to_customer']
                ));

                $this->addFlash(
                    'success',
                    $this->trans('Comment successfully added.', 'Admin.Notifications.Success')
                );
            } catch (CannotSendEmailException $exception) {
                $this->addFlash(
                    'success',
                    $this->trans('Comment successfully added.', 'Admin.Notifications.Success')
                );

                $this->addFlash(
                    'error',
                    $this->trans(
                        'An error occurred while sending an email to the customer.',
                        'Admin.Orderscustomers.Notification'
                    )
                );
            } catch (Exception $exception) {
                $this->addFlash(
                    'error',
                    $this->getErrorMessageForException($exception, $this->getCustomerMessageErrorMapping($exception))
                );
            }
        }

        $routesCollection = $this->get('router')->getRouteCollection();

        if (
            null !== $routesCollection &&
            !$orderMessageForm->isValid() &&
            $viewRoute = $routesCollection->get('admin_orders_view')) {
            return $this->forward(
                $viewRoute->getDefault('_controller'),
                [
                    'orderId' => $orderId,
                ],
                $viewRoute->getDefaults()
            );
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function changeCustomerAddressAction(Request $request): RedirectResponse
    {
        $orderId = $request->query->get('orderId');
        if (!$orderId) {
            return $this->redirectToRoute('admin_orders_index');
        }

        $customerId = $request->query->get('customerId');
        if (!$customerId) {
            return $this->redirectToRoute('admin_orders_index');
        }

        $changeOrderAddressForm = $this->createForm(ChangeOrderAddressType::class, [], [
            'customer_id' => (int) $request->query->get('customerId'),
        ]);
        $changeOrderAddressForm->handleRequest($request);

        if (!$changeOrderAddressForm->isSubmitted() || !$changeOrderAddressForm->isValid()) {
            return $this->redirectToRoute('admin_orders_view', [
                'orderId' => $orderId,
            ]);
        }

        $data = $changeOrderAddressForm->getData();

        try {
            if ($data['address_type'] === ChangeOrderAddressType::SHIPPING_TYPE) {
                $command = new ChangeOrderDeliveryAddressCommand((int) $orderId, (int) $data['new_address_id']);
            } else {
                $command = new ChangeOrderInvoiceAddressCommand((int) $orderId, (int) $data['new_address_id']);
            }

            $this->getCommandBus()->handle($command);

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function changeCurrencyAction(int $orderId, Request $request): RedirectResponse
    {
        $changeOrderCurrencyForm = $this->createForm(ChangeOrderCurrencyType::class);
        $changeOrderCurrencyForm->handleRequest($request);

        if (!$changeOrderCurrencyForm->isSubmitted() || !$changeOrderCurrencyForm->isValid()) {
            return $this->redirectToRoute('admin_orders_view', [
                'orderId' => $orderId,
            ]);
        }

        $data = $changeOrderCurrencyForm->getData();

        try {
            $this->getCommandBus()->handle(
                new ChangeOrderCurrencyCommand($orderId, (int) $data['new_currency_id'])
            );

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', 'AdminOrders')",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param int $orderStatusId
     * @param int $orderHistoryId
     *
     * @return RedirectResponse
     */
    public function resendEmailAction(int $orderId, int $orderStatusId, int $orderHistoryId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(
                new ResendOrderEmailCommand($orderId, $orderStatusId, $orderHistoryId)
            );

            $this->addFlash(
                'success',
                $this->trans('The message was successfully sent to the customer.', 'Admin.Orderscustomers.Notification')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     * @param int $orderDetailId
     *
     * @return JsonResponse
     */
    public function deleteProductAction(int $orderId, int $orderDetailId): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(
                new DeleteProductFromOrderCommand($orderId, $orderDetailId)
            );

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return JsonResponse
     */
    public function getPricesAction(int $orderId): JsonResponse
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $this->json([
            'orderTotalFormatted' => $orderForViewing->getPrices()->getTotalAmountFormatted(),
            'productsTotalFormatted' => $orderForViewing->getPrices()->getProductsPriceFormatted(),
            'shippingTotalFormatted' => $orderForViewing->getPrices()->getShippingPriceFormatted(),
            'taxesTotalFormatted' => $orderForViewing->getPrices()->getTaxesAmountFormatted(),
        ]);
    }

    /**
     * Returns products for given order
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPaginatedProductsAction(int $orderId, Request $request): JsonResponse
    {
        $offset = $request->get('offset');
        $limit = $request->get('limit');

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $products = $orderForViewing->getProducts()->getProducts();
        if (null !== $limit && null !== $offset) {
            // @todo: Optimize this by using a GetPartialOrderForViewing query which loads only the relevant products
            $products = array_slice($products, (int) $offset, (int) $limit);
        }

        return $this->json([
            'products' => $products,
        ]);
    }

    /**
     * Generates invoice for given order
     *
     * @param int $orderId
     *
     * @return RedirectResponse
     */
    public function generateInvoiceAction(int $orderId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new GenerateInvoiceCommand($orderId));

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Sends email with process order link to customer
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sendProcessOrderEmailAction(Request $request): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new SendProcessOrderEmailCommand($request->request->getInt('cartId')));

            return $this->json([
                'message' => $this->trans('The email was sent to your customer.', 'Admin.Orderscustomers.Notification'),
            ]);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['update', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_view",
     *     redirectQueryParamsToKeep={"orderId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function cancellationAction(int $orderId, Request $request)
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.cancellation_form_handler');
        $form = $formBuilder->getFormFor($orderId);
        try {
            $form->handleRequest($request);
            $result = $formHandler->handleFor($orderId, $form);
            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Selected products were successfully cancelled.', 'Admin.Catalog.Notification'));
                } else {
                    $this->addFlashFormErrors($form);
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Initializes order status update
     *
     * @param int $orderId
     * @param int $orderStatusId
     */
    private function handleOrderStatusUpdate(int $orderId, int $orderStatusId): void
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateOrderStatusCommand(
                    $orderId,
                    $orderStatusId
                )
            );
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (ChangeOrderStatusException $e) {
            $this->handleChangeOrderStatusException($e);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        $refundableQuantity = 0;
        if ($e instanceof InvalidCancelProductException) {
            $refundableQuantity = $e->getRefundableQuantity();
        }

        return [
            CannotEditDeliveredOrderProductException::class => $this->trans('You cannot edit the cart once the order delivered', 'Admin.Orderscustomers.Notification'),
            OrderNotFoundException::class => $e instanceof OrderNotFoundException ?
                $this->trans(
                    'Order #%d cannot be loaded.',
                    'Admin.Orderscustomers.Notification',
                    ['#%d' => $e->getOrderId()->getValue()]
                ) : '',
            OrderEmailSendException::class => $this->trans(
                'An error occurred while sending the e-mail to the customer.',
                'Admin.Orderscustomers.Notification'
            ),
            OrderException::class => $this->trans(
                $e->getMessage(),
                'Admin.Orderscustomers.Notification'
            ),
            InvalidAmountException::class => $this->trans(
                'Only numbers and decimal points (".") are allowed in the amount fields, e.g. 10.50 or 1050.',
                'Admin.Orderscustomers.Notification'
            ),
            InvalidCancelProductException::class => [
                InvalidCancelProductException::INVALID_QUANTITY => $this->trans(
                    'Please enter a positive quantity.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCancelProductException::QUANTITY_TOO_HIGH => $this->trans(
                    'Please enter a maximum quantity of [1].',
                    'Admin.Orderscustomers.Notification',
                    ['[1]' => $refundableQuantity]
                ),
                InvalidCancelProductException::NO_REFUNDS => $this->trans(
                    'Please select at least one product.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCancelProductException::INVALID_AMOUNT => $this->trans(
                    'Please enter a positive amount.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCancelProductException::NO_GENERATION => $this->trans(
                    'Please generate at least one credit slip or voucher.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],
            ProductOutOfStockException::class => $this->trans(
                'There are not enough products in stock.',
                'Admin.Catalog.Notification'
            ),
            NegativePaymentAmountException::class => $this->trans(
                'Invalid value: the payment must be a positive amount.',
                'Admin.Notifications.Error'
            ),
            InvalidOrderStateException::class => [
                InvalidOrderStateException::ALREADY_PAID => $this->trans(
                    'Invalid action: this order has already been paid.',
                    'Admin.Notifications.Error'
                ),
                InvalidOrderStateException::DELIVERY_NOT_FOUND => $this->trans(
                    'Invalid action: this order has not been delivered.',
                    'Admin.Notifications.Error'
                ),
                InvalidOrderStateException::UNEXPECTED_DELIVERY => $this->trans(
                    'Invalid action: this order has already been delivered.',
                    'Admin.Notifications.Error'
                ),
                InvalidOrderStateException::NOT_PAID => $this->trans(
                    'Invalid action: this order has not been paid.',
                    'Admin.Notifications.Error'
                ),
            ],
            InvalidProductQuantityException::class => $this->trans(
                'Please enter a positive quantity.',
                'Admin.Orderscustomers.Notification'
            ),
        ];
    }

    private function getPaymentErrorMessages(Exception $e)
    {
        return array_merge($this->getErrorMessages($e), [
            InvalidArgumentException::class => $this->trans(
                'Only numbers and decimal points (".") are allowed in the amount fields of the payment block, e.g. 10.50 or 1050.',
                'Admin.Orderscustomers.Notification'
            ),
        ]);
    }

    /**
     * @param ChangeOrderStatusException $e
     */
    private function handleChangeOrderStatusException(ChangeOrderStatusException $e)
    {
        $orderIds = array_merge(
            $e->getOrdersWithFailedToUpdateStatus(),
            $e->getOrdersWithFailedToSendEmail()
        );

        /** @var OrderId $orderId */
        foreach ($orderIds as $orderId) {
            $this->addFlash(
                'error',
                $this->trans(
                    'An error occurred while changing the status for order #%d, or we were unable to send an email to the customer.',
                    'Admin.Orderscustomers.Notification',
                    ['#%d' => $orderId->getValue()]
                )
            );
        }

        foreach ($e->getOrdersWithAssignedStatus() as $orderId) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Order #%d has already been assigned this status.',
                    'Admin.Orderscustomers.Notification',
                    ['#%d' => $orderId->getValue()]
                )
            );
        }
    }

    private function getCustomerMessageErrorMapping(Exception $exception): array
    {
        return [
            OrderNotFoundException::class => $exception instanceof OrderNotFoundException ?
                $this->trans(
                    'Order #%d cannot be loaded',
                    'Admin.Orderscustomers.Notification',
                    ['#%d' => $exception->getOrderId()->getValue()]
                ) : '',
            CustomerMessageConstraintException::class => [
                CustomerMessageConstraintException::MISSING_MESSAGE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf('"%s"', $this->trans('Message', 'Admin.Global')),
                        ]
                    ),
                CustomerMessageConstraintException::INVALID_MESSAGE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf('"%s"', $this->trans('Message', 'Admin.Global')),
                        ]
                    ),
            ],
        ];
    }
}
