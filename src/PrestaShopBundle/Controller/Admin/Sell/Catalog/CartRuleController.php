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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkDeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\DeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\BulkDeleteCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\BulkToggleCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\SearchCartRules;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CartRuleFormDataProvider;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CartRuleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Cart rules (a.k.a cart discounts/vouchers) actions in Back Office
 */
class CartRuleController extends FrameworkBundleAdminController
{
    use BulkActionsTrait;

    /**
     * Displays cart rule listing page.
     *
     * @param Request $request
     * @param CartRuleFilters $cartRuleFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        CartRuleFilters $cartRuleFilters
    ): Response {
        $cartRuleGridFactory = $this->get('prestashop.core.grid.grid_factory.cart_rule');
        $cartRuleGrid = $cartRuleGridFactory->getGrid($cartRuleFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CartRule/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'cartRuleGrid' => $this->presentGrid($cartRuleGrid),
            'layoutTitle' => $this->trans('Cart rules', 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => [
                'add_cart_rule' => [
                    'href' => $this->generateUrl('admin_cart_rules_create'),
                    'desc' => $this->trans('Add new cart rule', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * Searches for cart rules by provided search phrase
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function searchAction(Request $request): JsonResponse
    {
        $searchPhrase = $request->query->get('search_phrase');
        $cartRules = [];

        if ($searchPhrase) {
            try {
                $cartRules = $this->getQueryBus()->handle(new SearchCartRules($searchPhrase));
            } catch (Exception $e) {
                return $this->json(
                    ['message' => $this->getFallbackErrorMessage($e::class, $e->getCode())],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }

        return $this->json([
            'cart_rules' => $cartRules,
        ]);
    }

    /**
     * Deletes cart rule
     *
     * @param int $cartRuleId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function deleteAction(int $cartRuleId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCartRuleCommand($cartRuleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * Deletes cartRules on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkActionIds($request, 'cart_rule_bulk');

        try {
            $this->getCommandBus()->handle(new BulkDeleteCartRuleCommand($cartRuleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * Toggles cart rule status
     *
     * @param int $cartRuleId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function toggleStatusAction(int $cartRuleId): RedirectResponse
    {
        try {
            /** @var CartRuleForEditing $editableCartRule */
            $editableCartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing((int) $cartRuleId));
            $this->getCommandBus()->handle(
                new ToggleCartRuleStatusCommand((int) $cartRuleId, !$editableCartRule->getInformation()->isEnabled())
            );
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CartRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function createAction(Request $request): Response
    {
        $form = $this->getFormBuilder()->getForm($this->prefillFormDataForCreation($request));
        $form->handleRequest($request);

        try {
            $handlerResult = $this->getFormHandler()->handle($form);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                //@todo: redirect to edition page when it is implemented
                return $this->redirectToRoute('admin_cart_rules_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CartRule/create.html.twig', [
            'enableSidebar' => true,
            'cartRuleForm' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Enables cart rules on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkActionIds($request, 'cart_rule_bulk');

        try {
            $this->getCommandBus()->handle(new BulkToggleCartRuleStatusCommand($cartRuleIds, true));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CartRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * Disables cart rules on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cart_rules_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cart_rules_index')]
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkActionIds($request, 'cart_rule_bulk');

        try {
            $this->getCommandBus()->handle(new BulkToggleCartRuleStatusCommand($cartRuleIds, false));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CartRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * This just prefills form fields that depends on query parameter like customer search input (if id is provided),
     * all remaining data should be set in related form data provider.
     * (The parameter is in the URL, not the post data which is why it's out of the provider's responsibility)
     *
     * @param Request $request
     *
     * @return array
     */
    private function prefillFormDataForCreation(Request $request): array
    {
        $formData = [];

        $customerId = $request->query->getInt('customerId');
        if ($customerId) {
            $cartRuleFormDataProvider = $this->get(CartRuleFormDataProvider::class);
            // form data is multidimensional, so we need to get all of it and override only customer,
            // or else the remaining data from data provider 'conditions' tab will be lost
            $formData = $cartRuleFormDataProvider->getDefaultData();
            /** @var ViewableCustomer $customer */
            $customer = $this->getQueryBus()->handle(new GetCustomerForViewing($customerId));
            $customerInfo = $customer->getPersonalInformation();
            $formData['conditions']['customer'][] = [
                'id_customer' => $customerId,
                'fullname_and_email' => sprintf(
                    '%s %s - %s',
                    $customerInfo->getFirstname(),
                    $customerInfo->getLastname(),
                    $customerInfo->getEmail()
                ),
            ];
        }

        return $formData;
    }

    private function getErrorMessages(Exception $e): array
    {
        return [
            BulkDeleteCartRuleException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof BulkDeleteCartRuleException ? implode(', ', $e->getCartRuleIds()) : ''
            ),
            BulkToggleCartRuleException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while toggling this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof BulkToggleCartRuleException ? implode(', ', $e->getCartRuleIds()) : ''
            ),
            CartRuleConstraintException::class => [
                CartRuleConstraintException::NON_UNIQUE_CODE => $this->trans(
                    'This cart rule code is already used',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.cart_rule_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.cart_rule_form_builder');
    }
}
