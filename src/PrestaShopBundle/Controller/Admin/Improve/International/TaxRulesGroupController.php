<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings;
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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\BulkDeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\DeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotAddTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotBulkDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotUpdateTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleList;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\EditableTaxRule;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\TaxRuleList;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\TaxRuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRulesGroupFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Improve > International > Tax Rules" page.
 */
class TaxRulesGroupController extends PrestaShopAdminController
{
    /**
     * Show tax rules group page.
     *
     * @param Request $request
     * @param TaxRulesGroupFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        TaxRulesGroupFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.tax_rules_group')]
        GridFactoryInterface $taxRulesGroupGridFactory
    ): Response {
        $taxRulesGroupGrid = $taxRulesGroupGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/index.html.twig', [
            'taxRulesGroupGrid' => $this->presentGrid($taxRulesGroupGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getTaxRulesGroupToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $taxRulesGroupForm = $formBuilder->getForm();
        $taxRulesGroupForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($taxRulesGroupForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

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
            'layoutTitle' => $this->trans('New tax rule', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Handles tax rules group edit
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function editAction(
        Request $request,
        int $taxRulesGroupId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $taxRulesGroupForm = null;

        try {
            $taxRulesGroupForm = $formBuilder->getFormFor((int) $taxRulesGroupId, [], [
                'show_tax_rules_list' => true,
                'tax_rules_group_id' => $taxRulesGroupId,
            ]);
            $taxRulesGroupForm->handleRequest($request);
            $result = $formHandler->handleFor((int) $taxRulesGroupId, $taxRulesGroupForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

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

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Editing tax rule %value%', ['%value%' => $taxRulesGroupForm->getData()['name']], 'Admin.Navigation.Menu'),
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Returns states for a given country as JSON.
     *
     * @param int $countryId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function getStatesForCountryAction(
        int $countryId,
        #[Autowire(service: 'doctrine.dbal.default_connection')]
        \Doctrine\DBAL\Connection $connection,
        #[Autowire(param: 'database_prefix')]
        string $dbPrefix,
    ): JsonResponse {
        $qb = $connection->createQueryBuilder();
        $states = $qb
            ->select('s.id_state', 's.name')
            ->from($dbPrefix . 'state', 's')
            ->where('s.id_country = :countryId')
            ->andWhere('s.active = 1')
            ->setParameter('countryId', $countryId)
            ->orderBy('s.name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return new JsonResponse($states);
    }

    /**
     * Returns paginated list of tax rules for a group as JSON.
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function listTaxRulesAction(Request $request, int $taxRulesGroupId): JsonResponse
    {
        $limit = $request->query->getInt('limit') ?: null;
        $offset = $request->query->getInt('offset') ?: null;

        /** @var TaxRuleList $taxRuleList */
        $taxRuleList = $this->dispatchQuery(
            new GetTaxRuleList(
                $taxRulesGroupId,
                $this->getLanguageContext()->getId(),
                $limit,
                $offset,
            )
        );

        $taxRules = [];
        foreach ($taxRuleList->getTaxRules() as $taxRule) {
            $taxRules[] = [
                'id' => $taxRule->getTaxRuleId(),
                'countryName' => $taxRule->getCountryName(),
                'stateName' => $taxRule->getStateName(),
                'zipcode' => $taxRule->getZipcode(),
                'behavior' => $this->formatBehavior($taxRule->getBehavior()),
                'taxName' => $taxRule->getTaxName(),
                'taxRate' => $taxRule->getTaxRate() . ' %',
                'description' => $taxRule->getDescription(),
                'editUrl' => $this->generateUrl('admin_tax_rules_edit', [
                    'taxRulesGroupId' => $taxRulesGroupId,
                    'taxRuleId' => $taxRule->getTaxRuleId(),
                ]),
                'deleteUrl' => $this->generateUrl('admin_tax_rules_delete', [
                    'taxRulesGroupId' => $taxRulesGroupId,
                    'taxRuleId' => $taxRule->getTaxRuleId(),
                ]),
            ];
        }

        return $this->json([
            'taxRules' => $taxRules,
            'total' => $taxRuleList->getTotalCount(),
        ]);
    }

    /**
     * Handles tax rules grid search (filters).
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchTaxRulesAction(
        Request $request,
        int $taxRulesGroupId,
        #[Autowire(service: 'PrestaShop\\PrestaShop\\Core\\Grid\\Definition\\Factory\\TaxRuleGridDefinitionFactory')]
        GridDefinitionFactoryInterface $definitionFactory,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definitionFactory,
            $request,
            TaxRuleGridDefinitionFactory::GRID_ID,
            'admin_tax_rules_groups_edit',
            ['taxRulesGroupId']
        );
    }

    /**
     * Creates a tax rule within a group.
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function createTaxRuleAction(
        Request $request,
        int $taxRulesGroupId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tax_rule_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.tax_rule_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $isLiteDisplaying = $request->query->has('liteDisplaying');
        $taxRuleForm = $formBuilder->getForm(['tax_rules_group_id' => $taxRulesGroupId]);
        $taxRuleForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($taxRuleForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                if ($isLiteDisplaying) {
                    return $this->redirectToRoute('admin_tax_rules_create', [
                        'taxRulesGroupId' => $taxRulesGroupId,
                        'liteDisplaying' => 1,
                    ]);
                }

                $newGroupId = $handlerResult->getIdentifiableObjectId();

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                    'taxRulesGroupId' => $newGroupId,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/create_tax_rule.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New tax rule', [], 'Admin.Navigation.Menu'),
            'taxRuleForm' => $taxRuleForm->createView(),
            'taxRulesGroupId' => $taxRulesGroupId,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'lightDisplay' => $isLiteDisplaying,
        ]);
    }

    /**
     * Edits a tax rule.
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     * @param int $taxRuleId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function editTaxRuleAction(
        Request $request,
        int $taxRulesGroupId,
        int $taxRuleId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tax_rule_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.tax_rule_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        $isLiteDisplaying = $request->query->has('liteDisplaying');

        try {
            $taxRuleForm = $formBuilder->getFormFor($taxRuleId, [], [
                'is_edit' => true,
            ]);
            $taxRuleForm->handleRequest($request);
            $result = $formHandler->handleFor($taxRuleId, $taxRuleForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                if ($isLiteDisplaying) {
                    return $this->redirectToRoute('admin_tax_rules_edit', [
                        'taxRulesGroupId' => $taxRulesGroupId,
                        'taxRuleId' => $taxRuleId,
                        'liteDisplaying' => 1,
                    ]);
                }

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                    'taxRulesGroupId' => $taxRulesGroupId,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                'taxRulesGroupId' => $taxRulesGroupId,
            ]);
        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/edit_tax_rule.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Edit tax rule', [], 'Admin.Navigation.Menu'),
            'taxRuleForm' => $taxRuleForm->createView(),
            'taxRulesGroupId' => $taxRulesGroupId,
            'taxRuleId' => $taxRuleId,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'lightDisplay' => $isLiteDisplaying,
        ]);
    }

    /**
     * Deletes a single tax rule.
     *
     * @param int $taxRulesGroupId
     * @param int $taxRuleId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function deleteTaxRuleAction(int $taxRulesGroupId, int $taxRuleId): JsonResponse
    {
        try {
            $this->dispatchCommand(new DeleteTaxRuleCommand($taxRuleId));
        } catch (Exception $e) {
            return $this->json(
                ['error' => $this->getErrorMessageForException($e, $this->getErrorMessages())],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(['message' => $this->trans('Successful deletion', [], 'Admin.Notifications.Success')]);
    }

    /**
     * Bulk deletes tax rules within a group.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function bulkDeleteTaxRulesAction(Request $request): RedirectResponse
    {
        $taxRuleIds = $this->getBulkTaxRulesFromRequest($request);

        // Resolve the group ID from the first tax rule
        $taxRulesGroupId = 0;
        if (!empty($taxRuleIds)) {
            try {
                /** @var EditableTaxRule $editableTaxRule */
                $editableTaxRule = $this->dispatchQuery(
                    new GetTaxRuleForEditing($taxRuleIds[0])
                );
                $taxRulesGroupId = $editableTaxRule->getTaxRulesGroupId()->getValue();
            } catch (Exception) {
                // Fallback: redirect to index if we can't determine the group
                $this->addFlash('error', $this->trans('An error occurred while deleting this selection.', [], 'Admin.Notifications.Error'));

                return $this->redirectToRoute('admin_tax_rules_groups_index');
            }
        }

        try {
            $this->dispatchCommand(new BulkDeleteTaxRuleCommand($taxRulesGroupId, $taxRuleIds));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_edit', [
            'taxRulesGroupId' => $taxRulesGroupId,
        ]);
    }

    /**
     * Deletes tax rules group.
     *
     * @param int $taxRulesGroupId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function deleteAction(int $taxRulesGroupId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteTaxRulesGroupCommand($taxRulesGroupId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_index');
    }

    /**
     * Toggles status.
     *
     * @param int $taxRulesGroupId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function toggleStatusAction(int $taxRulesGroupId): RedirectResponse
    {
        try {
            /** @var EditableTaxRulesGroup $editableTaxRulesGroup */
            $editableTaxRulesGroup = $this->dispatchQuery(
                new GetTaxRulesGroupForEditing($taxRulesGroupId)
            );

            $this->dispatchCommand(
                new SetTaxRulesGroupStatusCommand($taxRulesGroupId, !$editableTaxRulesGroup->isActive())
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
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
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function bulkEnableStatusAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->dispatchCommand(new BulkSetTaxRulesGroupStatusCommand($taxRulesGroupIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
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
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function bulkDisableStatusAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->dispatchCommand(new BulkSetTaxRulesGroupStatusCommand($taxRulesGroupIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
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
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_tax_rules_groups_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $taxRulesGroupIds = $this->getBulkTaxRulesGroupFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteTaxRulesGroupCommand($taxRulesGroupIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
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
     * @param Request $request
     *
     * @return array
     */
    private function getBulkTaxRulesFromRequest(Request $request): array
    {
        $taxRuleIds = $request->request->all('tax_rules_bulk');

        return array_map('intval', $taxRuleIds);
    }

    /**
     * @return array
     */
    private function getTaxRulesGroupToolbarButtons(): array
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_tax_rules_groups_create'),
                'desc' => $this->trans('Add new tax rules group', [], 'Admin.International.Feature'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }

    private function formatBehavior(int $behavior): string
    {
        return match ($behavior) {
            TaxRuleSettings::BEHAVIOR_COMBINE => $this->trans('Combine', [], 'Admin.International.Feature'),
            TaxRuleSettings::BEHAVIOR_ONE_AFTER_ANOTHER => $this->trans('One after another', [], 'Admin.International.Feature'),
            default => $this->trans('This tax only', [], 'Admin.International.Feature'),
        };
    }

    /**
     * Gets error messages for exceptions
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(?Exception $e = null): array
    {
        return [
            CannotDeleteTaxRulesGroupException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
            TaxRulesGroupNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotUpdateTaxRulesGroupException::class => [
                CannotUpdateTaxRulesGroupException::FAILED_TOGGLE_STATUS => $this->trans(
                    'An error occurred while updating the status.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotBulkDeleteTaxRulesGroupException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkDeleteTaxRulesGroupException ? implode(', ', $e->getTaxRulesGroupsIds()) : ''
            ),
            CannotBulkUpdateTaxRulesGroupException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while updating the status.',
                    [],
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkUpdateTaxRulesGroupException ? implode(', ', $e->getTaxRulesGroupsIds()) : ''
            ),
            TaxRulesGroupConstraintException::class => [
                TaxRulesGroupConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            // TaxRule exceptions
            TaxRuleNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotAddTaxRuleException::class => $this->trans(
                'An error occurred while creating an object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotUpdateTaxRuleException::class => $this->trans(
                'An error occurred while updating an object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteTaxRuleException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteTaxRuleException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkDeleteTaxRuleException ? implode(', ', $e->getTaxRuleIds()) : ''
            ),
            TaxRuleConstraintException::class => [
                TaxRuleConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
                TaxRuleConstraintException::INVALID_ZIPCODE => $this->trans(
                    'The Zip/Postal code is invalid.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
