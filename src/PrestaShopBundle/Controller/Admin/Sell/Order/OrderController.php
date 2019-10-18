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
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailResendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddOrderCartRuleType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrderCurrencyType;
use PrestaShopBundle\Form\Admin\Sell\Order\AddProductToOrderType;
use PrestaShopBundle\Form\Admin\Sell\Order\ChangeOrdersStatusType;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderPaymentType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateProductInOrderType;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderStatusType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
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
            ]
        );
    }

    //@todo: wip
    public function createAction()
    {
        return $this->render('@PrestaShop/Admin/Sell/Order/Order/create.html.twig', [
            'currencies' => \Currency::getCurrenciesByIdShop(\Context::getContext()->shop->id),
            'languages' => \Language::getLanguages(true, \Context::getContext()->shop->id),
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

    public function viewAction(int $orderId): Response
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

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

        $changeOrderCurrencyForm = $this->createForm(ChangeOrderCurrencyType::class, [], [
            'current_currency_id' => $orderForViewing->getCurrencyId(),
        ]);
        $privateNoteForm = $this->createForm(PrivateNoteType::class, [
            'note' => $orderForViewing->getCustomer()->getPrivateNote(),
        ]);
        $addProductToOrderForm = $this->createForm(AddProductToOrderType::class);
        $updateOrderProductForm = $this->createForm(UpdateProductInOrderType::class);

        return $this->render('@PrestaShop/Admin/Sell/Order/Order/view.html.twig', [
            'showContentHeader' => false,
            'orderForViewing' => $orderForViewing,
            'addOrderCartRuleForm' => $addOrderCartRuleForm->createView(),
            'updateOrderStatusForm' => $updateOrderStatusForm->createView(),
            'updateOrderStatusActionBarForm' => $updateOrderStatusActionBarForm->createView(),
            'addOrderPaymentForm' => $addOrderPaymentForm->createView(),
            'changeOrderCurrencyForm' => $changeOrderCurrencyForm->createView(),
            'privateNoteForm' => $privateNoteForm->createView(),
            'addProductToOrderForm' => $addProductToOrderForm->createView(),
            'updateOrderProductForm' => $updateOrderProductForm->createView(),
        ]);
    }

    /**
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

    public function updateProductAction(int $orderId, int $orderDetailId, Request $request): RedirectResponse
    {
        $updateOrderProductForm = $this->createForm(UpdateProductInOrderType::class);
        $updateOrderProductForm->handleRequest($request);

        if ($updateOrderProductForm->isSubmitted() && $updateOrderProductForm->isValid()) {
            $data = $updateOrderProductForm->getData();

            $this->getCommandBus()->handle(
                new UpdateProductInOrderCommand(
                    $orderId,
                    $orderDetailId,
                    $data['price_tax_excl'],
                    $data['price_tax_incl'],
                    $data['quantity']
                )
            );

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
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

        if ($form->isSubmitted() && $form->isValid()) {
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
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_orders_view', [
            'orderId' => $orderId,
        ]);
    }

    /**
     * Duplicates cart from specified order
     *
     * @param int $orderId
     *
     * @return JsonResponse
     *
     * @throws CartConstraintException
     */
    public function duplicateOrderCartAction(int $orderId)
    {
        $cartId = $this->getCommandBus()->handle(new DuplicateOrderCartCommand($orderId))->getValue();

        return $this->json(
            $this->getQueryBus()->handle(new GetCartInformation($cartId))
        );
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
     * @param OrderException $e
     *
     * @return array
     */
    private function getErrorMessages(OrderException $e)
    {
        return [
            CannotEditDeliveredOrderProductException::class => $this->trans('You cannot edit the cart once the order delivered', 'Admin.Orderscustomers.Notification'),
            OrderNotFoundException::class => $e instanceof OrderNotFoundException ?
                $this->trans(
                    'Order #%d cannot be loaded',
                    'Admin.Orderscustomers.Notification',
                    ['#%d' => $e->getOrderId()->getValue()]
                ) : '',
            OrderEmailResendException::class => $this->trans(
                'An error occurred while sending the e-mail to the customer.',
                'Admin.Orderscustomers.Notification'
            ),
        ];
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
}
