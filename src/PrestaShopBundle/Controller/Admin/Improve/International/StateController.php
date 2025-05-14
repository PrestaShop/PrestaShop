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
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CountryStateByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotAddStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\StateFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling country states data
 */
class StateController extends PrestaShopAdminController
{
    /**
     * Provides country states in json response
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', request.get('AdminCustomers')) || is_granted('update', request.get('AdminCustomers')) || is_granted('create', request.get('AdminManufacturers')) || is_granted('update', request.get('AdminManufacturers')) || is_granted('create', request.get('AdminSuppliers')) || is_granted('update', request.get('AdminSuppliers'))")]
    public function getStatesAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.form.choice_provider.country_state_by_id')]
        CountryStateByIdChoiceProvider $statesProvider
    ): JsonResponse {
        try {
            $countryId = (int) $request->query->get('id_country');
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
     * Provides country states select input options for legacy pages
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', request.get('AdminTaxRulesGroup')) || is_granted('update', request.get('AdminTaxRulesGroup')) || is_granted('create', request.get('AdminStores')) || is_granted('update', request.get('AdminStores'))")]
    public function getLegacyStatesOptionsAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.form.choice_provider.country_state_by_id')]
        CountryStateByIdChoiceProvider $statesProvider
    ): Response {
        try {
            $countryId = (int) $request->query->get('id_country');
            $states = $statesProvider->getChoices([
                'id_country' => $countryId,
            ]);

            if (!empty($states)) {
                $htmlResponse = '';
                if ($request->query->get('no_empty')) {
                    $emptyValue = $request->get('empty_value') ?: '-';
                    $htmlResponse = '<option value="0">' . htmlentities($emptyValue, ENT_QUOTES, 'utf-8') . '</option>' . "\n";
                }

                $queryStateId = (int) $request->query->get('id_state');
                foreach ($states as $stateName => $stateId) {
                    $htmlResponse .= '<option value="' . $stateId . '"' . ($queryStateId == $stateId ? ' selected="selected"' : '') . '>' . $stateName . '</option>' . "\n";
                }

                return new Response($htmlResponse);
            }
        } catch (Exception $e) {
            return $this->json([
                'message' => $this->getErrorMessageForException($e, []),
            ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new Response('false');
    }

    /**
     * Show states listing page
     *
     * @param Request $request
     * @param StateFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        StateFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.state')]
        GridFactoryInterface $gridFactory
    ): Response {
        $stateGrid = $gridFactory->getGrid($filters);

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
     * @param int $stateId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_states_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index')]
    public function deleteAction(int $stateId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteStateCommand($stateId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Handles edit form rendering and submission
     *
     * @param int $stateId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index')]
    public function editAction(
        int $stateId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.state_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.state_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        try {
            /** @var EditableState $editableState */
            $editableState = $this->dispatchQuery(new GetStateForEditing((int) $stateId));
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_states_index');
        }

        $stateForm = null;

        try {
            $stateForm = $formBuilder->getFormFor((int) $stateId);
            $stateForm->handleRequest($request);
            $result = $formHandler->handleFor((int) $stateId, $stateForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_states_index');
            }
        } catch (StateNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_states_index');
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/State/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Editing state %value%', ['%value%' => $editableState->getName()], 'Admin.Navigation.Menu'),
            'stateForm' => $stateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index', message: 'You do not have permission to create this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.state_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.state_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $stateForm = $formBuilder->getForm();
        $stateForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($stateForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_states_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/State/add.html.twig', [
            'enableSidebar' => true,
            'stateForm' => $stateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'layoutTitle' => $this->trans('New state', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Toggles state status
     *
     * @param int $stateId
     *
     * @return JsonResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_states_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index')]
    public function toggleStatusAction(int $stateId): JsonResponse
    {
        try {
            $this->dispatchCommand(
                new ToggleStateStatusCommand((int) $stateId)
            );
            $response = [
                'status' => true,
                'message' => $this->trans(
                    'The status has been successfully updated.',
                    [],
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index', message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteStateCommand($stateIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Enables states on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_states_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index')]
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkToggleStateStatusCommand(true, $stateIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_states_index');
    }

    /**
     * Disables states on bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_states_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_states_index')]
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $stateIds = $this->getBulkStatesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkToggleStateStatusCommand(false, $stateIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
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
            'desc' => $this->trans('Add new state', [], 'Admin.International.Feature'),
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
        $stateIds = $request->request->all('state_states_bulk');

        foreach ($stateIds as $i => $stateId) {
            $stateIds[$i] = (int) $stateId;
        }

        return $stateIds;
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            StateException::class => $this->trans(
                'An unexpected error occurred.',
                [],
                'Admin.Notifications.Error'
            ),
            StateNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            StateConstraintException::class => [
                StateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotUpdateStateException::class => $this->trans(
                'An error occurred while attempting to save.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotAddStateException::class => $this->trans(
                'An error occurred while attempting to save.',
                [],
                'Admin.Notifications.Error'
            ),
            ZoneNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            ZoneException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                [],
                'Admin.Notifications.Error'
            ),
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
