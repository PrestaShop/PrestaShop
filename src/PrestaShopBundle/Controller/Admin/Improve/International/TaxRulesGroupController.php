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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkSetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\SetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRuleFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRulesGroupFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Improve > International > Tax Rules" page.
 */
class TaxRulesGroupController extends FrameworkBundleAdminController
{
    /**
     * Show tax rules group page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param TaxRulesGroupFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, TaxRulesGroupFilters $filters): Response
    {
        $taxRulesGroupGridFactory = $this->get('prestashop.core.grid.factory.tax_rules_group');
        $taxRulesGroupGrid = $taxRulesGroupGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/index.html.twig', [
            'taxRulesGroupGrid' => $this->presentGrid($taxRulesGroupGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getTaxRulesGroupToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $taxRulesGroupForm = $this->getFormBuilder()->getForm();
        $taxRulesGroupForm->handleRequest($request);

        try {
            $handlerResult = $this->getFormHandler()->handle($taxRulesGroupForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                    'taxRulesGroupId' => $handlerResult->getIdentifiableObjectId(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/create.html.twig', [
            'enableSidebar' => true,
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('New tax rule', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Handles tax rules group edit
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     * @param TaxRuleFilters $filters
     *
     * @return Response
     */
    public function editAction(Request $request, int $taxRulesGroupId, TaxRuleFilters $filters): Response
    {
        $taxRulesGroupForm = null;

        try {
            $taxRulesGroupForm = $this->getFormBuilder()->getFormFor((int) $taxRulesGroupId);
            $taxRulesGroupForm->handleRequest($request);
            $result = $this->getFormHandler()->handleFor((int) $taxRulesGroupId, $taxRulesGroupForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                    'taxRulesGroupId' => $taxRulesGroupId,
                ]);
            }
        } catch (TaxRulesGroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $filters->addFilter(['taxRulesGroupId' => $taxRulesGroupId]);
        $taxRuleGridFactory = $this->get('prestashop.core.grid.factory.tax_rule');
        $taxRuleGrid = $taxRuleGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Editing tax rule %value%', 'Admin.Navigation.Menu', ['%value%' => $taxRulesGroupForm->getData()['name']]),
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'taxRuleGrid' => $this->presentGrid($taxRuleGrid),
        ]);
    }

    /**
     * Deletes tax rules group.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param int $taxRulesGroupId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $taxRulesGroupId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteTaxRulesGroupCommand($taxRulesGroupId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * Toggles status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param int $taxRulesGroupId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $taxRulesGroupId): RedirectResponse
    {
        try {
            /** @var EditableTaxRulesGroup $editableTaxRulesGroup */
            $editableTaxRulesGroup = $this->getQueryBus()->handle(
                new GetTaxRulesGroupForEditing($taxRulesGroupId)
            );

            $this->getCommandBus()->handle(
                new SetTaxRulesGroupStatusCommand($taxRulesGroupId, !$editableTaxRulesGroup->isActive())
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * Enables tax rules groups status on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @return RedirectResponse
     */
    public function bulkEnableStatusAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkSetTaxRulesGroupStatusCommand($taxRulesGroupIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * Disables tax rules groups status on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @return RedirectResponse
     */
    public function bulkDisableStatusAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkSetTaxRulesGroupStatusCommand($taxRulesGroupIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * Delete tax rules groups on bulk action.
     *
     * @param Request $request
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteTaxRulesGroupCommand($taxRulesGroupIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkTaxRulesGroupFromRequest(Request $request): array
    {
        $taxRulesGroupIds = $request->request->all('tax_rules_group_bulk');

        return array_map('intval', $taxRulesGroupIds);
    }

    /**
     * @return array
     */
    private function getTaxRulesGroupToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_tax_rules_groups_create'),
            'desc' => $this->trans('Add new tax rule', 'Admin.International.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Gets error messages for exceptions
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e = null): array
    {
        return [
            CannotDeleteTaxRulesGroupException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            TaxRulesGroupNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotUpdateTaxRulesGroupException::class => [
                CannotUpdateTaxRulesGroupException::FAILED_TOGGLE_STATUS => $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotBulkDeleteTaxRulesGroupException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkDeleteTaxRulesGroupException ? implode(', ', $e->getTaxRulesGroupsIds()) : ''
            ),
            CannotBulkUpdateTaxRulesGroupException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkUpdateTaxRulesGroupException ? implode(', ', $e->getTaxRulesGroupsIds()) : ''
            ),
            TaxRulesGroupConstraintException::class => [
                TaxRulesGroupConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder');
    }
}
