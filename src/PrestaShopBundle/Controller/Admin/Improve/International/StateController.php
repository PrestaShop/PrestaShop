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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Search\Filters\StateFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Responsible for handling country states data
 */
class StateController extends FrameworkBundleAdminController
{
    /**
     * Provides country states in json response
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getStatesAction(Request $request)
    {
        try {
            $countryId = (int) $request->query->get('id_country');
            $statesProvider = $this->get('prestashop.adapter.form.choice_provider.country_state_by_id');
            $states = $statesProvider->getChoices([
                'id_country' => $countryId,
            ]);

            return $this->json([
                'states' => $states,
            ]);
        } catch (Exception $e) {
            return $this->json([
                'message' => $this->getErrorMessageForException($e, []),
            ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Show states listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param StateFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, StateFilters $filters): Response
    {
        $stateGrid = $this->get('prestashop.core.grid.grid_factory.state')->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/State/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'stateGrid' => $this->presentGrid($stateGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }

    /**
     * Deletes state
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_states_index")
     * @DemoRestricted(redirectRoute="admin_states_index")
     *
     * @param int $stateId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $stateId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteStateCommand($stateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Handles edit form rendering and submission
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_states_index"
     * )
     *
     * @param int $stateId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $stateId, Request $request): Response
    {
        return $this->redirect(
            $this->getAdminLink(
                'AdminStates',
                ['updatestate' => '', 'id_state' => $stateId],
                true
            )
        );
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_states_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        return $this->redirect(
            $this->getAdminLink(
                'AdminStates',
                ['addstate' => ''],
                true
            )
        );
    }

    /**
     * Toggles state status
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_states_index")
     * @DemoRestricted(redirectRoute="admin_states_index")
     *
     * @param int $stateId
     *
     * @return JsonResponse
     */
    public function toggleStatusAction(int $stateId): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(
                new ToggleStateStatusCommand((int) $stateId)
            );
            $response = [
                'status' => true,
                'message' => $this->trans(
                    'The status has been successfully updated.',
                    'Admin.Notifications.Success'
                ),
            ];
        } catch (StateException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        return $this->json($response);
    }

    /**
     * Delete states in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_states_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteStateCommand($stateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Enables states on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_states_index")
     * @DemoRestricted(redirectRoute="admin_states_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleStateStatusCommand(true, $stateIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Disables states on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_states_index")
     * @DemoRestricted(redirectRoute="admin_states_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleStateStatusCommand(false, $stateIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * @return array
     */
    private function getToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_states_create'),
            'desc' => $this->trans('Add new state', 'Admin.International.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkStatesFromRequest(Request $request): array
    {
        $stateIds = $request->request->get('state_states_bulk');

        if (!is_array($stateIds)) {
            return [];
        }

        foreach ($stateIds as $i => $stateId) {
            $stateIds[$i] = (int) $stateId;
        }

        return $stateIds;
    }

    /**
     * @param Throwable|null $e
     *
     * @return array
     */
    private function getErrorMessages(?Throwable $e = null): array
    {
        return [
            StateNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
