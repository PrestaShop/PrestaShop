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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\DuplicateOrderStateNameException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\MissingOrderStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
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
        ]);
    }

    /**
     * Process Grid search.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
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
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))")
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
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/create.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $this->get('prestashop.adapter.legacy.context')->getLanguages()),
        ]);
    }

    /**
     * Show order_state edit form & handle processing of it.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
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

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_order_states');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderStates/edit.html.twig', [
            'orderStateForm' => $orderStateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'editableOrderState' => $this->getQueryBus()->handle(new GetOrderStateForEditing((int) $orderStateId)),
            'contextLangId' => $this->getContextLangId(),
            'templatesPreviewUrl' => _MAIL_DIR_,
            'languages' => array_map(
                function (array $language) {
                    return [
                        'id' => $language['iso_code'],
                        'value' => sprintf('%s - %s', $language['iso_code'], $language['name']), ];
                }, $this->get('prestashop.adapter.legacy.context')->getLanguages()),
        ]);
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
            DuplicateOrderStateNameException::class => sprintf(
                '%s %s',
                $this->trans('An order status with the same name already exists:', 'Admin.Shopparameters.Notification'),
                $e instanceof DuplicateOrderStateNameException ? $e->getName()->getValue() : ''
            ),
            OrderStateConstraintException::class => [
                OrderStateConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],
            MissingOrderStateRequiredFieldsException::class => $this->trans(
                'The field %s is required.',
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
