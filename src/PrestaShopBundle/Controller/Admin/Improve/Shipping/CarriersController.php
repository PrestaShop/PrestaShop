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
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CarrierGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CarrierFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Improve > Shipping > Carriers" page.
 */
class CarriersController extends FrameworkBundleAdminController
{
    /**
     * Show carriers listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CarrierFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CarrierFilters $filters): Response
    {
        $carrierGridFactory = $this->get('prestashop.core.grid.factory.carrier');
        $carrierGrid = $carrierGridFactory->getGrid($filters);

        $showHeaderAlert = $this->get('prestashop.adapter.carrier.carrier_module_advice_alert_checker')->isAlertDisplayed();

        $showcaseCardIsClose = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $this->getContext()->employee->id, ShowcaseCard::CARRIERS_CARD)
        );

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carriers/index.html.twig', [
            'carrierGrid' => $this->presentGrid($carrierGrid),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'showHeaderAlert' => $showHeaderAlert,
            'showcaseCardName' => ShowcaseCard::CARRIERS_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClose,
            'layoutHeaderToolbarBtn' => $this->getLayoutHeaderToolbarButtons(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Carriers', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Process Grid search.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.carrier'),
            $request,
            CarrierGridDefinitionFactory::GRID_ID,
            'admin_carriers_index'
        );
    }

    /**
     * Redirect to carrier wizard for carrier editing.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    public function editAction(int $carrierId): RedirectResponse
    {
        return $this->redirect($this->getAdminLink('AdminCarrierWizard', ['id_carrier' => $carrierId]));
    }

    /**
     * Deletes carrier.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     * )
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $carrierId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCarrierCommand($carrierId));
            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Toggles carrier status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     *     message="You need permission to edit this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $carrierId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleCarrierStatusCommand($carrierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Toggles carrier is-free status
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     *     message="You need permission to edit this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param int $carrierId
     *
     * @return RedirectResponse
     */
    public function toggleIsFreeAction(int $carrierId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleCarrierIsFreeCommand($carrierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Changes carrier position
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     *     message="You need permission to edit this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updatePositionAction(Request $request): RedirectResponse
    {
        $positionsData = [
            'positions' => $request->request->get('positions'),
        ];

        $positionDefinition = $this->get('prestashop.core.grid.carrier.position_definition');
        $positionUpdateFactory = $this->get(PositionUpdateFactoryInterface::class);

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get(GridPositionUpdaterInterface::class);
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Bulk deletes carriers.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     * )
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCarrierCommand($carrierIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Enables carrier status on bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     * )
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableStatusAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleCarrierStatusCommand($carrierIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CarrierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_carriers_index');
    }

    /**
     * Disables carrier status on bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_carriers_index",
     * )
     * @DemoRestricted(redirectRoute="admin_carriers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableStatusAction(Request $request): RedirectResponse
    {
        $carrierIds = $this->getCarrierIdsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleCarrierStatusCommand($carrierIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
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
                'Admin.Notifications.Error'
            ),
            CannotToggleCarrierStatusException::class => [
                CannotToggleCarrierStatusException::SINGLE_TOGGLE => $this->trans(
                    'An error occurred while updating the status for an object.',
                    'Admin.Notifications.Error'
                ),
                CannotToggleCarrierStatusException::BULK_TOGGLE => $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteCarrierException::class => [
                CannotDeleteCarrierException::SINGLE_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteCarrierException::BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotToggleCarrierIsFreeStatusException::class => $this->trans(
                'An error occurred while updating the free shipping status.',
                'Admin.Shipping.Notification'
            ),
        ];
    }

    private function getLayoutHeaderToolbarButtons(): array
    {
        $toolbarButtons['add'] = [
            'href' => $this->getAdminLink('AdminCarrierWizard', []),
            'desc' => $this->trans('Add new carrier', 'Admin.Shipping.Feature'),
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
