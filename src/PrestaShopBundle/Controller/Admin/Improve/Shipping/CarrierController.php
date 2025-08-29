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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use PrestaShop\PrestaShop\Adapter\Carrier\CarrierModuleAdviceAlertChecker;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkDeleteCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkToggleCarrierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\DeleteCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\ToggleCarrierIsFreeCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\ToggleCarrierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotDeleteCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierIsFreeStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CarrierGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Search\Filters\CarrierFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Improve > Shipping > Carriers" page.
 */
class CarrierController extends PrestaShopAdminController
{
    /**
     * Show carriers listing page
     *
     * @param Request $request
     * @param CarrierFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.factory.carrier')]
        GridFactoryInterface $carrierGridFactory,
        CarrierFilters $filters,
        CarrierModuleAdviceAlertChecker $carrierModuleAdviceAlertChecker,
        EmployeeContext $employeeContext,
    ): Response {
        $carrierGrid = $carrierGridFactory->getGrid($filters);
        $showHeaderAlert = $carrierModuleAdviceAlertChecker->isAlertDisplayed();

        $showcaseCardIsClose = $this->dispatchQuery(
            new GetShowcaseCardIsClosed($employeeContext->getEmployee()->getId(), ShowcaseCard::CARRIERS_CARD)
        );

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carriers/index.html.twig', [
            'carrierGrid' => $this->presentGrid($carrierGrid),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'showHeaderAlert' => $showHeaderAlert,
            'showcaseCardName' => ShowcaseCard::CARRIERS_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClose,
            'layoutHeaderToolbarBtn' => $this->getLayoutHeaderToolbarButtons(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Carriers', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Process Grid search.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.definition.factory.carrier')]
        GridDefinitionFactoryInterface $gridDefinitionFactory,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            CarrierGridDefinitionFactory::GRID_ID,
            'admin_carriers_index'
        );
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.carrier_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.carrier_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $result = $formHandler->handle($form);

        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_carriers_edit', ['carrierId' => $result->getIdentifiableObjectId()]);
        }

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carriers/form.html.twig', [
            'layoutTitle' => $this->trans('New Carrier', [], 'Admin.Navigation.Menu'),
            'carrierForm' => $form->createView(),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(
        int $carrierId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.carrier_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.carrier_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formBuilder->getFormFor($carrierId);
        $form->handleRequest($request);
        $result = $formHandler->handleFor($carrierId, $form);

        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_carriers_edit', ['carrierId' => $result->getIdentifiableObjectId()]);
        }

        try {
            $editableCarrier = $this->dispatchQuery(new GetCarrierForEditing(
                (int) $carrierId,
                ShopConstraint::allShops()
            ));
        } catch (CarrierNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_carriers_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carriers/form.html.twig', [
            'layoutTitle' => $this->trans(
                'Editing carrier %name%',
                ['%name%' => $editableCarrier->getName()],
                'Admin.Navigation.Menu',
            ),
            'carrierForm' => $form->createView(),
        ]);
    }

    /**
     * Deletes carrier.
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index')]
    public function deleteAction(int $carrierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteCarrierCommand($carrierId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Toggles carrier status.
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index', message: 'You need permission to edit this.')]
    public function toggleStatusAction(int $carrierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleCarrierStatusCommand($carrierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Toggles carrier is-free status
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index', message: 'You need permission to edit this.')]
    public function toggleIsFreeAction(int $carrierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleCarrierIsFreeCommand($carrierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Changes carrier position
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index', message: 'You need permission to edit this.')]
    public function updatePositionAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.carrier.position_definition')]
        PositionDefinition $positionDefinition,
    ): RedirectResponse {
        try {
            $this->updateGridPosition($positionDefinition, [
                'positions' => $request->request->all('positions'),
            ]);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Bulk deletes carriers.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteCarrierCommand($carrierIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Enables carrier status on bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index')]
    public function bulkEnableStatusAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->dispatchCommand(new BulkToggleCarrierStatusCommand($carrierIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Disables carrier status on bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_carriers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_carriers_index')]
    public function bulkDisableStatusAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->dispatchCommand(new BulkToggleCarrierStatusCommand($carrierIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    private function getErrorMessages(): array
    {
        return [
            CarrierNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotToggleCarrierStatusException::class => [
                CannotToggleCarrierStatusException::SINGLE_TOGGLE => $this->trans(
                    'An error occurred while updating the status for an object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotToggleCarrierStatusException::BULK_TOGGLE => $this->trans(
                    'An error occurred while updating the status.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteCarrierException::class => [
                CannotDeleteCarrierException::SINGLE_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotDeleteCarrierException::BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotToggleCarrierIsFreeStatusException::class => $this->trans(
                'An error occurred while updating the free shipping status.',
                [],
                'Admin.Shipping.Notification'
            ),
        ];
    }

    private function getLayoutHeaderToolbarButtons(): array
    {
        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_carriers_create'),
            'desc' => $this->trans('Add new carrier', [], 'Admin.Shipping.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Get carrier IDs from request for bulk actions.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getCarrierIdsFromRequest(Request $request): array
    {
        $carrierIds = $request->request->all('carrier_bulk');

        foreach ($carrierIds as $i => $carrierId) {
            $carrierIds[$i] = (int) $carrierId;
        }

        return $carrierIds;
    }
}
