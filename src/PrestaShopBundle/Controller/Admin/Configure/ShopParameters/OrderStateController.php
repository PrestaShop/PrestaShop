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

use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderReturnStatesGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderStatesGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnStatesFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderStatesFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Order states Settings" page.
 */
#[AllShopContext]
class OrderStateController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            OrderStatesGridDefinitionFactory::GRID_ID => OrderStatesGridDefinitionFactory::class,
            OrderReturnStatesGridDefinitionFactory::GRID_ID => OrderReturnStatesGridDefinitionFactory::class,
        ];
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        OrderStatesFilters $orderStatesFilters,
        OrderReturnStatesFilters $orderReturnStatesFilters,
        #[Autowire(service: 'prestashop.core.grid.factory.order_states')]
        GridFactoryInterface $orderStatesGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.order_return_states')]
        GridFactoryInterface $orderReturnStatesGridFactory,
    ): Response {
        $orderStatesGrid = $orderStatesGridFactory->getGrid($orderStatesFilters);
        $orderReturnStatesGrid = $orderReturnStatesGridFactory->getGrid($orderReturnStatesFilters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'orderStatesGrid' => $this->presentGrid($orderStatesGrid),
            'orderReturnStatesGrid' => $this->presentGrid($orderReturnStatesGrid),
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'enableSidebar' => true,
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchGridAction(
        Request $request
    ): RedirectResponse {
        if ($request->request->has(OrderReturnStatesGridDefinitionFactory::GRID_ID)) {
            $gridDefinitionFactory = $this->container->get(OrderReturnStatesGridDefinitionFactory::GRID_ID);
            $filterId = OrderReturnStatesGridDefinitionFactory::GRID_ID;
        } else {
            $gridDefinitionFactory = $this->container->get(OrderStatesGridDefinitionFactory::GRID_ID);
            $filterId = OrderStatesGridDefinitionFactory::GRID_ID;
        }

        return $this->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            $filterId,
            'admin_order_states'
        );
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_state_form_builder')]
        FormBuilderInterface $orderStateFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_state_form_handler')]
        FormHandlerInterface $orderStateFormHandler,
        LegacyContext $context,
    ): Response {
        $orderStateForm = $orderStateFormBuilder->getForm();
        $orderStateForm->handleRequest($request);

        try {
            $result = $orderStateFormHandler->handle($orderStateForm);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/create.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contextLangId' => $this->getLanguageContext()->getId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'enableSidebar' => true,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $context->getLanguages()),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'layoutTitle' => $this->trans('New order status', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(
        int $orderStateId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_state_form_builder')]
        FormBuilderInterface $orderStateFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_state_form_handler')]
        FormHandlerInterface $orderStateFormHandler,
        LegacyContext $context,
    ): Response {
        $orderStateForm = $orderStateFormBuilder->getFormFor($orderStateId);
        $orderStateForm->handleRequest($request);

        try {
            $result = $orderStateFormHandler->handleFor($orderStateId, $orderStateForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($orderStateForm);
                }

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $editableOrderState = $this->dispatchQuery(new GetOrderStateForEditing((int) $orderStateId));

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/edit.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableOrderState' => $editableOrderState,
            'contextLangId' => $this->getLanguageContext()->getId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'enableSidebar' => true,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $context->getLanguages()),
            'layoutTitle' => $this->trans(
                'Editing order status %name%',
                [
                    '%name%' => $editableOrderState->getLocalizedNames()[$this->getLanguageContext()->getId()],
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createOrderReturnStateAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_return_state_form_builder')]
        FormBuilderInterface $orderReturnStateFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_return_state_form_handler')]
        FormHandlerInterface $orderReturnStateFormHandler,
    ): Response {
        $orderReturnStateForm = $orderReturnStateFormBuilder->getForm();
        $orderReturnStateForm->handleRequest($request);

        try {
            $result = $orderReturnStateFormHandler->handle($orderReturnStateForm);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

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
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New return status', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editOrderReturnStateAction(
        int $orderReturnStateId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_return_state_form_builder')]
        FormBuilderInterface $orderReturnStateFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_return_state_form_handler')]
        FormHandlerInterface $orderReturnStateFormHandler,
    ): Response {
        $orderReturnStateForm = $orderReturnStateFormBuilder->getFormFor($orderReturnStateId);
        $orderReturnStateForm->handleRequest($request);

        try {
            $result = $orderReturnStateFormHandler->handleFor($orderReturnStateId, $orderReturnStateForm);

            if ($result->isSubmitted()) {
                if ($result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($orderReturnStateForm);
                }

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $editableOrderReturnState = $this->dispatchQuery(new GetOrderReturnStateForEditing((int) $orderReturnStateId));

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderReturnStates/edit.html.twig', [
            'orderReturnStateForm' => $orderReturnStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableOrderReturnState' => $editableOrderReturnState,
            'contextLangId' => $this->getLanguageContext()->getId(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing return status %name%',
                [
                    '%name%' => $editableOrderReturnState->getLocalizedNames()[$this->getLanguageContext()->getId()],
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states')]
    public function deleteOrderReturnStateAction(Request $request, int $orderReturnStateId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteOrderReturnStateCommand($orderReturnStateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $request->query->has('redirectUrl') ?
            $this->redirect($request->query->get('redirectUrl')) :
            $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states', message: 'You do not have permission to delete this.')]
    public function deleteOrderReturnStateBulkAction(Request $request): RedirectResponse
    {
        $orderReturnStateIds = $this->getBulkOrderReturnStatesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteOrderReturnStateCommand($orderReturnStateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (OrderReturnStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states', message: 'You do not have permission to edit this.')]
    public function toggleDeliveryAction(int $orderStateId): RedirectResponse
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->dispatchQuery(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setDelivery(!$editableOrderState->isDelivery());

            $this->dispatchCommand($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states', message: 'You do not have permission to edit this.')]
    public function toggleInvoiceAction(int $orderStateId): RedirectResponse
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->dispatchQuery(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setInvoice(!$editableOrderState->isInvoice());

            $this->dispatchCommand($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states', message: 'You do not have permission to edit this.')]
    public function toggleSendEmailAction(int $orderStateId): RedirectResponse
    {
        try {
            /** @var EditableOrderState $editableOrderState */
            $editableOrderState = $this->dispatchQuery(new GetOrderStateForEditing((int) $orderStateId));

            $editOrderStateCommand = new EditOrderStateCommand((int) $orderStateId);
            $editOrderStateCommand->setSendEmail(!$editableOrderState->isSendEmailEnabled());

            $this->dispatchCommand($editOrderStateCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states')]
    public function deleteAction(Request $request, int $orderStateId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteOrderStateCommand($orderStateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $request->query->has('redirectUrl') ?
            $this->redirect($request->query->get('redirectUrl')) :
            $this->redirectToRoute('admin_order_states');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_states', message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $orderStateIds = $this->getBulkOrderStatesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteOrderStateCommand($orderStateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (OrderStateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_states');
    }

    private function getBulkOrderStatesFromRequest(Request $request): array
    {
        $orderStateIds = $request->request->all('order_states_order_states_bulk');
        if (empty($orderStateIds)) {
            return [];
        }

        return array_map(function (string $orderStateId): int {
            return (int) $orderStateId;
        }, $orderStateIds);
    }

    private function getBulkOrderReturnStatesFromRequest(Request $request): array
    {
        $orderReturnStateIds = $request->request->all('order_return_states_order_return_states_bulk');

        return array_map(static function (string $orderReturnStateId) {
            return (int) $orderReturnStateId;
        }, $orderReturnStateIds);
    }

    /**
     * Get errors that can be used to translate exceptions into user-friendly messages
     *
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        return [
            OrderStateNotFoundException::class => $this->trans(
                'This order status does not exist.',
                [],
                'Admin.Notifications.Error'
            ),
            DuplicateOrderStateNameException::class => $this->trans(
                'An order status with the same name already exists: %s',
                [$e instanceof DuplicateOrderStateNameException ? $e->getName()->getValue() : ''],
                'Admin.Shopparameters.Notification',
            ),
            OrderStateConstraintException::class => [
                OrderStateConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
            ],
            MissingOrderStateRequiredFieldsException::class => $this->trans(
                'The %s field is required.',
                [
                    implode(
                        ',',
                        $e instanceof MissingOrderStateRequiredFieldsException ? $e->getMissingRequiredFields() : []
                    ),
                ],
                'Admin.Notifications.Error',
            ),
        ];
    }
}
