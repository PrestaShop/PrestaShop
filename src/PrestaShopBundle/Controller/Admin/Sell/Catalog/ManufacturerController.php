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
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressFieldException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerAddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerAddressFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Manages "Sell > Catalog > Brands & Suppliers > Brands" page
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show manufacturers listing page.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ManufacturerFilters $manufacturerFilters,
        ManufacturerAddressFilters $manufacturerAddressFilters
    ) {
        $manufacturerGridFactory = $this->get('prestashop.core.grid.grid_factory.manufacturer');
        $manufacturerGrid = $manufacturerGridFactory->getGrid($manufacturerFilters);

        $manufacturerAddressFactory = $this->get('prestashop.core.grid.grid_factory.manufacturer_address');
        $manufacturerAddressGrid = $manufacturerAddressFactory->getGrid($manufacturerAddressFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'manufacturerGrid' => $this->presentGrid($manufacturerGrid),
            'manufacturerAddressGrid' => $this->presentGrid($manufacturerAddressGrid),
            'settingsTipMessage' => $this->getSettingsTipMessage(),
            'layoutHeaderToolbarBtn' => $this->getManufacturerIndexToolbarButtons(),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(Request $request)
    {
        $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.manufacturer';
        $filterId = ManufacturerGridDefinitionFactory::GRID_ID;
        if ($request->request->has(ManufacturerAddressGridDefinitionFactory::GRID_ID)) {
            $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.manufacturer_address';
            $filterId = ManufacturerAddressGridDefinitionFactory::GRID_ID;
        }

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'admin_manufacturers_index'
        );
    }

    /**
     * Show & process manufacturer creation.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(Request $request)
    {
        $manufacturerForm = $this->getFormBuilder()->getForm();
        $manufacturerForm->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($manufacturerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/add.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
            'layoutTitle' => $this->trans('New brand', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * View single manufacturer details
     *
     * @param int $manufacturerId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function viewAction(Request $request, $manufacturerId)
    {
        try {
            /** @var ViewableManufacturer $viewableManufacturer */
            $viewableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForViewing(
                (int) $manufacturerId,
                (int) $this->getContextLangId()
            ));
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_manufacturers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/view.html.twig', [
            'viewableManufacturer' => $viewableManufacturer,
            'isStockManagementEnabled' => $this->getConfiguration()->get('PS_STOCK_MANAGEMENT'),
            'isAllShopContext' => $this->get('prestashop.adapter.shop.context')->isAllShopContext(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutHeaderToolbarBtn' => $this->getManufacturerViewToolbarButtons($manufacturerId),
            'layoutTitle' => $this->trans(
                'Brand %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $viewableManufacturer->getName(),
                ]
            ),
        ]);
    }

    /**
     * Show & process manufacturer editing.
     *
     * @param int $manufacturerId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(Request $request, $manufacturerId)
    {
        try {
            /** @var EditableManufacturer $editableManufacturer */
            $editableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForEditing((int) $manufacturerId));

            $manufacturerForm = $this->getFormBuilder()->getFormFor((int) $manufacturerId);
            $manufacturerForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor((int) $manufacturerId, $manufacturerForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ManufacturerNotFoundException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }

        if (!isset($editableManufacturer) || !isset($manufacturerForm)) {
            return $this->redirectToRoute('admin_manufacturers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
            'manufacturerName' => $editableManufacturer->getName(),
            'logoImage' => $editableManufacturer->getLogoImage(),
            'layoutTitle' => $this->trans(
                'Editing brand %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $editableManufacturer->getName(),
                ]
            ),
        ]);
    }

    /**
     * Deletes manufacturer
     *
     * @param int|string $manufacturerId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function deleteAction($manufacturerId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteManufacturerCommand((int) $manufacturerId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Deletes manufacturers on bulk action
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function bulkDeleteAction(Request $request)
    {
        $manufacturerIds = $this->getBulkManufacturersFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteManufacturerCommand($manufacturerIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Enables manufacturers on bulk action
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function bulkEnableAction(Request $request)
    {
        $manufacturerIds = $this->getBulkManufacturersFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleManufacturerStatusCommand($manufacturerIds, true));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Disables manufacturers on bulk action
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function bulkDisableAction(Request $request)
    {
        $manufacturerIds = $this->getBulkManufacturersFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleManufacturerStatusCommand($manufacturerIds, false));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Toggles manufacturer status
     *
     * @param int $manufacturerId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function toggleStatusAction($manufacturerId)
    {
        try {
            /** @var EditableManufacturer $editableManufacturer */
            $editableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForEditing((int) $manufacturerId));
            $this->getCommandBus()->handle(
                new ToggleManufacturerStatusCommand((int) $manufacturerId, !$editableManufacturer->isEnabled())
            );
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Export filtered manufacturers.
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function exportAction(ManufacturerFilters $filters)
    {
        $filters = new ManufacturerFilters(['limit' => null] + $filters->all());
        $manufacturersGridFactory = $this->get('prestashop.core.grid.grid_factory.manufacturer');
        $manufacturersGrid = $manufacturersGridFactory->getGrid($filters);

        $headers = [
            'id_manufacturer' => $this->trans('ID', 'Admin.Global'),
            'logo' => $this->trans('Logo', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'addresses_count' => $this->trans('Addresses', 'Admin.Global'),
            'products_count' => $this->trans('Products', 'Admin.Global'),
            'active' => $this->trans('Enabled', 'Admin.Global'),
        ];

        $data = [];

        foreach ($manufacturersGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_manufacturer' => $record['id_manufacturer'],
                'logo' => $record['logo'],
                'name' => $record['name'],
                'addresses_count' => $record['addresses_count'],
                'products_count' => $record['products_count'],
                'active' => $record['active'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('brands_' . date('Y-m-d_His') . '.csv')
            ;
    }

    /**
     * Deletes manufacturer logo image.
     *
     * @param Request $request
     * @param int $manufacturerId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_manufacturers_edit', redirectQueryParamsToKeep: ['manufacturerId'])]
    public function deleteLogoImageAction(Request $request, int $manufacturerId): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete-logo-thumbnail', $request->request->get('_csrf_token'))) {
            return $this->redirectToRoute('admin_security_compromised', [
                'uri' => $this->generateUrl('admin_manufacturers_edit', [
                    'manufacturerId' => $manufacturerId,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }

        try {
            $this->getCommandBus()->handle(new DeleteManufacturerLogoImageCommand($manufacturerId));
            $this->addFlash(
                'success',
                $this->trans('Image successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_edit', [
            'manufacturerId' => $manufacturerId,
        ]);
    }

    /**
     * Deletes address
     *
     * @param int $addressId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function deleteAddressAction($addressId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAddressCommand((int) $addressId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (AddressException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Export filtered manufacturer addresses.
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function exportAddressAction(ManufacturerAddressFilters $filters)
    {
        $addressesGridFactory = $this->get('prestashop.core.grid.grid_factory.manufacturer_address');
        $addressesGrid = $addressesGridFactory->getGrid($filters);

        $headers = [
            'id_address' => $this->trans('ID', 'Admin.Global'),
            'name' => $this->trans('Brand', 'Admin.Global'),
            'firstname' => $this->trans('First name', 'Admin.Global'),
            'lastname' => $this->trans('Last name', 'Admin.Global'),
            'postcode' => $this->trans('Zip/Postal code', 'Admin.Global'),
            'city' => $this->trans('City', 'Admin.Global'),
            'country' => $this->trans('Country', 'Admin.Global'),
        ];

        $data = [];

        foreach ($addressesGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_address' => $record['id_address'],
                'name' => $record['name'],
                'firstname' => $record['firstname'],
                'lastname' => $record['lastname'],
                'postcode' => $record['postcode'],
                'city' => $record['city'],
                'country' => $record['country'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('address_' . date('Y-m-d_His') . '.csv')
            ;
    }

    /**
     * Deletes adresses in bulk action
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_manufacturers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_manufacturers_index')]
    public function bulkDeleteAddressAction(Request $request)
    {
        $addressIds = $this->getBulkAddressesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteAddressCommand($addressIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (AddressException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Show & process address creation.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAddressAction(Request $request)
    {
        $addressFormBuilder = $this->getAddressFormBuilder();
        $addressFormHandler = $this->getAddressFormHandler();

        $formData = [];
        if ($request->request->has('manufacturer_address') && isset($request->request->all('manufacturer_address')['id_country'])) {
            $formCountryId = (int) $request->request->all('manufacturer_address')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        $addressForm = $addressFormBuilder->getForm($formData);
        $addressForm->handleRequest($request);

        try {
            $result = $addressFormHandler->handle($addressForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (DomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ManufacturerConstraintException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New brand address', 'Admin.Navigation.Menu'),
            'addressForm' => $addressForm->createView(),
        ]);
    }

    /**
     * Show & process address editing.
     *
     * @param int $addressId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAddressAction(Request $request, $addressId)
    {
        $addressId = (int) $addressId;

        $addressFormBuilder = $this->getAddressFormBuilder();
        $addressFormHandler = $this->getAddressFormHandler();

        $formData = [];
        if ($request->request->has('manufacturer_address') && isset($request->request->all('manufacturer_address')['id_country'])) {
            $formCountryId = (int) $request->request->all('manufacturer_address')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        try {
            /** @var EditableManufacturerAddress $editableAddress */
            $editableAddress = $this->getQueryBus()->handle(new GetManufacturerAddressForEditing($addressId));
            $addressForm = $addressFormBuilder->getFormFor($addressId, $formData);
            $addressForm->handleRequest($request);

            $result = $addressFormHandler->handleFor($addressId, $addressForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof AddressNotFoundException || $e instanceof AddressConstraintException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }

        if (!isset($editableAddress) || !isset($addressForm)) {
            return $this->redirectToRoute('admin_manufacturers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Editing brand address', 'Admin.Navigation.Menu'),
            'addressForm' => $addressForm->createView(),
            'address' => $editableAddress->getAddress(),
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        $iniConfig = $this->get('prestashop.core.configuration.ini_configuration');

        return [
            DeleteManufacturerException::class => [
                DeleteManufacturerException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteManufacturerException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            UpdateManufacturerException::class => [
                UpdateManufacturerException::FAILED_BULK_UPDATE_STATUS => [
                    $this->trans(
                        'An error occurred while updating the status.',
                        'Admin.Notifications.Error'
                    ),
                ],
                UpdateManufacturerException::FAILED_UPDATE_STATUS => [
                    $this->trans(
                        'An error occurred while updating the status for an object.',
                        'Admin.Notifications.Error'
                    ),
                ],
            ],
            DeleteAddressException::class => [
                DeleteAddressException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteAddressException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            ManufacturerNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            MemoryLimitException::class => $this->trans(
                    'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.',
                    'Admin.Notifications.Error'
            ),
            ImageUploadException::class => $this->trans(
                'An error occurred while uploading the image.',
                'Admin.Notifications.Error'
            ),
            ImageOptimizationException::class => $this->trans(
                'Unable to resize one or more of your pictures.',
                'Admin.Catalog.Notification'
            ),
            UploadedImageConstraintException::class => [
                UploadedImageConstraintException::EXCEEDED_SIZE => $this->trans(
                    'Max file size allowed is "%s" bytes.', 'Admin.Notifications.Error', [
                        $iniConfig->getUploadMaxSizeInBytes(),
                    ]),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png, .webp',
                    'Admin.Notifications.Error'
                ),
            ],
            AddressNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            InvalidAddressFieldException::class => $this->trans(
                'Address fields contain invalid values.',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function getBulkManufacturersFromRequest(Request $request): array
    {
        $manufacturerIds = $request->request->all('manufacturer_bulk');

        foreach ($manufacturerIds as $i => $manufacturerId) {
            $manufacturerIds[$i] = (int) $manufacturerId;
        }

        return $manufacturerIds;
    }

    /**
     * @return array<int, int>
     */
    private function getBulkAddressesFromRequest(Request $request): array
    {
        $addressIds = $request->request->all('manufacturer_address_bulk');

        foreach ($addressIds as $i => $addressId) {
            $addressIds[$i] = (int) $addressId;
        }

        return $addressIds;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.manufacturer_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_form_builder');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getAddressFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_address_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getAddressFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.manufacturer_address_form_handler');
    }

    /**
     * @return string
     */
    private function getSettingsTipMessage(): string
    {
        $urlOpening = sprintf('<a href="%s">', $this->get('router')->generate('admin_preferences'));
        $urlEnding = '</a>';

        if ($this->getConfiguration()->get('PS_DISPLAY_MANUFACTURERS')) {
            return $this->trans(
                'The display of your brands is enabled on your store. Go to %sShop Parameters > General%s to edit settings.',
                'Admin.Catalog.Notification',
                [$urlOpening, $urlEnding]
            );
        }

        return $this->trans(
            'The display of your brands is disabled on your store. Go to %sShop Parameters > General%s to edit settings.',
            'Admin.Catalog.Notification',
            [$urlOpening, $urlEnding]
        );
    }

    /**
     * @return array
     */
    private function getManufacturerIndexToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add_manufacturer'] = [
            'href' => $this->generateUrl('admin_manufacturers_create'),
            'desc' => $this->trans('Add new brand', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        $toolbarButtons['add_manufacturer_address'] = [
            'href' => $this->generateUrl('admin_manufacturer_addresses_create'),
            'desc' => $this->trans('Add new brand address', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * @param int $manufacturerId
     *
     * @return array
     */
    private function getManufacturerViewToolbarButtons(int $manufacturerId): array
    {
        $toolbarButtons = [];

        $toolbarButtons['edit'] = [
            'href' => $this->generateUrl('admin_manufacturers_edit', ['manufacturerId' => $manufacturerId]),
            'desc' => $this->trans('Edit brand', 'Admin.Catalog.Feature'),
            'icon' => 'mode_edit',
        ];

        return $toolbarButtons;
    }
}
