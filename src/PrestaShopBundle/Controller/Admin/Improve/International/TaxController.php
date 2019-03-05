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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\DeleteTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\UpdateTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult\EditableTax;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TaxController is responsible for handling "Improve > International > Taxes" page.
 */
class TaxController extends FrameworkBundleAdminController
{
    /**
     * Show taxes page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param TaxFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, TaxFilters $filters)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $taxGridFactory = $this->get('prestashop.core.grid.factory.tax');
        $taxGrid = $taxGridFactory->getGrid($filters);
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Improve/International/Tax/index.html.twig', [
            'taxGrid' => $gridPresenter->present($taxGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ]);
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.tax');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_taxes_index', ['filters' => $filters]);
    }

    public function createAction()
    {
        //@todo: implement create action
        return $this->redirectToRoute('admin_taxes_index');
    }

    public function editAction($taxId)
    {
        //@todo: implement edit
        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Deletes tax.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @param int $taxId
     *
     * @return RedirectResponse
     */
    public function deleteAction($taxId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteTaxCommand((int) $taxId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Toggles status.
     *
     * @param int $taxId
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($taxId)
    {
        try {
            /** @var EditableTax $editableTax */
            $editableTax = $this->getQueryBus()->handle(new GetTaxForEditing((int) $taxId));
            $this->getCommandBus()->handle(new ToggleTaxStatusCommand((int) $taxId, !$editableTax->isActive()));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Enables taxes status on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @return RedirectResponse
     */
    public function bulkEnableStatusAction(Request $request)
    {
        $taxIds = $request->request->get('tax_bulk');
        try {
            $this->getCommandBus()->handle(new BulkToggleTaxStatusCommand($taxIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Disables taxes status on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @return RedirectResponse
     */
    public function bulkDisableStatusAction(Request $request)
    {
        $taxIds = $request->request->get('tax_bulk');
        try {
            $this->getCommandBus()->handle(new BulkToggleTaxStatusCommand($taxIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Delete taxes on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_taxes_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_taxes_index")
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $taxIds = $request->request->get('tax_bulk');
        try {
            $this->getCommandBus()->handle(new BulkDeleteTaxCommand($taxIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_taxes_index');
    }

    /**
     * Gets error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            TaxNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            UpdateTaxException::class => [
                UpdateTaxException::FAILED_BULK_UPDATE_STATUS => [
                    $this->trans(
                        'An error occurred while updating the status.',
                        'Admin.Notifications.Error'
                    ),
                ],
                UpdateTaxException::FAILED_UPDATE_STATUS => [
                    $this->trans(
                        'An error occurred while updating the status for an object.',
                        'Admin.Notifications.Error'
                    ),
                ],
            ],
            DeleteTaxException::class => [
                DeleteTaxException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                DeleteTaxException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
