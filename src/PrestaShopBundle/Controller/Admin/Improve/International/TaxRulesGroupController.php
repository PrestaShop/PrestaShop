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
use PrestaShop\PrestaShop\Adapter\Country\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\UpdateTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkDeleteTaxRulesException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRule;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRuleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                        'tax_rules_group_id' => $handlerResult->getIdentifiableObjectId(),
                    ]
                );
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
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

        $session = $this->get('session');
        $taxRuleFormOptions = [];
        $taxRuleFormData = [];

        if ($session->has('tax_rule')) {
            $taxRuleSessionParameter = $session->get('tax_rule');
            $taxRuleFormOptions['action'] = $taxRuleSessionParameter['form_action'];
            $taxRuleFormData = [
                'tax_rule_id' => $taxRuleSessionParameter['tax_rule_id'],
                'tax_rules_group_id' => $taxRulesGroupId,
            ];

            $session->remove('tax_rule');
        }

        try {
            $taxRuleGridFactory = $this->get('prestashop.core.grid.factory.tax_rule');

            $filters->set('filters', ['tax_rules_group_id' => $taxRulesGroupId]);
            $taxRuleGrid = $taxRuleGridFactory->getGrid($filters);

            $taxRulesGroupFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.tax_rules_group_form_builder'
            );

            $taxRulesGroupFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.tax_rules_group_form_handler'
            );

            $taxRuleFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.tax_rule_form_builder'
            );

            $taxRuleForm = $taxRuleFormBuilder->getForm($taxRuleFormData, $taxRuleFormOptions);

            $taxRuleForm->handleRequest($request);

            $taxRulesGroupForm = $taxRulesGroupFormBuilder->getFormFor($taxRulesGroupId);
            $taxRulesGroupForm->handleRequest($request);

            $result = $taxRulesGroupFormHandler->handleFor($taxRulesGroupId, $taxRulesGroupForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/TaxRulesGroup/edit.html.twig', [
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getTaxRuleToolbarButtons($taxRulesGroupId),
            'taxRulesGroupForm' => $taxRulesGroupForm->createView(),
            'taxRuleForm' => $taxRuleForm->createView(),
            'taxRuleGrid' => $this->presentGrid($taxRuleGrid),
            'taxRulesGroup' => $editableTaxRulesGroup,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Actions',
                [
                    '%value%' => $editableTaxRulesGroup->getName(),
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
     * @param int $taxRulesGroupId
     *
     * @return Response
     */
    public function createTaxRuleAction(Request $request, int $taxRulesGroupId): Response
    {
        $taxRuleFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.tax_rule_form_builder'
        );

        $taxRuleForm = $taxRuleFormBuilder->getForm(['tax_rules_group_id' => $taxRulesGroupId]);
        $taxRuleForm->handleRequest($request);

        try {
            if ($taxRuleForm->isSubmitted() && $taxRuleForm->isValid()) {
                $this->getCommandBus()->handle($this->createAddTaxRulesCommand($taxRuleForm->getData()));
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));
            }

            if ($taxRuleForm->isSubmitted() && !$taxRuleForm->isValid()) {
                $this->setTaxRuleSessionParameters(
                        $this->generateUrl('admin_tax_rules_create', ['taxRulesGroupId' => $taxRulesGroupId])
                    );

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                        'taxRulesGroupId' => $taxRulesGroupId,
                        'request' => $request,
                    ], 307);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_edit', [
            'taxRulesGroupId' => $taxRulesGroupId,
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
     * @param int $taxRuleId
     *
     * @return Response
     */
    public function editTaxRuleAction(Request $request, int $taxRuleId): Response
    {
        try {
            /** @var EditableTaxRule $editableTaxRule */
            $editableTaxRule = $this->getQueryBus()->handle(new GetTaxRuleForEditing((int) $taxRuleId));
        } catch (Exception $e) {
            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

        try {
            $taxRuleFormBuilder = $this->get(
                        'prestashop.core.form.identifiable_object.builder.tax_rule_form_builder'
                    );

            $taxRuleForm = $taxRuleFormBuilder->getFormFor(
                        $taxRuleId,
                        [
                            'tax_rules_group_id' => $editableTaxRule->getTaxRulesGroupId()->getValue(),
                            'tax_rule_id' => $taxRuleId,
                        ]
                    );

            $taxRuleForm->handleRequest($request);

            if ($taxRuleForm->isSubmitted() && $taxRuleForm->isValid()) {
                $this->getCommandBus()->handle($this->createUpdateTaxRuleCommand($taxRuleId, $taxRuleForm->getData()));
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            }

            if ($taxRuleForm->isSubmitted() && !$taxRuleForm->isValid()) {
                $this->setTaxRuleSessionParameters(
                            $this->generateUrl('admin_tax_rules_edit', ['taxRuleId' => $taxRuleId]),
                            $taxRuleId
                        );

                return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                            'taxRulesGroupId' => $editableTaxRule->getTaxRulesGroupId()->getValue(),
                            'request' => $request,
                        ], 307);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_edit', [
            'taxRulesGroupId' => $editableTaxRule->getTaxRulesGroupId()->getValue(),
        ]);
    }

    /**
     * Loads tax rule data for ajax requests
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param int $taxRuleId
     *
     * @return Response
     */
    public function loadTaxRuleAction(int $taxRuleId)
    {
        try {
            /** @var EditableTaxRule $editableTaxRule */
            $editableTaxRule = $this->getQueryBus()->handle(new GetTaxRuleForEditing((int) $taxRuleId));
            $statesProvider = $this->get('prestashop.adapter.form.choice_provider.country_state_by_id');
            $states = $statesProvider->getChoices([
                'id_country' => $editableTaxRule->getCountryId()->getValue(),
            ]);
            $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
            $serializer = new Serializer([$normalizer], ['json' => new JsonEncoder()]);

            return new Response($serializer->serialize([$editableTaxRule, $states], 'json'));
        } catch (Exception $e) {
            return $this->json([
                'message' => $this->getErrorMessageForException($e, []),
            ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Deletes tax rules group.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @param int $taxRuleId
     *
     * @return RedirectResponse
     */
    public function deleteTaxRuleAction(int $taxRuleId): RedirectResponse
    {
        try {
            /** @var EditableTaxRule $editableTaxRule */
            $editableTaxRule = $this->getQueryBus()->handle(new GetTaxRuleForEditing((int) $taxRuleId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_tax_rules_groups_index');
        }

        try {
            $this->getCommandBus()->handle(new DeleteTaxRuleCommand((int) $taxRuleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                'taxRulesGroupId' => $editableTaxRule->getTaxRulesGroupId()->getValue(),
            ]
        );
    }

    /**
     * Delete tax rules groups on bulk action.
     *
     * @param Request $request
     * @param int $taxRulesGroupId
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_tax_rules_groups_index",
     * )
     *
     * @return RedirectResponse
     */
    public function bulkDeleteTaxRulesAction(Request $request, int $taxRulesGroupId): RedirectResponse
    {
        $taxRuleIds = $this->getBulkTaxRulesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteTaxRuleCommand($taxRuleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (TaxRulesGroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_tax_rules_groups_edit', [
                'taxRulesGroupId' => $taxRulesGroupId,
            ]
        );
    }

    /**
     * @param string $action
     * @param int|null $taxRuleId
     */
    private function setTaxRuleSessionParameters(string $action, ?int $taxRuleId = null)
    {
        $session = $this->get('session');

        $options = [
            'form_action' => $action,
            'tax_rule_id' => $taxRuleId,
        ];

        $session->set('tax_rule', $options);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkTaxRulesFromRequest(Request $request): array
    {
        $taxRulesGroupIds = $request->request->get('tax_rule_grid_bulk');

        if (!is_array($taxRulesGroupIds)) {
            return [];
        }

        foreach ($taxRulesGroupIds as $i => $taxRulesGroupId) {
            $taxRulesGroupIds[$i] = (int) $taxRulesGroupId;
        }

        return $taxRulesGroupIds;
    }

    /**
     * @param int $taxRulesGroupId
     *
     * @return array
     */
    private function getTaxRuleToolbarButtons(int $taxRulesGroupId): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_tax_rules_create', ['taxRulesGroupId' => $taxRulesGroupId]),
            'desc' => $this->trans('Add a new tax rule', 'Admin.International.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * Gets error messages for exceptions
     *
     * @param Exception|null $e
     *
     * @return array
     */
    private function getErrorMessages(?Exception $e = null): array
    {
        return [
            CannotAddTaxRuleException::class => $this->getErrorMessageForTaxRuleBulkCreate($e),
            CannotUpdateTaxRuleException::class => $this->getErrorMessageForTaxRuleBulkUpdate($e),
            TaxRuleConstraintException::class => [
                TaxRuleConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
                TaxRuleConstraintException::INVALID_BEHAVIOR_ID => sprintf(
                    $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                    $this->trans('Behavior', 'Admin.International.Feature')
                ),
            ],
            CannotAddTaxRulesGroupException::class => $this->trans(
                'An error occurred while creating an object.',
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteTaxRulesException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof CannotBulkDeleteTaxRulesException ? implode(', ', $e->getIds()) : ''
            ),
            CannotDeleteTaxRuleException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CannotUpdateTaxRulesGroupException::class => $this->trans(
                'An error occurred while updating an object.',
                'Admin.Notifications.Error'
            ),
            TaxRuleNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            TaxRulesGroupException::class => $this->trans(
                'Unexpected error occurred.',
                'Admin.Notifications.Error'
            ),
            TaxRulesGroupNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            TaxRulesGroupConstraintException::class => [
                TaxRulesGroupConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            StateConstraintException::class => [
                StateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            TaxConstraintException::class => [
                TaxConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    private function getErrorMessageForTaxRuleBulkCreate(?Exception $e): string
    {
        $errorCreatingMessage = $this->trans(
            'An error occurred while creating an object.',
            'Admin.Notifications.Error'
        );

        if (!$e instanceof CannotAddTaxRuleException) {
            return $errorCreatingMessage;
        }

        return $this->getErrorMessageForTaxRuleBulkAction($errorCreatingMessage, $e->getFailedCountryRules());
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    private function getErrorMessageForTaxRuleBulkUpdate(?Exception $e): string
    {
        $errorUpdatingMessage = $this->trans(
            'An error occurred while updating an object.',
            'Admin.Notifications.Error'
        );

        if (!$e instanceof CannotUpdateTaxRuleException) {
            return $errorUpdatingMessage;
        }

        return $this->getErrorMessageForTaxRuleBulkAction($errorUpdatingMessage, $e->getFailedCountryRules());
    }

    /**
     * @param string $errorBaseMessage
     * @param array $errors
     *
     * @return string
     */
    private function getErrorMessageForTaxRuleBulkAction(string $errorBaseMessage, array $errors): string
    {
        if (empty($errors)) {
            return $errorBaseMessage;
        }

        $failedOnObjectMessage = $this->trans(
            'Countries',
            'Admin.International.Feature'
        );

        $ids = implode(', ', array_keys($errors));

        if (count($errors) === 1) {
            $failedOnObjectMessage = $this->trans(
                'States',
                'Admin.International.Feature'
            );

            $ids = implode(', ', array_map(function ($value) {
                return  implode(', ', $value);
            }, $errors));
        }

        return sprintf(
            '%s %s: %s',
            $errorBaseMessage,
            $failedOnObjectMessage,
            $ids
        );
    }

    /**
     * @param array $data
     * @return AddTaxRulesCommand
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxConstraintException
     * @throws TaxRuleConstraintException
     * @throws TaxRulesGroupConstraintException
     */
    private function createAddTaxRulesCommand(array $data)
    {
        $stateIds = $this->getStateIds($data);

        $command = new AddTaxRulesCommand(
            (int) $data['tax_rules_group_id'],
            (int) $data['behavior_id'],
            array_map('intval', $stateIds)
        );

        if (!empty($data['tax_id'])) {
            $command->setTaxId((int) $data['tax_id']);
        }

        if (!empty($data['description'])) {
            $command->setDescription($data['description']);
        }

        if (!empty($data['country_id'])) {
            $command->setCountryId((int) $data['country_id']);
        }

        if (!empty($data['zip_code'])) {
            $command->setZipCode($data['zip_code']);
        }

        return $command;
    }

    /**
     * @param $id
     * @param array $data
     * @return UpdateTaxRuleCommand
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxConstraintException
     * @throws TaxRuleConstraintException
     */
    private function createUpdateTaxRuleCommand($id, array $data)
    {
        $stateIds = $this->getStateIds($data);

        $command = new UpdateTaxRuleCommand(
            $id,
            (int) $data['country_id'],
            (int) $data['behavior_id'],
            array_map('intval', $stateIds)
        );

        if (null !== $data['description']) {
            $command->setDescription($data['description']);
        }

        if (!empty($data['tax_id'])) {
            $command->setTaxId($data['tax_id']);
        }

        if (!empty($data['zip_code'])) {
            $command->setZipCode($data['zip_code']);
        }

        return $command;
    }

    /**
     * @param array $data
     * @return array|mixed
     */
    private function getStateIds(array $data): array
    {
        return !empty($data['state_ids']) ? $data['state_ids'] : [StateId::ALL_STATES_ID];
    }
}
