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

use Exception;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRuleFilters;
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
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->redirect($this->getAdminLink('AdminTaxRulesGroup', []));
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
        $taxRulesGroupFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder'
        );

        $taxRulesGroupFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler'
        );

        $taxRulesGroupForm = $taxRulesGroupFormBuilder->getForm();
        $taxRulesGroupForm->handleRequest($request);

        try {
            $handlerResult = $taxRulesGroupFormHandler->handle($taxRulesGroupForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tax_rules_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/create.html.twig', [
            'enableSidebar' => true,
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
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
        try {
            /** @var EditableTaxRulesGroup $editableTaxRulesGroup */
            $editableTaxRulesGroup = $this->getQueryBus()->handle(new GetTaxRulesGroupForEditing((int) $taxRulesGroupId));
        } catch (TaxRulesGroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

//        try {
            $taxRuleGridFactory = $this->get('prestashop.core.grid.factory.tax_rule');

            $filters->set('filters', ['tax_rules_group_id' => $taxRulesGroupId]);
            $taxRuleGrid = $taxRuleGridFactory->getGrid($filters);

            $taxRulesGroupFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder'
            );

            $taxRulesGroupFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler'
            );

            $taxRulesGroupForm = $taxRulesGroupFormBuilder->getFormFor($taxRulesGroupId);
            $taxRulesGroupForm->handleRequest($request);

            $result = $taxRulesGroupFormHandler->handleFor($taxRulesGroupId, $taxRulesGroupForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tax_rules_groups_index');
            }
//        } catch (Exception $e) {
//            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
//
//            return $this->redirectToRoute('admin_tax_rules_groups_index');
//        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/edit.html.twig', [
            'enableSidebar' => true,
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'taxRuleGrid' => $this->presentGrid($taxRuleGrid),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Actions',
                [
                    '%value%' => $editableTaxRulesGroup->getName()
                ]
            ),
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
    public function createTaxRuleAction(Request $request): Response
    {
        return $this->redirect($this->getAdminLink('AdminTaxRulesGroup', []));
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
     *
     * @return Response
     */
    public function editTaxRuleAction(Request $request, int $taxRulesGroupId): Response
    {
        return $this->redirect($this->getAdminLink('AdminTaxRulesGroup', []));
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
    public function deleteTaxRuleAction(int $taxRulesGroupId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteTaxRulesGroupCommand((int) $taxRulesGroupId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (TaxRulesGroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
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
    public function bulkDeleteTaxRulesAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteTaxRulesGroupCommand($taxRulesGroupIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (TaxRulesGroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkTaxRulesFromRequest(Request $request): array
    {
        $taxRulesGroupIds = $request->request->get('tax_rules_group_bulk');

        if (!is_array($taxRulesGroupIds)) {
            return [];
        }

        foreach ($taxRulesGroupIds as $i => $taxRulesGroupId) {
            $taxRulesGroupIds[$i] = (int) $taxRulesGroupId;
        }

        return $taxRulesGroupIds;
    }

    /**
     * @return array
     */
    private function getTaxRuleToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_tax_rules_create'),
            'desc' => $this->trans('Add a new tax rule', 'Admin.International.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Gets error messages for exceptions
     *
     * @param TaxRulesGroupException $e
     *
     * @return array
     */
    private function getErrorMessages(TaxRulesGroupException $e = null): array
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
                '%s : %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkDeleteTaxRulesGroupException ? $e->getMessage() : ''
            ),
            CannotBulkUpdateTaxRulesGroupException::class => sprintf(
                '%s : %s',
                $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkUpdateTaxRulesGroupException ? $e->getMessage() : ''
            ),
            TaxRulesGroupConstraintException::class => [
                TaxRulesGroupConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
