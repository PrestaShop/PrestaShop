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

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotToggleSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
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

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Suppliers/index.html.twig',
            [
                'supplierGrid' => $gridPresenter->present($supplierGrid),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
            ]
        );
    }

    /**
     * Filters list results.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.supplier');
        $supplierDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($supplierDefinition);

        $searchParametersForm->handleRequest($request);
        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_suppliers_index', ['filters' => $filters]);
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
     * @return RedirectResponse
     */
    public function createAction()
    {
        $legacyLink = $this->getAdminLink('AdminSuppliers', [
            'addsupplier' => 1,
        ]);

        return $this->redirect($legacyLink);
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
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
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
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
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
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
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
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
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
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    public function editAction($supplierId)
    {
        $legacyLink = $this->getAdminLink('AdminSuppliers', [
            'id_supplier' => $supplierId,
            'updatesupplier' => 1,
        ]);

        return $this->redirect($legacyLink);
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
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    /**
     * Views supplier products information.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $supplierId
     *
     * @return RedirectResponse
     */
    public function viewAction($supplierId)
    {
        $legacyLink = $this->getAdminLink('AdminSuppliers', [
            'id_supplier' => $supplierId,
            'viewsupplier' => 1,
        ]);

        return $this->redirect($legacyLink);
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
     * Gets error by exception type.
     *
     * @param Exception $exception
     *
     * @return string
     *
     * @todo use FrameworkAdminBundleController::getErrorMessageForException() instead
     */
    private function handleException(Exception $exception)
    {
        if (0 !== $exception->getCode()) {
            return $this->getExceptionMessageByExceptionCode($exception);
        }

        return $this->getExceptionMessageByType($exception);
    }

    /**
     * Gets by exception type
     *
     * @param Exception $exception
     *
     * @return string
     */
    private function getExceptionMessageByType(Exception $exception)
    {
        $exceptionTypeDictionary = [
            SupplierNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
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
        ];

        if ($exception instanceof CannotDeleteSupplierException) {
            return $this->trans(
                'Can\'t delete #%id%',
                'Admin.Notifications.Error',
                [
                    '%id%' => $exception->getSupplierId(),
                ]
            );
        }

        $exceptionType = get_class($exception);
        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
    }

    /**
     * Gets exception message by exception code.
     *
     * @param Exception $exception
     *
     * @return string
     */
    private function getExceptionMessageByExceptionCode(Exception $exception)
    {
        $exceptionConstraintDictionary = [
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
            ],
        ];

        $exceptionType = get_class($exception);
        $exceptionCode = $exception->getCode();

        if (isset($exceptionConstraintDictionary[$exceptionType][$exceptionCode])) {
            return $exceptionConstraintDictionary[$exceptionType][$exceptionCode];
        }

        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
    }
}
