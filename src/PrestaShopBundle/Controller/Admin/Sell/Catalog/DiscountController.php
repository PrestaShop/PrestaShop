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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\DeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\DiscountFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountController extends PrestaShopAdminController
{
    /**
     * Displays discount listing page.
     *
     * @param Request $request
     * @param DiscountFilters $discountFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        DiscountFilters $discountFilters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.discount')]
        GridFactoryInterface $discountFactory,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.create_discount_form_builder')]
        FormBuilderInterface $formBuilder,
    ): Response {
        $discountGrid = $discountFactory->getGrid($discountFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Discount/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'discountGrid' => $this->presentGrid($discountGrid),
            'createDiscountForm' => $formBuilder->getForm()->createView(),
            'layoutTitle' => $this->trans('Discounts', [], 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => [
                'add_discount' => [
                    'desc' => $this->trans('Create discount', [], 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                    'modal_target' => '#createDiscountModal',
                ],
            ],
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_discounts_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_discounts_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.create_discount_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.discount_form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($form);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                // @todo: redirect to edition page when it is implemented
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_discounts_index');
    }

    /**
     * Toggles discount status
     *
     * @param int $discountId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_discounts_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_discounts_index')]
    public function toggleStatusAction(int $discountId): RedirectResponse
    {
        try {
            /** @var DiscountForEditing $editableDiscount */
            $editableDiscount = $this->dispatchQuery(new GetDiscountForEditing($discountId));

            // @todo: this should be replaced with dedicated discount command when available
            $this->dispatchCommand(
                new ToggleCartRuleStatusCommand((int) $discountId, !$editableDiscount->isActive())
            );
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (CartRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_discounts_index');
    }

    /**
     * Deletes discount
     *
     * @param int $discountId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_discounts_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_discounts_index')]
    public function deleteAction(int $discountId): RedirectResponse
    {
        try {
            // @todo: this should be replaced with dedicated discount command when available
            $this->dispatchCommand(new DeleteCartRuleCommand($discountId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_discounts_index');
    }

    private function getErrorMessages(Exception $e): array
    {
        return [
        ];
    }
}
