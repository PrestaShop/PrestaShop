<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotToggleSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\SupplierFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SupplierController is responsible for "Sell > Catalog > Brands & Suppliers > Suppliers" page.
 */
class SupplierController extends FrameworkBundleAdminController
{
    /**
     * Show suppliers listing.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param SupplierFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, SupplierFilters $filters)
    {
        $supplierGridFactory = $this->get('prestashop.core.grid.factory.supplier');
        $supplierGrid = $supplierGridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Suppliers/index.html.twig',
            [
                'supplierGrid' => $this->presentGrid($supplierGrid),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'settingsTipMessage' => $this->getSettingsTipMessage(),
            ]
        );
    }

    /**
     * Displays supplier creation form and handles form submit which creates new supplier.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to add this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $formData = [];
        if ($request->request->has('supplier') && isset($request->request->get('supplier')['id_country'])) {
            $formCountryId = (int) $request->request->get('supplier')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        $supplierForm = $this->getFormBuilder()->getForm($formData);
        $supplierForm->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($supplierForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_suppliers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Suppliers/add.html.twig', [
            'supplierForm' => $supplierForm->createView(),
        ]);
    }

    /**
     * Deletes supplier.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_suppliers_index"
     * )
     *
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    public function deleteAction($supplierId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteSupplierCommand((int) $supplierId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk deletion of suppliers.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_suppliers_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $suppliersToDelete = $request->request->get('supplier_bulk');

        try {
            $suppliersToDelete = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToDelete
            );
            $this->getCommandBus()->handle(new BulkDeleteSupplierCommand($suppliersToDelete));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk disables supplier statuses.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_suppliers_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request)
    {
        $suppliersToDisable = $request->request->get('supplier_bulk');

        try {
            $suppliersToDisable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToDisable
            );
            $this->getCommandBus()->handle(new BulkDisableSupplierCommand($suppliersToDisable));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Bulk enables supplier statuses.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_suppliers_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request)
    {
        $suppliersToEnable = $request->request->get('supplier_bulk');

        try {
            $suppliersToEnable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $suppliersToEnable
            );
            $this->getCommandBus()->handle(new BulkEnableSupplierCommand($suppliersToEnable));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Displays edit supplier form and submits form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     * @param int $supplierId
     *
     * @return Response
     */
    public function editAction(Request $request, $supplierId)
    {
        $formData = [];
        if ($request->request->has('supplier') && isset($request->request->get('supplier')['id_country'])) {
            $formCountryId = (int) $request->request->get('supplier')['id_country'];
            $formData['id_country'] = $formCountryId;
        }

        try {
            /** @var EditableSupplier $editableSupplier */
            $editableSupplier = $this->getQueryBus()->handle(new GetSupplierForEditing((int) $supplierId));

            $supplierForm = $this->getFormBuilder()->getFormFor((int) $supplierId, $formData);
            $supplierForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor((int) $supplierId, $supplierForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

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
        ]);
    }

    /**
     * Toggles supplier active status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_suppliers_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_suppliers_index"
     * )
     *
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($supplierId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleSupplierStatusCommand((int) $supplierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Views supplier products information.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $supplierId
     *
     * @return Response
     */
    public function viewAction(Request $request, $supplierId)
    {
        try {
            /** @var ViewableSupplier $viewableSupplier */
            $viewableSupplier = $this->getQueryBus()->handle(new GetSupplierForViewing(
                (int) $supplierId,
                (int) $this->getContextLangId()
            ));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_suppliers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Suppliers/view.html.twig', [
            'layoutTitle' => $viewableSupplier->getName(),
            'viewableSupplier' => $viewableSupplier,
            'isStockManagementEnabled' => $this->configuration->get('PS_STOCK_MANAGEMENT'),
            'isAllShopContext' => $this->get('prestashop.adapter.shop.context')->isAllShopContext(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Exports to csv visible suppliers list data.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param SupplierFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(SupplierFilters $filters)
    {
        $filters = new SupplierFilters(['limit' => null] + $filters->all());
        $supplierGridFactory = $this->get('prestashop.core.grid.factory.supplier');
        $supplierGrid = $supplierGridFactory->getGrid($filters);

        $headers = [
            'id_supplier' => $this->trans('ID', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'products_count' => $this->trans('Number of products', 'Admin.Catalog.Feature'),
            'active' => $this->trans('Enabled', 'Admin.Global'),
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
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $iniConfig = $this->get('prestashop.core.configuration.ini_configuration');

        return [
            SupplierNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            AddressNotFoundException::class => $this->trans(
                'The address for this supplier have been deleted',
                'Admin.Notifications.Error'
            ),
            CannotToggleSupplierStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotUpdateSupplierStatusException::class => $this->trans(
                'An error occurred while updating the status for an object.',
                'Admin.Notifications.Error'
            ),
            SupplierConstraintException::class => [
                SupplierConstraintException::INVALID_BULK_DATA => $this->trans(
                    'You must select at least one element to delete.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteSupplierException::class => [
                CannotDeleteSupplierException::HAS_PENDING_ORDERS => $this->trans(
                    'It is not possible to delete a supplier if there are pending supplier orders.',
                    'Admin.Catalog.Notification'
                ),
                CannotDeleteSupplierException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteSupplierException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
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
                    'Maximum image size: %s.', 'Admin.Global', [
                    $iniConfig->getUploadMaxSizeInBytes(),
                ]),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.supplier_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.supplier_form_handler');
    }

    protected function getSettingsTipMessage()
    {
        $urlOpening = sprintf('<a href="%s">', $this->get('router')->generate('admin_preferences'));
        $urlEnding = '</a>';

        if ($this->configuration->get('PS_DISPLAY_SUPPLIERS')) {
            return $this->trans(
                'The display of your suppliers is enabled on your store. Go to %sShop Parameters > General%s to edit settings.',
                'Admin.Catalog.Notification',
                [$urlOpening, $urlEnding]
            );
        }

        return $this->trans(
            'The display of your suppliers is disabled on your store. Go to %sShop Parameters > General%s to edit settings.',
            'Admin.Catalog.Notification',
            [$urlOpening, $urlEnding]
        );
    }
}
