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
declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\BulkDeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\DeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query\GetOrderReturnStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\BulkDeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\DeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\DuplicateOrderStateNameException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\MissingOrderStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;
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
class OrderStateController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
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
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Process Grid search.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
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

    /**
     * Show order_state create form & handle processing of it.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $orderStateForm = $this->get('prestashop.core.form.identifiable_object.builder.order_state_form_builder')->getForm();
        $orderStateForm->handleRequest($request);

        $orderStateFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_state_form_handler');

        try {
            $result = $orderStateFormHandler->handle($orderStateForm);

            if ($orderStateId = $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/create.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contextLangId' => $this->getContextLangId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'enableSidebar' => true,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $this->get('prestashop.adapter.legacy.context')->getLanguages()),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
            'layoutTitle' => $this->trans('New order status', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Show order_state edit form & handle processing of it.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function editAction(int $orderStateId, Request $request)
    {
        $orderStateForm = $this->get('prestashop.core.form.identifiable_object.builder.order_state_form_builder')->getFormFor($orderStateId);
        $orderStateForm->handleRequest($request);

        $orderStateFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_state_form_handler');

        try {
            $result = $orderStateFormHandler->handleFor($orderStateId, $orderStateForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($orderStateForm);
                }

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $editableOrderState = $this->getQueryBus()->handle(new GetOrderStateForEditing((int) $orderStateId));

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/edit.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableOrderState' => $editableOrderState,
            'contextLangId' => $this->getContextLangId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'enableSidebar' => true,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $this->get('prestashop.adapter.legacy.context')->getLanguages()),
            'layoutTitle' => $this->trans(
                'Editing order status %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $editableOrderState->getLocalizedNames()[$this->getContextLangId()],
                ]
            ),
        ]);
    }

    /**
     * Show order return state create form & handle processing of it.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createOrderReturnStateAction(Request $request)
    {
        $orderReturnStateForm = $this->get('prestashop.core.form.identifiable_object.builder.order_return_state_form_builder')->getForm();
        $orderReturnStateForm->handleRequest($request);

        $orderReturnStateFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_return_state_form_handler');

        try {
            $result = $orderReturnStateFormHandler->handle($orderReturnStateForm);

            if ($orderReturnStateId = $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderReturnStates/create.html.twig', [
            'orderReturnStateForm' => $orderReturnStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New return status', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Show order return state edit form & handle processing of it.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function editOrderReturnStateAction(int $orderReturnStateId, Request $request)
    {
        $orderReturnStateForm = $this->get('prestashop.core.form.identifiable_object.builder.order_return_state_form_builder')->getFormFor($orderReturnStateId);
        $orderReturnStateForm->handleRequest($request);

        $orderReturnStateFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_return_state_form_handler');

        try {
            $result = $orderReturnStateFormHandler->handleFor($orderReturnStateId, $orderReturnStateForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($orderReturnStateForm);
                }

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $editableOrderReturnState = $this->getQueryBus()->handle(new GetOrderReturnStateForEditing((int) $orderReturnStateId));

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderReturnStates/edit.html.twig', [
            'orderReturnStateForm' => $orderReturnStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableOrderReturnState' => $editableOrderReturnState,
            'contextLangId' => $this->getContextLangId(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing return status %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $editableOrderReturnState->getLocalizedNames()[$this->getContextLangId()],
                ]
            ),
        ]);
    }

    /**
     * Deletes order return state
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_order_states")
     *
     * @param int $orderReturnStateId
     *
     * @return RedirectResponse
     */
    public function deleteOrderReturnStateAction(Request $request, int $orderReturnStateId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteOrderReturnStateCommand($orderReturnStateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $request->query->has('redirectUrl') ?
            $this->redirect($request->query->get('redirectUrl')) :
            $this->redirectToRoute('admin_order_states');
    }

    /**
     * Delete order return states in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_order_states",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteOrderReturnStateBulkAction(Request $request): RedirectResponse
    {
        $orderReturnStateIds = $this->getBulkOrderReturnStatesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteOrderReturnStateCommand($orderReturnStateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    /**
     * Toggle order state delivery option.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_order_states",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderStateId
     *
     * @return RedirectResponse
     */
    public function toggleDeliveryAction($orderStateId)
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->getQueryBus()->handle(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setDelivery(!$editableOrderState->isDelivery());

            $this->getCommandBus()->handle($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    /**
     * Toggle order state invoice option.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_order_states",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderStateId
     *
     * @return RedirectResponse
     */
    public function toggleInvoiceAction($orderStateId)
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->getQueryBus()->handle(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setInvoice(!$editableOrderState->isInvoice());

            $this->getCommandBus()->handle($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    /**
     * Toggle order state send_email option.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_order_states",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $orderStateId
     *
     * @return RedirectResponse
     */
    public function toggleSendEmailAction($orderStateId)
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->getQueryBus()->handle(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setSendEmail(!$editableOrderState->isSendEmailEnabled());

            $this->getCommandBus()->handle($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    /**
     * Deletes order state
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_order_states")
     *
     * @param int $orderStateId
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, int $orderStateId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteOrderStateCommand($orderStateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $request->query->has('redirectUrl') ?
            $this->redirect($request->query->get('redirectUrl')) :
            $this->redirectToRoute('admin_order_states');
    }

    /**
     * Delete order states in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_order_states",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $orderStateIds = $this->getBulkOrderStatesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteOrderStateCommand($orderStateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkOrderStatesFromRequest(Request $request): array
    {
        $orderStateIds = $request->request->all('order_states_order_states_bulk');

        if (!is_array($orderStateIds)) {
            return [];
        }

        return array_map(function (string $orderStateId): int {
            return (int) $orderStateId;
        }, $orderStateIds);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkOrderReturnStatesFromRequest(Request $request): array
    {
        $orderReturnStateIds = $request->request->all('order_return_states_order_return_states_bulk');

        return array_map(static function (string $orderReturnStateId) {
            return (int) $orderReturnStateId;
        }, $orderReturnStateIds);
    }

    /**
     * Get errors that can be used to translate exceptions into user friendly messages
     *
     * @return array
     */
    private function getErrorMessages(\Exception $e)
    {
        return [
            OrderStateNotFoundException::class => $this->trans(
                'This order status does not exist.',
                'Admin.Notifications.Error'
            ),
            DuplicateOrderStateNameException::class => $this->trans(
                'An order status with the same name already exists: %s',
                'Admin.Shopparameters.Notification',
                [$e instanceof DuplicateOrderStateNameException ? $e->getName()->getValue() : '']
            ),
            OrderStateConstraintException::class => [
                OrderStateConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],
            MissingOrderStateRequiredFieldsException::class => $this->trans(
                'The %s field is required.',
                'Admin.Notifications.Error',
                [
                    implode(
                        ',',
                        $e instanceof MissingOrderStateRequiredFieldsException ? $e->getMissingRequiredFields() : []
                    ),
                ]
            ),
        ];
    }
}
