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
use PrestaShop\PrestaShop\Core\Search\Filters\OrderMessageFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
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
}
