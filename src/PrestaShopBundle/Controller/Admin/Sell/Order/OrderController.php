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

use Currency;
use Exception;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonsCollection;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\InvalidCartRuleDiscountValueException;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CannotSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CustomerMessageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderDeliveryAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendProcessOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SetInternalOrderNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\NegativePaymentAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\TransistEmailSendingException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\UpdateInvoiceNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceException;
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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductSearchEmptyPhraseException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Order\OrderSiblingProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\InvalidModuleException;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddOrderCartRuleType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddProductRowType;
use PrestaShopBundle\Form\Admin\Sell\Order\CartSummaryType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrderAddressType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrderCurrencyType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrdersStatusType;
use PrestaShopBundle\Form\Admin\Sell\Order\EditProductRowType;
use PrestaShopBundle\Form\Admin\Sell\Order\InternalNoteType;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderMessageType;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderPaymentType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderShippingType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderStatusType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages "Sell > Orders" page
 */
class OrderController extends FrameworkBundleAdminController
{
    /**
     * Default number of products per page (in case invalid value is used)
     */
    public const DEFAULT_PRODUCTS_NUMBER = 8;

    /**
     * Options used for the number of products per page
     */
    public const PRODUCTS_PAGINATION_OPTIONS = [8, 20, 50, 100];

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

        $isSingleShopContext = $this->get('prestashop.adapter.shop.context')->isSingleShopContext();

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_orders_create'),
            'desc' => $this->trans('Add new order', 'Admin.Orderscustomers.Feature'),
            'icon' => 'add_circle_outline',
            'disabled' => !$isSingleShopContext,
        ];

        if (!$isSingleShopContext) {
            $toolbarButtons['add']['help'] = $this->trans(
                'You can use this feature in a single shop context only. Switch context to enable it.',
                'Admin.Orderscustomers.Feature'
            );
            $toolbarButtons['add']['href'] = '#';
        }

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
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.cart_summary_form_handler');

        try {
            $result = $formHandler->handle($summaryForm);

            if ($result->getIdentifiableObjectId() instanceof OrderId) {
                /** @var OrderId $orderId */
                $orderId = $result->getIdentifiableObjectId();

                return $this->redirectToRoute('admin_orders_view', [
                    'orderId' => $orderId->getValue(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
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
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        /** @var ShopContext $shopContextChecker */
        $shopContextChecker = $this->container->get('prestashop.adapter.shop.context');

        if (!$shopContextChecker->isSingleShopContext()) {
            $this->addFlash('error', $this->trans(
                'You have to select a shop before creating new orders.',
                'Admin.Orderscustomers.Notification'
            ));

            return $this->redirectToRoute('admin_orders_index');
        }

        $summaryForm = $this->createForm(CartSummaryType::class);
        $languages = $this->get('prestashop.core.form.choice_provider.language_by_id')->getChoices(
            [
                'shop_id' => $shopContextChecker->getContextShopID(),
            ]
        );
        $currencies = $this->get('prestashop.core.form.choice_provider.currency_by_id')->getChoices();

        $configuration = $this->get('prestashop.adapter.legacy.configuration');

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/create.html.twig', [
            'currencies' => $currencies,
            'languages' => $languages,
            'summaryForm' => $summaryForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'recycledPackagingEnabled' => (bool) $configuration->get('PS_RECYCLABLE_PACK'),
            'giftSettingsEnabled' => (bool) $configuration->get('PS_GIFT_WRAPPING'),
            'stockManagementEnabled' => (bool) $configuration->get('PS_STOCK_MANAGEMENT'),
            'isB2BEnabled' => (bool) $configuration->get('PS_B2B_ENABLE'),
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

            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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

        $filters = new OrderFilters(['limit' => null] + $filters->all());
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
            $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));
        } catch (OrderException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_orders_index');
        }

        $formFactory = $this->get('form.factory');
        $updateOrderStatusForm = $formFactory->createNamed(
            'update_order_status',
            UpdateOrderStatusType::class, [
                'new_order_status_id' => $orderForViewing->getHistory()->getCurrentOrderStatusId(),
            ]
        );
        $updateOrderStatusActionBarForm = $formFactory->createNamed(
            'update_order_status_action_bar',
            UpdateOrderStatusType::class, [
                'new_order_status_id' => $orderForViewing->getHistory()->getCurrentOrderStatusId(),
            ]
        );

        $addOrderCartRuleForm = $this->createForm(AddOrderCartRuleType::class, [], [
            'order_id' => $orderId,
        ]);
        $addOrderPaymentForm = $this->createForm(OrderPaymentType::class, [
            'id_currency' => $orderForViewing->getCurrencyId(),
        ], [
            'id_order' => $orderId,
        ]);

        $orderMessageForm = $this->createForm(OrderMessageType::class, [
            'lang_id' => $orderForViewing->getCustomer()->getLanguageId(),
        ], [
            'action' => $this->generateUrl('admin_orders_send_message', ['orderId' => $orderId]),
        ]);
        $orderMessageForm->handleRequest($request);

        $changeOrderCurrencyForm = $this->createForm(ChangeOrderCurrencyType::class, [], [
            'current_currency_id' => $orderForViewing->getCurrencyId(),
        ]);

        $changeOrderAddressForm = null;
        $privateNoteForm = null;

        if (null !== $orderForViewing->getCustomer() && $orderForViewing->getCustomer()->getId() !== 0) {
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
        //@todo: Fix me. Should not rely on legacy object model - Currency
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        $addProductRowForm = $this->createForm(AddProductRowType::class, [], [
            'order_id' => $orderId,
            'currency_id' => $orderForViewing->getCurrencyId(),
            'symbol' => $orderCurrency->symbol,
        ]);
        $editProductRowForm = $this->createForm(EditProductRowType::class, [], [
            'order_id' => $orderId,
            'symbol' => $orderCurrency->symbol,
        ]);

        $internalNoteForm = $this->createForm(InternalNoteType::class, [
            'note' => $orderForViewing->getNote(),
        ]);

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $backOfficeOrderButtons = new ActionsBarButtonsCollection();

        try {
            $this->dispatchHook(
                'actionGetAdminOrderButtons',
                [
                    'controller' => $this,
                    'id_order' => $orderId,
                    'actions_bar_buttons_collection' => $backOfficeOrderButtons,
                ]
            );

            $cancelProductForm = $formBuilder->getFormFor($orderId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_orders_index');
        }

        $this->handleOutOfStockProduct($orderForViewing);

        $merchandiseReturnEnabled = (bool) $this->configuration->get('PS_ORDER_RETURN');

        /** @var OrderSiblingProviderInterface $orderSiblingProvider */
        $orderSiblingProvider = $this->get('prestashop.adapter.order.order_sibling_provider');

        $paginationNum = (int) $this->configuration->get('PS_ORDER_PRODUCTS_NB_PER_PAGE', self::DEFAULT_PRODUCTS_NUMBER);
        $paginationNumOptions = self::PRODUCTS_PAGINATION_OPTIONS;
        if (!in_array($paginationNum, $paginationNumOptions)) {
            $paginationNumOptions[] = $paginationNum;
        }
        sort($paginationNumOptions);

        $metatitle = sprintf(
            '%s %s %s',
            $this->trans('Orders', 'Admin.Orderscustomers.Feature'),
            $this->configuration->get('PS_NAVIGATION_PIPE', '>'),
            $this->trans(
                'Order %reference% from %firstname% %lastname%',
                'Admin.Orderscustomers.Feature',
                [
                    '%reference%' => $orderForViewing->getReference(),
                    '%firstname%' => $orderForViewing->getCustomer()->getFirstName(),
                    '%lastname%' => $orderForViewing->getCustomer()->getLastName(),
                ]
            )
        );

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/view.html.twig', [
            'showContentHeader' => true,
            'enableSidebar' => true,
            'orderCurrency' => $orderCurrency,
            'meta_title' => $metatitle,
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
            'paginationNum' => $paginationNum,
            'paginationNumOptions' => $paginationNumOptions,
            'isAvailableQuantityDisplayed' => $this->configuration->getBoolean('PS_STOCK_MANAGEMENT'),
            'internalNoteForm' => $internalNoteForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
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

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))"
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
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

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))"
     * )
     *
     * @param int $orderId
     * @param Request $request
     *
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
                    $this->addFlash('success', $this->trans('The product was successfully returned.', 'Admin.Orderscustomers.Notification'));
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
        $isStockManagementEnabled = $this->configuration->getBoolean('PS_STOCK_MANAGEMENT');
        if (!$isStockManagementEnabled || $orderForViewing->isDelivered() || $orderForViewing->isShipped()) {
            return;
        }

        foreach ($orderForViewing->getProducts()->getProducts() as $product) {
            if ($product->getAvailableQuantity() <= 0) {
                $this->addFlash(
                    'warning',
                    $this->trans('This product is out of stock:', 'Admin.Orderscustomers.Notification') . ' ' . $product->getName()
                );
            }
        }
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     * @param Request $request
     *
     * @return Response
     */
    public function addProductAction(int $orderId, Request $request): Response
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));

        $previousProducts = [];
        foreach ($orderForViewing->getProducts()->getProducts() as $orderProductForViewing) {
            $previousProducts[$orderProductForViewing->getOrderDetailId()] = $orderProductForViewing;
        }

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
                $hasFreeShipping = null;
                if ($request->request->has('free_shipping')) {
                    $hasFreeShipping = (bool) filter_var($request->get('free_shipping'), FILTER_VALIDATE_BOOLEAN);
                }
                $addProductCommand = AddProductToOrderCommand::withNewInvoice(
                    $orderId,
                    (int) $request->get('product_id'),
                    (int) $request->get('combination_id'),
                    $request->get('price_tax_incl'),
                    $request->get('price_tax_excl'),
                    (int) $request->get('quantity'),
                    $hasFreeShipping
                );
            }
            $this->getCommandBus()->handle($addProductCommand);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }

        /**
         * Returning the products list view is not required since we reload the whole list
         * We keep it for now to avoid Breaking Change
         */
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));

        $updatedProducts = [];
        foreach ($orderForViewing->getProducts()->getProducts() as $orderProductForViewing) {
            $updatedProducts[$orderProductForViewing->getOrderDetailId()] = $orderProductForViewing;
        }

        $newProducts = array_diff_key($updatedProducts, $previousProducts);

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $cancelProductForm = $formBuilder->getFormFor($orderId);

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        $addedGridRows = '';
        foreach ($newProducts as $newProduct) {
            $addedGridRows .= $this->renderView('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig', [
                'orderForViewing' => $orderForViewing,
                'product' => $newProduct,
                'isColumnLocationDisplayed' => $newProduct->getLocation() !== '',
                'isColumnRefundedDisplayed' => $newProduct->getQuantityRefunded() > 0,
                'isAvailableQuantityDisplayed' => $this->configuration->getBoolean('PS_STOCK_MANAGEMENT'),
                'cancelProductForm' => $cancelProductForm->createView(),
                'orderCurrency' => $orderCurrency,
            ]);
        }

        return new Response($addedGridRows);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return Response
     */
    public function getProductPricesAction(int $orderId): Response
    {
        try {
            $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
            $productsForViewing = $orderForViewing->getProducts();
            $productList = $productsForViewing->getProducts();

            $result = [];
            foreach ($productList as $product) {
                $result[] = [
                    'orderDetailId' => $product->getOrderDetailId(),
                    'unitPrice' => $product->getUnitPrice(),
                    'unitPriceTaxExclRaw' => $product->getUnitPriceTaxExclRaw(),
                    'unitPriceTaxInclRaw' => $product->getUnitPriceTaxInclRaw(),
                    'quantity' => $product->getQuantity(),
                    'availableQuantity' => $product->getAvailableQuantity(),
                    'totalPrice' => $product->getTotalPrice(),
                ];
            }
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($result);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
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
            'display_total' => true,
        ]);

        return $this->json([
            'invoices' => $choices,
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     */
    public function getDocumentsAction(int $orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $this->json([
            'total' => count($orderForViewing->getDocuments()->getDocuments()),
            'html' => $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/documents.html.twig', [
                'orderForViewing' => $orderForViewing,
            ])->getContent(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     */
    public function getShippingAction(int $orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $this->json([
            'total' => count($orderForViewing->getShipping()->getCarriers()),
            'html' => $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/shipping.html.twig', [
                'orderForViewing' => $orderForViewing,
            ])->getContent(),
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

                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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

        $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

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
        try {
            $this->getCommandBus()->handle(new UpdateInvoiceNoteCommand(
                $orderInvoiceId,
                $request->request->get('invoice_note')
            ));
            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        } catch (InvoiceException $e) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($e, $this->getErrorMessages($e))
            );
        }

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
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));

        $products = $orderForViewing->getProducts()->getProducts();
        $product = array_reduce($products, function ($result, OrderProductForViewing $item) use ($orderDetailId) {
            return $item->getOrderDetailId() == $orderDetailId ? $item : $result;
        });

        // The whole product row has been removed so we return an empty response
        if (null === $product) {
            return new Response('');
        }

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $cancelProductForm = $formBuilder->getFormFor($orderId);

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/product.html.twig', [
            'cancelProductForm' => $cancelProductForm->createView(),
            'isColumnLocationDisplayed' => $product->getLocation() !== '',
            'isColumnRefundedDisplayed' => $product->getQuantityRefunded() > 0,
            'isAvailableQuantityDisplayed' => $this->configuration->getBoolean('PS_STOCK_MANAGEMENT'),
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

                try {
                    $this->getCommandBus()->handle(
                        new AddCartRuleToOrderCommand(
                            $orderId,
                            $data['name'],
                            $data['type'],
                            $data['value'] ?? null,
                            empty($data['invoice_id']) ? null : (int) $data['invoice_id']
                        )
                    );

                    $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
                } catch (Exception $e) {
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
        $formFactory = $this->get('form.factory');

        $form = $formFactory->createNamed(
            'update_order_status',
            UpdateOrderStatusType::class
        );
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            // Check if the form is submit from the action bar
            $form = $formFactory->createNamed(
                'update_order_status_action_bar',
                UpdateOrderStatusType::class
            );
            $form->handleRequest($request);
        }

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
                            (int) $this->getContext()->employee->id,
                            $data['id_invoice'],
                            $data['transaction_id']
                        )
                    );

                    $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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
            $this->getQueryBus()->handle(
                (new GetCartForOrderCreation($cartId))
                    ->setHideDiscounts(true)
            )
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

        if (null !== $routesCollection &&
            !$orderMessageForm->isValid() &&
            $viewRoute = $routesCollection->get('admin_orders_view')
        ) {
            $attributes = $viewRoute->getDefaults();
            $attributes['orderId'] = $orderId;

            return $this->forward(
                $viewRoute->getDefault('_controller'),
                $attributes
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

            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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

            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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
     * @return Response
     */
    public function getDiscountsAction(int $orderId): Response
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/discount_list.html.twig', [
            'discounts' => $orderForViewing->getDiscounts()->getDiscounts(),
            'orderId' => $orderId,
        ]);
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
        $orderForViewingPrices = $orderForViewing->getPrices();

        return $this->json([
            'orderTotalFormatted' => $orderForViewingPrices->getTotalAmountFormatted(),
            'discountsAmountFormatted' => $orderForViewingPrices->getDiscountsAmountFormatted(),
            'discountsAmountDisplayed' => $orderForViewingPrices->getDiscountsAmountRaw()->isGreaterThanZero(),
            'productsTotalFormatted' => $orderForViewingPrices->getProductsPriceFormatted(),
            'shippingTotalFormatted' => $orderForViewingPrices->getShippingPriceFormatted(),
            'shippingTotalDisplayed' => $orderForViewingPrices->getShippingPriceRaw()->isGreaterThanZero(),
            'taxesTotalFormatted' => $orderForViewingPrices->getTaxesAmountFormatted(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return Response
     */
    public function getPaymentsAction(int $orderId): Response
    {
        try {
            /** @var OrderForViewing $orderForViewing */
            $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

            return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/payments_alert.html.twig', [
                'payments' => $orderForViewing->getPayments(),
                'linkedOrders' => $orderForViewing->getLinkedOrders(),
            ]);
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
     * @return Response
     */
    public function getProductsListAction(int $orderId): Response
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));

        $currencyDataProvider = $this->container->get('prestashop.adapter.data_provider.currency');
        $orderCurrency = $currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.cancel_product_form_builder');
        $cancelProductForm = $formBuilder->getFormFor($orderId);

        $paginationNum = $this->configuration->getInt('PS_ORDER_PRODUCTS_NB_PER_PAGE', self::DEFAULT_PRODUCTS_NUMBER);
        $paginationNumOptions = self::PRODUCTS_PAGINATION_OPTIONS;
        if (!in_array($paginationNum, $paginationNumOptions)) {
            $paginationNumOptions[] = $paginationNum;
        }
        sort($paginationNumOptions);

        $isColumnLocationDisplayed = false;
        $isColumnRefundedDisplayed = false;

        foreach (array_slice($orderForViewing->getProducts()->getProducts(), $paginationNum) as $product) {
            if (!empty($product->getLocation())) {
                $isColumnLocationDisplayed = true;
            }
            if ($product->getQuantityRefunded() > 0) {
                $isColumnRefundedDisplayed = true;
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/Blocks/View/product_list.html.twig', [
            'orderForViewing' => $orderForViewing,
            'cancelProductForm' => $cancelProductForm->createView(),
            'orderCurrency' => $orderCurrency,
            'paginationNum' => $paginationNum,
            'isColumnLocationDisplayed' => $isColumnLocationDisplayed,
            'isColumnRefundedDisplayed' => $isColumnRefundedDisplayed,
            'isAvailableQuantityDisplayed' => $this->configuration->getBoolean('PS_STOCK_MANAGEMENT'),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     message="You do not have permission to generate this."
     * )
     *
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

            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
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
                    $this->addFlash('success', $this->trans('Selected products were successfully canceled.', 'Admin.Catalog.Notification'));
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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function configureProductPaginationAction(Request $request): JsonResponse
    {
        $numPerPage = (int) $request->request->get('numPerPage');
        if ($numPerPage < 1) {
            $numPerPage = self::DEFAULT_PRODUCTS_NUMBER;
        }

        try {
            $this->configuration->set('PS_ORDER_PRODUCTS_NB_PER_PAGE', $numPerPage);
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Method for downloading customization picture
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $orderId
     * @param string $value
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    public function displayCustomizationImageAction(int $orderId, string $value)
    {
        $uploadDir = $this->get('prestashop.adapter.legacy.context')->getUploadDirectory();
        $filePath = $uploadDir . $value;
        $filesystem = new Filesystem();

        try {
            if (!$filesystem->exists($filePath)) {
                $this->addFlash('error', $this->trans('The product customization picture could not be found.', 'Admin.Notifications.Error'));

                return $this->redirectToRoute('admin_orders_view', [
                    'orderId' => $orderId,
                ]);
            }

            $imageFile = new File($filePath);
            $fileName = sprintf('%s-customization-%s.%s', $orderId, $value, $imageFile->guessExtension() ?? 'jpg');

            return $this->file($filePath, $fileName);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Set order internal note.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_orders_index"
     * )
     *
     * @param mixed $orderId
     * @param Request $request
     *
     * @return Response
     */
    public function setInternalNoteAction($orderId, Request $request)
    {
        $internalNoteForm = $this->createForm(InternalNoteType::class);
        $internalNoteForm->handleRequest($request);

        if ($internalNoteForm->isSubmitted()) {
            $data = $internalNoteForm->getData();

            try {
                $this->getCommandBus()->handle(new SetInternalOrderNoteCommand(
                    (int) $orderId,
                    $data['note']
                ));

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('Update successful', 'Admin.Notifications.Success'),
                    ]);
                }

                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } catch (OrderException $e) {
                $this->addFlash(
                    'error',
                    $this->getErrorMessageForException($e, $this->getErrorMessages($e))
                );
            }
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller'))",
     *     message="You do not have permission to perform this search."
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchProductsAction(Request $request): JsonResponse
    {
        try {
            $defaultCurrencyId = (int) $this->get('prestashop.adapter.legacy.configuration')->get('PS_CURRENCY_DEFAULT');

            $searchPhrase = $request->query->get('search_phrase');
            $currencyId = $request->query->get('currency_id');
            $currencyIsoCode = $currencyId !== null
                ? Currency::getIsoCodeById((int) $currencyId)
                : Currency::getIsoCodeById($defaultCurrencyId);
            $orderId = null;
            if ($request->query->has('order_id')) {
                $orderId = (int) $request->query->get('order_id');
            }

            /** @var FoundProduct[] $foundProducts */
            $foundProducts = $this->getQueryBus()->handle(new SearchProducts($searchPhrase, 10, $currencyIsoCode, $orderId));

            return $this->json([
                'products' => $foundProducts,
            ]);
        } catch (ProductSearchEmptyPhraseException $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, [])],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
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
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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
        $orderInvoiceNumber = '#unknown';
        if ($e instanceof DuplicateProductInOrderInvoiceException) {
            $orderInvoiceNumber = $e->getOrderInvoiceNumber();
        }

        return [
            ProductSearchEmptyPhraseException::class => $this->trans(
                'Product search phrase must not be an empty string.',
                'Admin.Orderscustomers.Notification'
            ),
            CannotEditDeliveredOrderProductException::class => $this->trans(
                'You cannot edit the cart once the order delivered.',
                'Admin.Orderscustomers.Notification'
            ),
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
            InvoiceException::class => $this->trans(
                $e->getMessage(),
                'Admin.Orderscustomers.Notification'
            ),
            InvalidAmountException::class => $this->trans(
                'Only numbers and decimal points (".") are allowed in the amount fields, e.g. 10.50 or 1050.',
                'Admin.Orderscustomers.Notification'
            ),
            InvalidCartRuleDiscountValueException::class => [
                InvalidCartRuleDiscountValueException::INVALID_MIN_PERCENT => $this->trans(
                    'Percent value must be greater than 0.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCartRuleDiscountValueException::INVALID_MAX_PERCENT => $this->trans(
                    'Percent value cannot exceed 100.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCartRuleDiscountValueException::INVALID_MIN_AMOUNT => $this->trans(
                    'Amount value must be greater than 0.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCartRuleDiscountValueException::INVALID_MAX_AMOUNT => $this->trans(
                    'Discount value cannot exceed the total price of this order.',
                    'Admin.Orderscustomers.Notification'
                ),
                InvalidCartRuleDiscountValueException::INVALID_FREE_SHIPPING => $this->trans(
                    'Shipping discount value cannot exceed the total price of this order.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],
            InvalidCancelProductException::class => [
                InvalidCancelProductException::INVALID_QUANTITY => $this->trans(
                    'Positive product quantity is required.',
                    'Admin.Notifications.Error'
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
            InvalidModuleException::class => $this->trans(
                'You must choose a payment module to create the order.',
                'Admin.Orderscustomers.Notification'
            ),
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
                InvalidOrderStateException::INVALID_ID => $this->trans(
                    'You must choose an order status to create the order.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],

            OrderConstraintException::class => [
                OrderConstraintException::INVALID_CUSTOMER_MESSAGE => $this->trans(
                    'The order message given is invalid.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],
            InvalidProductQuantityException::class => $this->trans(
                'Positive product quantity is required.',
                'Admin.Notifications.Error'
            ),
            DuplicateProductInOrderException::class => $this->trans(
                'This product is already in your order, please edit the quantity instead.',
                'Admin.Notifications.Error'
            ),
            DuplicateProductInOrderInvoiceException::class => $this->trans(
                'This product is already in the invoice [1], please edit the quantity instead.',
                'Admin.Notifications.Error',
                ['[1]' => $orderInvoiceNumber]
            ),
            CannotFindProductInOrderException::class => $this->trans(
                'You cannot edit the price of a product that no longer exists in your catalog.',
                'Admin.Notifications.Error'
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
            OrderConstraintException::class => [
                OrderConstraintException::INVALID_PAYMENT_METHOD => sprintf(
                    '%s %s %s',
                    $this->trans(
                        'The selected payment method is invalid.',
                        'Admin.Orderscustomers.Notification'
                    ),
                    $this->trans(
                        'Invalid characters:',
                        'Admin.Notifications.Info'
                    ),
                    AddPaymentCommand::INVALID_CHARACTERS_NAME
                ),
            ],
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
                    'Order #%d cannot be loaded.',
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
