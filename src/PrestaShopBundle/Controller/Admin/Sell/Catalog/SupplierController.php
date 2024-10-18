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
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotToggleSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\SupplierFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SupplierController is responsible for "Sell > Catalog > Brands & Suppliers > Suppliers" page.
 */
class SupplierController extends PrestaShopAdminController
{
    /**
     * Show suppliers listing.
     *
     * @param Request $request
     * @param SupplierFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.factory.supplier')]
        GridFactoryInterface $supplierGridFactory,
        SupplierFilters $filters
    ): Response {
        $supplierGrid = $supplierGridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Suppliers/index.html.twig',
            [
                'supplierGrid' => $this->presentGrid($supplierGrid),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'settingsTipMessage' => $this->getSettingsTipMessage(),
                'layoutHeaderToolbarBtn' => $this->getSupplierIndexToolbarButtons(),
            ]
        );
    }

    /**
     * Displays supplier creation form and handles form submit which creates new supplier.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to add this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.supplier_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.supplier_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $formData = [];
        if ($request->request->has('supplier') && isset($request->request->all('supplier')['id_country'])) {
            $formCountryId = (int) $request->request->all('supplier')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        $supplierForm = $formBuilder->getForm($formData);
        $supplierForm->handleRequest($request);

        try {
            $result = $formHandler->handle($supplierForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_suppliers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Suppliers/add.html.twig', [
            'supplierForm' => $supplierForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New supplier', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Deletes supplier.
     *
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_suppliers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to delete this.')]
    public function deleteAction($supplierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteSupplierCommand((int) $supplierId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk deletion of suppliers.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_suppliers_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to delete this.')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $suppliersToDelete = $request->request->all('supplier_bulk');

        try {
            $suppliersToDelete = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToDelete
            );
            $this->dispatchCommand(new BulkDeleteSupplierCommand($suppliersToDelete));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk disables supplier statuses.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_suppliers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to edit this.')]
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $suppliersToDisable = $request->request->all('supplier_bulk');

        try {
            $suppliersToDisable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToDisable
            );
            $this->dispatchCommand(new BulkDisableSupplierCommand($suppliersToDisable));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk enables supplier statuses.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_suppliers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to edit this.')]
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $suppliersToEnable = $request->request->all('supplier_bulk');

        try {
            $suppliersToEnable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToEnable
            );
            $this->dispatchCommand(new BulkEnableSupplierCommand($suppliersToEnable));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Displays edit supplier form and submits form.
     *
     * @param Request $request
     * @param int $supplierId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to edit this.')]
    public function editAction(
        Request $request,
        int $supplierId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.supplier_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.supplier_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $formData = [];
        if ($request->request->has('supplier') && isset($request->request->all('supplier')['id_country'])) {
            $formCountryId = (int) $request->request->all('supplier')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        try {
            /** @var EditableSupplier $editableSupplier */
            $editableSupplier = $this->dispatchQuery(new GetSupplierForEditing((int) $supplierId));

            $supplierForm = $formBuilder->getFormFor((int) $supplierId, $formData);
            $supplierForm->handleRequest($request);

            $result = $formHandler->handleFor((int) $supplierId, $supplierForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_suppliers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        if (!isset($supplierForm) || !isset($editableSupplier)) {
            return $this->redirectToRoute('admin_suppliers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Suppliers/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'supplierForm' => $supplierForm->createView(),
            'supplierName' => $editableSupplier->getName(),
            'logoImage' => $editableSupplier->getLogoImage(),
            'layoutTitle' => $this->trans(
                'Editing supplier %name%',
                [
                    '%name%' => $editableSupplier->getName(),
                ],
                'Admin.Navigation.Menu'
            ),
        ]);
    }

    /**
     * Toggles supplier active status.
     *
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_suppliers_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_suppliers_index', message: 'You do not have permission to edit this.')]
    public function toggleStatusAction(int $supplierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleSupplierStatusCommand((int) $supplierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Views supplier products information.
     *
     * @param Request $request
     * @param int $supplierId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function viewAction(
        Request $request,
        int $supplierId,
    ): Response {
        try {
            /** @var ViewableSupplier $viewableSupplier */
            $viewableSupplier = $this->dispatchQuery(new GetSupplierForViewing(
                (int) $supplierId,
                (int) $this->getLanguageContext()->getId()
            ));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_suppliers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Suppliers/view.html.twig', [
            'viewableSupplier' => $viewableSupplier,
            'isStockManagementEnabled' => $this->getConfiguration()->get('PS_STOCK_MANAGEMENT'),
            'isAllShopContext' => $this->getShopContext()->getShopConstraint()->isAllShopContext(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getSupplierViewToolbarButtons($supplierId),
            'layoutTitle' => $this->trans(
                'Supplier %name%',
                [
                    '%name%' => $viewableSupplier->getName(),
                ],
                'Admin.Navigation.Menu'
            ),
        ]);
    }

    /**
     * Exports to csv visible suppliers list data.
     *
     * @param SupplierFilters $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportAction(
        #[Autowire(service: 'prestashop.core.grid.factory.supplier')]
        GridFactoryInterface $supplierGridFactory,
        SupplierFilters $filters
    ): CsvResponse {
        $filters = new SupplierFilters(['limit' => null] + $filters->all());
        $supplierGrid = $supplierGridFactory->getGrid($filters);

        $headers = [
            'id_supplier' => $this->trans('ID', [], 'Admin.Global'),
            'name' => $this->trans('Name', [], 'Admin.Global'),
            'products_count' => $this->trans('Number of products', [], 'Admin.Catalog.Feature'),
            'active' => $this->trans('Enabled', [], 'Admin.Global'),
        ];

        $data = [];

        foreach ($supplierGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_supplier' => $record['id_supplier'],
                'name' => $record['name'],
                'products_count' => $record['products_count'],
                'active' => $record['active'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('supplier_' . date('Y-m-d_His') . '.csv')
        ;
    }

    /**
     * Deletes supplier logo image.
     *
     * @param Request $request
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_suppliers_edit', redirectQueryParamsToKeep: ['supplierId'])]
    public function deleteLogoImageAction(Request $request, int $supplierId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteSupplierLogoImageCommand($supplierId));
            $this->addFlash(
                'success',
                $this->trans('Image successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (SupplierException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_edit', [
            'supplierId' => $supplierId,
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            SupplierNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            AddressNotFoundException::class => $this->trans(
                'The address for this supplier has been deleted.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotToggleSupplierStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotUpdateSupplierStatusException::class => $this->trans(
                'An error occurred while updating the status for an object.',
                [],
                'Admin.Notifications.Error'
            ),
            SupplierConstraintException::class => [
                SupplierConstraintException::INVALID_BULK_DATA => $this->trans(
                    'You must select at least one element to delete.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteSupplierException::class => [
                CannotDeleteSupplierException::HAS_PENDING_ORDERS => $this->trans(
                    'It is not possible to delete a supplier if there are pending supplier orders.',
                    [],
                    'Admin.Catalog.Notification'
                ),
                CannotDeleteSupplierException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotDeleteSupplierException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            MemoryLimitException::class => $this->trans(
                'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.',
                [],
                'Admin.Notifications.Error'
            ),
            ImageUploadException::class => $this->trans(
                'An error occurred while uploading the image.',
                [],
                'Admin.Notifications.Error'
            ),
            ImageOptimizationException::class => $this->trans(
                'Unable to resize one or more of your pictures.',
                [],
                'Admin.Catalog.Notification'
            ),
            UploadedImageConstraintException::class => [
                UploadedImageConstraintException::EXCEEDED_SIZE => $this->trans(
                    'Maximum image size: %s.',
                    [$this->getIniConfiguration()->getUploadMaxSizeInBytes()],
                    'Admin.Global'
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png, .webp',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return string|null
     */
    protected function getSettingsTipMessage()
    {
        if ($this->getConfiguration()->get('PS_DISPLAY_SUPPLIERS')) {
            return null;
        }

        $urlOpening = sprintf('<a href="%s">', $this->generateUrl('admin_preferences'));
        $urlEnding = '</a>';

        return $this->trans(
            'The display of your suppliers is disabled on your store. Go to %sShop Parameters > General%s to edit settings.',
            [$urlOpening, $urlEnding],
            'Admin.Catalog.Notification'
        );
    }

    /**
     * @return array
     */
    private function getSupplierIndexToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_suppliers_create'),
            'desc' => $this->trans('Add new supplier', [], 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * @param int $supplierId
     *
     * @return array
     */
    private function getSupplierViewToolbarButtons(int $supplierId): array
    {
        $toolbarButtons = [];

        $toolbarButtons['edit'] = [
            'href' => $this->generateUrl('admin_suppliers_edit', ['supplierId' => $supplierId]),
            'desc' => $this->trans('Edit supplier', [], 'Admin.Catalog.Feature'),
            'icon' => 'mode_edit',
        ];

        return $toolbarButtons;
    }
}
