<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use DomainException;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerAddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerAddressFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerFilters;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Is responsible for "Sell > Catalog > Brands & Suppliers" page.
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show manufacturers listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param ManufacturerFilters $manufacturerFilters
     * @param ManufacturerAddressFilters $manufacturerAddressFilters
     *
     * @return Response
     */
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
            'manufacturer_grid' => $this->presentGrid($manufacturerGrid),
            'manufacturer_address_grid' => $this->presentGrid($manufacturerAddressGrid),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        try {
            $manufacturerForm = $this->getFormBuilder()->getForm();
            $manufacturerForm->handleRequest($request);

            $result = $this->getFormHandler()->handle($manufacturerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/add.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
        ]);
    }

    /**
     * Show & process manufacturer editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param int $manufacturerId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, $manufacturerId)
    {
        try {
            $manufacturerForm = $this->getFormBuilder()->getFormFor((int) $manufacturerId);
            $manufacturerForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor((int) $manufacturerId, $manufacturerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ManufacturerNotFoundException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }

        /** @var EditableManufacturer $editableManufacturer */
        $editableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForEditing((int) $manufacturerId));

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
            'manufacturerName' => $editableManufacturer->getName(),
            'logoImage' => $editableManufacturer->getLogoImage(),
        ]);
    }

    /**
     * Deletes manufacturer
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param $manufacturerId
     *
     * @return RedirectResponse
     */
    public function deleteAction($manufacturerId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteManufacturerCommand((int) $manufacturerId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Deletes manufacturers on bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $manufacturerIds = $this->getBulkManufacturersFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteManufacturerCommand($manufacturerIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Enables manufacturers on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param int $manufacturerId
     *
     * @return RedirectResponse
     */
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

    public function exportAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Deletes address
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param int $addressId
     *
     * @return RedirectResponse
     */
    public function deleteAddressAction($addressId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAddressCommand((int) $addressId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (AddressException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    public function exportAddressAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Deletes adresses in bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_manufacturers_index")
     * @DemoRestricted(redirectRoute="admin_manufacturers_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAddressAction(Request $request)
    {
        $addressIds = $this->getBulkAddressesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteAddressCommand($addressIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (AddressException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Show & process address creation.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAddressAction(Request $request)
    {
        $addressFormBuilder = $this->getAddressFormBuilder();
        $addressFormHandler = $this->getAddressFormHandler();
        $addressForm = $addressFormBuilder->getForm();
        $addressForm->handleRequest($request);

        try {
            $result = $addressFormHandler->handle($addressForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }
        } catch (DomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/add.html.twig', [
            'addressForm' => $addressForm->createView(),
        ]);
    }

    /**
     * Show & process address editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param int $addressId
     * @param Request $request
     *
     * @return Response
     */
    public function editAddressAction(Request $request, $addressId)
    {
        try {
            $addressForm = $this->getAddressFormBuilder()->getFormFor((int) $addressId, [], [
                'is_editing' => true,
            ]);
            $addressForm->handleRequest($request);
            $result = $this->getAddressFormHandler()->handleFor((int) $addressId, $addressForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers_index');
            }

            /** @var EditableManufacturerAddress $editableAddress */
            $editableAddress = $this->getQueryBus()->handle(new GetManufacturerAddressForEditing((int) $addressId));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof AddressNotFoundException || $e instanceof AddressConstraintException) {
                return $this->redirectToRoute('admin_manufacturers_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/edit.html.twig', [
            'addressForm' => $addressForm->createView(),
            'address' => $editableAddress->getAddress(),
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
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
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            MemoryLimitException::class => $this->trans(
                    'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ',
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
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    'Admin.Notifications.Error'
                ),
            ],
            AddressNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkManufacturersFromRequest(Request $request)
    {
        $manufacturerIds = $request->request->get('manufacturer_bulk');

        if (!is_array($manufacturerIds)) {
            return [];
        }

        foreach ($manufacturerIds as $i => $manufacturerId) {
            $manufacturerIds[$i] = (int) $manufacturerId;
        }

        return $manufacturerIds;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkAddressesFromRequest(Request $request)
    {
        $addressIds = $request->request->get('manufacturer_address_bulk');

        if (!is_array($addressIds)) {
            return [];
        }

        foreach ($addressIds as $i => $addressId) {
            $addressIds[$i] = (int) $addressId;
        }

        return $addressIds;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.manufacturer_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_form_builder');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getAddressFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_address_form_builder');
    }
}
