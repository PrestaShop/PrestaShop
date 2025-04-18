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
use Language;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\BulkDeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\DeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageNameAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Query\GetOrderMessageForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult\EditableOrderMessage;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderMessageFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages page under "Sell > Customer Service > Order Messages"
 */
class OrderMessageController extends PrestaShopAdminController
{
    /**
     * Show list of Order messages
     *
     * @param OrderMessageFilters $filters
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.order_message')]
        GridFactoryInterface $orderMessageGridFactory,
        OrderMessageFilters $filters
    ): Response {
        $grid = $orderMessageGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Order messages', [], 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_order_messages_create'),
                    'desc' => $this->trans('Add new order message', [], 'Admin.Orderscustomers.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'orderMessageGrid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * Create new order message
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_order_messages_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_message_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_message_form_handler')]
        FormHandlerInterface $formHandler,
        MultistoreFeature $multiStoreFeature
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_messages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New order message', [], 'Admin.Navigation.Menu'),
            'orderMessageForm' => $form->createView(),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $multiStoreFeature->isUsed(),
        ]);
    }

    /**
     * Edit existing order message
     *
     * @param int $orderMessageId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_order_messages_index')]
    public function editAction(
        int $orderMessageId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_message_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_message_form_handler')]
        FormHandlerInterface $formHandler,
        LanguageContext $languageContext
    ): Response {
        try {
            /** @var EditableOrderMessage $editableOrderMessage */
            $editableOrderMessage = $this->dispatchQuery(new GetOrderMessageForEditing($orderMessageId));

            $orderMessageName = $editableOrderMessage->getLocalizedName()[$languageContext->getId()];

            $form = $formBuilder->getFormFor($orderMessageId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($orderMessageId, $form);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_messages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_order_messages_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Editing message %s', [$orderMessageName], 'Admin.Navigation.Menu'),
            'orderMessageForm' => $form->createView(),
        ]);
    }

    /**
     * Delete single order message
     *
     * @param int $orderMessageId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_messages_index')]
    public function deleteAction(int $orderMessageId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteOrderMessageCommand($orderMessageId));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_messages_index');
    }

    /**
     * Delete order messages in bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_order_messages_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        try {
            $orderMessageIds = array_map(static function ($orderMessageId) {
                return (int) $orderMessageId;
            }, $request->request->all('order_message_order_messages_bulk'));

            $this->dispatchCommand(new BulkDeleteOrderMessageCommand($orderMessageIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_order_messages_index');
    }

    /**
     * Get user friendly errors for exception
     *
     * @param Exception|null $e
     *
     * @return array
     */
    private function getErrorMessages(?Exception $e = null): array
    {
        $language = $e instanceof OrderMessageNameAlreadyUsedException ? (new Language($e->getLangId()))->name : '';

        return [
            OrderMessageException::class => [
                OrderMessageException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                OrderMessageException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            OrderMessageNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            OrderMessageNameAlreadyUsedException::class => $this->trans(
                'An order message with the same name already exists in %s.',
                [
                    $language,
                ],
                'Admin.Orderscustomers.Notification',
            ),
        ];
    }
}
