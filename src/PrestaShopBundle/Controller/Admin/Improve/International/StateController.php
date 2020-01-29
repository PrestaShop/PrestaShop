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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotAddStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            return $this->json(
                [
                    'message' => $this->getErrorMessageForException($e, []),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $link = $this->getAdminLink('AdminStates', [], true);

        return $this->redirect($link);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_states_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $stateFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.state_form_builder'
        );
        $stateFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.state_form_handler'
        );

        $stateForm = $stateFormBuilder->getForm();
        $stateForm->handleRequest($request);

        try {
            $handlerResult = $stateFormHandler->handle($stateForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_states_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/State/add.html.twig', [
            'enableSidebar' => true,
            'stateForm' => $stateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
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
        try {
            /** @var EditableState $editableState */
            $editableState = $this->getQueryBus()->handle(new GetStateForEditing((int) $stateId));
        } catch (StateException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_states_index');
        }

        $stateForm = null;

        try {
            $stateFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.state_form_builder'
            );

            $stateFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.state_form_handler'
            );

            $stateForm = $stateFormBuilder->getFormFor((int) $stateId);
            $stateForm->handleRequest($request);
            $result = $stateFormHandler->handleFor((int) $stateId, $stateForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

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
            'layoutTitle' => $this->trans('Edit: %value%', 'Admin.Actions', ['%value%' => $editableState->getName()]),
            'stateForm' => $stateForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            StateException::class => $this->trans(
                'Unexpected error occurred.',
                'Admin.Notifications.Error'
            ),
            StateNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            StateConstraintException::class => [
                StateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
                StateConstraintException::INVALID_FIELD_VALUES => $this->trans(
                    'An error occurred when attempting to update the required fields.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotUpdateStateException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CannotAddStateException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            ZoneNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            ZoneConstraintException::class => [
                ZoneConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
