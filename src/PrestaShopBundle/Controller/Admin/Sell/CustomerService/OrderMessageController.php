<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\BulkDeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\DeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerAddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OrderMessageGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderMessageFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderMessageController extends FrameworkBundleAdminController
{
    public function indexAction(OrderMessageFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.grid_factory.order_message');
        $grid = $gridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/index.html.twig', [
            'layoutTitle' => $this->trans('Order Messages', 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_order_messages_create'),
                    'desc' => $this->trans('Add new customer', 'Admin.Orderscustomers.Feature'),
                    'icon' => 'add_circle_outline'
                ],
            ],
            'orderMessageGrid' => $this->presentGrid($grid),
        ]);
    }

    public function filterAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.order_message'),
            $request,
            OrderMessageGridDefinitionFactory::GRID_ID,
            'admin_order_messages_index'
        );
    }

    public function createAction(Request $request): Response
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.order_message_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_message_form_handler');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_messages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/create.html.twig', [
            'orderMessageForm' => $form->createView(),
        ]);
    }

    public function editAction(int $orderMessageId, Request $request): Response
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.order_message_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.order_message_form_handler');

        $form = $formBuilder->getFormFor($orderMessageId);
        $form->handleRequest($request);

        try {
            $result = $formHandler->handleFor($orderMessageId, $form);

            if ($result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_messages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderMessage/edit.html.twig', [
            'orderMessageForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $orderMessageId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteOrderMessageCommand($orderMessageId));

            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_order_messages_index');
    }

    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        try {
            $orderMessageIds = array_map(static function ($orderMessageId) {
                return (int) $orderMessageId;
            }, $request->request->get('order_message_order_messages_bulk'));

            $this->getCommandBus()->handle(new BulkDeleteOrderMessageCommand($orderMessageIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_order_messages_index');
    }
}
