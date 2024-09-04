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
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\UpdateOrderReturnException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as OptionFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MerchandiseReturnController responsible for "Sell > Customer Service > Merchandise Returns" page
 */
class MerchandiseReturnController extends PrestaShopAdminController
{
    /**
     * Render merchandise returns grid and options.
     *
     * @param Request $request
     * @param MerchandiseReturnFilters $filters
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.factory.merchandise_return')]
        GridFactoryInterface $gridFactory,
        MerchandiseReturnFilters $filters,
        #[Autowire(service: 'prestashop.admin.merchandise_return_options.form_handler')]
        OptionFormHandlerInterface $optionFormHandler
    ): Response {
        $optionsForm = $optionFormHandler->getForm();
        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {
            $errors = $optionFormHandler->save($optionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            } else {
                $this->addFlashErrors($errors);
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
     * @param int $orderReturnId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function editAction(
        int $orderReturnId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_return_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_return_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        try {
            $form = $formBuilder->getFormFor($orderReturnId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($orderReturnId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_merchandise_returns_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/edit.html.twig', [
            'orderReturnForm' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Return merchandise authorization (RMA)', [], 'Admin.Navigation.Menu'),
        ]);
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
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            OrderReturnNotFoundException::class => $this->trans(
                'Merchandise return not found.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            OrderReturnOrderStateConstraintException::class => [
                OrderReturnOrderStateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            UpdateOrderReturnException::class => $this->trans(
                'An error occurred while trying to update merchandise return.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
        ];
    }
}
