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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkDeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\DeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotEditZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotToggleZoneStatusException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\DeleteZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\MissingZoneRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShop\PrestaShop\Core\Domain\Zone\QueryResult\EditableZone;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ZoneGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\ZoneFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ZoneController is responsible for handling "Improve > International > Locations > Zones"
 */
class ZoneController extends FrameworkBundleAdminController
{
    /**
     * Show all zones.
     *
     * @param Request $request
     * @param ZoneFilters $zoneFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, ZoneFilters $zoneFilters): Response
    {
        $zoneGridFactory = $this->get('prestashop.core.grid.factory.zone');
        $zoneGrid = $zoneGridFactory->getGrid($zoneFilters);

        return $this->render('@PrestaShop/Admin/Improve/International/Zone/index.html.twig', [
            'zoneGrid' => $this->presentGrid($zoneGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutHeaderToolbarBtn' => $this->getZoneToolbarButtons(),
        ]);
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(Request $request): RedirectResponse
    {
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.zone'),
            $request,
            ZoneGridDefinitionFactory::GRID_ID,
            'admin_zones_index'
        );
    }

    /**
     * Show "Add new" zone form and handles its submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index', message: 'You need permission to create new zone.')]
    public function createAction(Request $request): Response
    {
        $zoneFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.zone_form_builder');
        $zoneFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.zone_form_handler');

        $zoneForm = $zoneFormBuilder->getForm();
        $zoneForm->handleRequest($request);

        try {
            $handleResult = $zoneFormHandler->handle($zoneForm);

            if (null !== $handleResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_zones_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Zone/create.html.twig', [
            'zoneForm' => $zoneForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New zone', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Displays zone edit for and handles its submit.
     *
     * @param int $zoneId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index', message: 'You need permission to edit this.')]
    public function editAction(int $zoneId, Request $request): Response
    {
        try {
            /** @var EditableZone $editableZone */
            $editableZone = $this->getQueryBus()->handle(new GetZoneForEditing($zoneId));

            $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.zone_form_builder');
            $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.zone_form_handler');

            $zoneForm = $formBuilder->getFormFor($zoneId);
            $zoneForm->handleRequest($request);

            $result = $formHandler->handleFor($zoneId, $zoneForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_zones_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            if ($e instanceof ZoneNotFoundException) {
                return $this->redirectToRoute('admin_zones_index');
            }
        }

        if (!isset($zoneForm)) {
            return $this->redirectToRoute('admin_zones_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Zone/edit.html.twig', [
            'zoneName' => $editableZone->getName(),
            'zoneForm' => $zoneForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing zone %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $editableZone->getName(),
                ]
            ),
        ]);
    }

    /**
     * Deletes zone.
     *
     * @param int $zoneId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_zones_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index', message: 'You need permission to delete this.')]
    public function deleteAction(int $zoneId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteZoneCommand($zoneId));
            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (ZoneException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_zones_index');
        }

        return $this->redirectToRoute('admin_zones_index');
    }

    /**
     * Toggles zone active status.
     *
     * @param int $zoneId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_zones_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index', message: 'You do not have permission to edit this.')]
    public function toggleStatusAction(int $zoneId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleZoneStatusCommand($zoneId));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_zones_index');
    }

    /**
     * Deletes zones in bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_zones_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $zoneIds = $this->getBulkZonesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteZoneCommand($zoneIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (ZoneException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_zones_index');
    }

    /**
     * Bulk toggles zones status.
     *
     * @param string $status
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_zones_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_zones_index')]
    public function bulkToggleStatus(string $status, Request $request): RedirectResponse
    {
        $status = $status === 'enable';
        $zoneIds = $this->getBulkZonesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleZoneStatusCommand($status, $zoneIds));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ZoneException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_zones_index');
    }

    /**
     * Returns zone error messages mapping.
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            CannotEditZoneException::class => $this->trans(
                'An error occurred while editing the zone.',
                'Admin.International.Notification'
            ),
            MissingZoneRequiredFieldsException::class => $this->trans(
                'The %s field is required.',
                'Admin.Notifications.Error',
                [
                    implode(
                        ', ',
                        $e instanceof MissingZoneRequiredFieldsException ? $e->getMissingRequiredFields() : []
                    ),
                ]
            ),
            ZoneNotFoundException::class => $this->trans(
                'This zone does not exist.',
                'Admin.Notifications.Error'
            ),
            CannotToggleZoneStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            DeleteZoneException::class => [
                DeleteZoneException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteZoneException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * Collects zone IDs from request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkZonesFromRequest(Request $request): array
    {
        $zoneIds = $request->request->all('zone_bulk');

        return array_map('intval', $zoneIds);
    }

    /**
     * @return array
     */
    private function getZoneToolbarButtons(): array
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_zones_create'),
                'desc' => $this->trans('Add new zone', 'Admin.International.Feature'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }
}
