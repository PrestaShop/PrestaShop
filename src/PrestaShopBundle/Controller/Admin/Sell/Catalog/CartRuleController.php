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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\SearchCartRules;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Search\Filters\CartRuleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Cart rules (a.k.a cart discounts/vouchers) actions in Back Office
 */
class CartRuleController extends FrameworkBundleAdminController
{
    /**
     * Displays cart rule listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CartRuleFilters $cartRuleFilters
     *
     * @return Response
     */
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
        ]);
    }

    /**
     * Searches for cart rules by provided search phrase
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request): JsonResponse
    {
        $searchPhrase = $request->query->get('search_phrase');
        $cartRules = [];

        if ($searchPhrase) {
            try {
                $cartRules = $this->getQueryBus()->handle(new SearchCartRules($searchPhrase));
            } catch (Exception $e) {
                return $this->json(
                    ['message' => $this->getFallbackErrorMessage(get_class($e), $e->getCode())],
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
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_cart_rules_index")
     * @DemoRestricted(redirectRoute="admin_cart_rules_index")
     *
     * @param int $cartRuleId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $cartRuleId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCartRuleCommand($cartRuleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * Deletes cartRules on bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_cart_rules_index")
     * @DemoRestricted(redirectRoute="admin_cart_rules_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkCartRulesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCartRuleCommand($cartRuleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_cart_rules_index');
    }

    /**
     * Toggles cart rule status
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_cart_rules_index")
     * @DemoRestricted(redirectRoute="admin_cart_rules_index")
     *
     * @param int $cartRuleId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $cartRuleId): RedirectResponse
    {
        try {
            /** @var EditableCartRule $editableCartRule */
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
     * Provides cart rule ids from request of bulk action
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCartRulesFromRequest(Request $request): array
    {
        $cartRuleIds = $request->request->get('cart_rule_bulk');

        if (!is_array($cartRuleIds)) {
            return [];
        }

        return array_map('intval', $cartRuleIds);
    }

    /**
     * Enables cart rules on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_cart_rules_index")
     * @DemoRestricted(redirectRoute="admin_cart_rules_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkCartRulesFromRequest($request);

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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_cart_rules_index")
     * @DemoRestricted(redirectRoute="admin_cart_rules_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $cartRuleIds = $this->getBulkCartRulesFromRequest($request);

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
        ];
    }
}
