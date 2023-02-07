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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Adapter\PDF\OrderReturnPdfGenerator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\DeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteOrderReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\UpdateOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\OrderReturnSettings;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnProductsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class MerchandiseReturnController responsible for "Sell > Customer Service > Merchandise Returns" page
 */
class MerchandiseReturnController extends FrameworkBundleAdminController
{
    /**
     * Render merchandise returns grid and options.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_returns_index"
     * )
     *
     * @param Request $request
     * @param MerchandiseReturnFilters $filters
     *
     * @return Response|RedirectResponse
     */
    public function indexAction(Request $request, MerchandiseReturnFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return');

        $optionsFormHandler = $this->getOptionsFormHandler();
        $optionsForm = $optionsFormHandler->getForm();
        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {
            $errors = $optionsFormHandler->save($optionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'merchandiseReturnsGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
            'merchandiseReturnsOptionsForm' => $optionsForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Edit existing order return
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_returns_index"
     * )
     *
     * @param int $orderReturnId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $orderReturnId, OrderReturnProductsFilters $filters, Request $request): Response
    {
        $gridFactory = $this->get('PrestaShop\Core\Grid\Factory\OrderReturnProducts');
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.order_return_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_return_form_handler');

        try {
            $editableOrderReturn = $this->getQueryBus()->handle(
                new GetOrderReturnForEditing(
                    $orderReturnId
                )
            );
            $form = $formBuilder->getFormFor($orderReturnId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($orderReturnId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_merchandise_returns_index');
        }

        $allowPrintingOrderReturnPdf =
            $editableOrderReturn->getOrderReturnStateId() === OrderReturnSettings::ORDER_RETURN_STATE_WAITING_FOR_PACKAGE_ID;

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/edit.html.twig', [
            'allowPrintingOrderReturnPdf' => $allowPrintingOrderReturnPdf,
            'editableOrderReturn' => $editableOrderReturn,
            'orderReturnForm' => $form->createView(),
            'orderReturnsProductsGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Return merchandise authorization (RMA)', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_merchandise_returns_index")
     *
     * @param Request $request
     * @param int $orderReturnId
     * @param int $orderReturnDetailId
     *
     * @return RedirectResponse
     */
    public function deleteProductAction(Request $request, int $orderReturnId, int $orderReturnDetailId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(
                new DeleteProductFromOrderReturnCommand($orderReturnId, $orderReturnDetailId)
            );

            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute(
            'admin_order_returns_edit',
            [
                'orderReturnId' => $orderReturnId,
            ]
        );
    }

    /**
     * Deletes order return products on bulk action
     *
     * @DemoRestricted(redirectRoute="admin_merchandise_returns_index")
     *
     * @param int $orderReturnId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteProductAction(int $orderReturnId, Request $request): RedirectResponse
    {
        $orderReturnDetails = $this->getBulkOrderReturnDetailsFromRequest($request);

        try {
            $this->getCommandBus()->handle(
                new BulkDeleteProductFromOrderReturnCommand(
                    $orderReturnId,
                    $orderReturnDetails
                )
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute(
            'admin_order_returns_edit',
            [
                'orderReturnId' => $orderReturnId,
            ]
        );
    }

    /**
     * Provides order return ids from request of bulk action
     *
     * @param Request $request
     *
     * @return OrderReturnDetailId[]
     */
    private function getBulkOrderReturnDetailsFromRequest(Request $request): array
    {
        $orderReturnDetailIds = $request->request->get('order_return_products_order_return_bulk');
        if (!is_array($orderReturnDetailIds)) {
            return [];
        }

        return $orderReturnDetailIds;
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            OrderReturnConstraintException::class => [
                OrderReturnConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            OrderReturnNotFoundException::class => $this->trans(
                'Merchandise return not found.',
                'Admin.Orderscustomers.Notification'
            ),
            OrderReturnOrderStateConstraintException::class => [
                OrderReturnOrderStateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            BulkDeleteOrderReturnProductException::class => [
                BulkDeleteOrderReturnProductException::CANT_DELETE_PRODUCT_NOT_PART_OF_ORDER_RETURN => $this->trans(
                    'Some order details don\'t exist in merchandise return',
                    'Admin.Notifications.Error'
                ),
                BulkDeleteOrderReturnProductException::CANT_DELETE_ALL_PRODUCTS => $this->trans(
                    'Merchandise return must have at least one product left',
                    'Admin.Notifications.Error'
                ),
                BulkDeleteOrderReturnProductException::UNEXPECTED_ERROR => $this->trans(
                    'Failed to delete some of merchandise return products',
                    'Admin.Notifications.Error'
                ),
            ],
            UpdateOrderReturnException::class => $this->trans(
                'An error occurred while trying to update merchandise return.',
                'Admin.Orderscustomers.Notification'
            ),
        ];
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_merchandise_returns_index")
     *
     * @param Request $request
     * @param int $orderReturnId
     *
     * @return Response
     */
    public function generateOrderReturnPdfAction(Request $request, int $orderReturnId): Response
    {
        return new StreamedResponse($this->get(OrderReturnPdfGenerator::class)->generatePDF([$orderReturnId]));
    }

    /**
     * @return FormHandlerInterface
     */
    private function getOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.merchandise_return_options.form_handler');
    }
}
