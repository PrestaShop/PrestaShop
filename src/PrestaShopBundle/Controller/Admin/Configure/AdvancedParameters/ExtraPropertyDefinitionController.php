<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\BulkDeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\BulkExtraPropertyException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\AssociationExistenceChecker;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormFieldTreeProvider;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowSerializer;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ExtraPropertyDefinitionFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Extra Property Definitions" page.
 *
 * Provides CRUD operations (index, create, edit, delete, bulk delete).
 *
 * All write operations reject module-owned definitions (module_name IS NOT NULL).
 */
class ExtraPropertyDefinitionController extends PrestaShopAdminController
{
    /**
     * Displays the extra property definition list grid.
     *
     * @param ExtraPropertyDefinitionFilters $filters
     * @param GridFactoryInterface $gridFactory
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', 'AdminExtraPropertyDefinitions')")]
    public function indexAction(
        ExtraPropertyDefinitionFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.extra_property_definition')]
        GridFactoryInterface $gridFactory,
    ): Response {
        $grid = $gridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/index.html.twig',
            [
                'grid' => $this->presentGrid($grid),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminExtraPropertyDefinitions'),
                'layoutTitle' => $this->trans('Extra property definitions', [], 'Admin.Advparameters.Feature'),
            ]
        );
    }

    /**
     * Displays and handles the extra property definition creation form.
     *
     * @param Request $request
     * @param FormBuilderInterface $formBuilder
     * @param FormHandlerInterface $formHandler
     * @param AssociationExistenceChecker $associationChecker
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('create', 'AdminExtraPropertyDefinitions')")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.extra_property_definition_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.extra_property_definition_form_handler')]
        FormHandlerInterface $formHandler,
        AssociationExistenceChecker $associationChecker,
    ): Response|RedirectResponse {
        // is_edit: false — structural fields (entity_name, property_name, type, scope) stay
        // editable on creation. See ExtraPropertyDefinitionFieldDefinitionType for the exact list.
        $form = $formBuilder->getForm([], ['is_edit' => false]);
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', [], 'Admin.Notifications.Success'));
                $this->flashAssociationExistenceWarnings($form, $associationChecker);

                return $this->redirectToRoute('admin_extra_property_definitions_edit', [
                    'extraPropertyDefinitionId' => $result->getIdentifiableObjectId(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/create.html.twig',
            [
                'extraPropertyDefinitionForm' => $form->createView(),
                'layoutTitle' => $this->trans('New extra property', [], 'Admin.Advparameters.Feature'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminExtraPropertyDefinitions'),
            ]
        );
    }

    /**
     * Returns the recursive field tree of an identifiable-object form as JSON, so the
     * "associated forms" picker can lazily suggest placement paths for one formId at a time
     * (the tree is too expensive to inline for every form on page load).
     *
     * Always responds 200: an unknown or un-introspectable form yields {"available": false}
     * and the UI falls back to a free-text path input.
     */
    #[AdminSecurity("is_granted('read', 'AdminExtraPropertyDefinitions')")]
    public function formFieldsAction(string $formId, FormFieldTreeProvider $treeProvider): JsonResponse
    {
        $fields = $treeProvider->getTree($formId);

        return new JsonResponse([
            'formId' => $formId,
            'available' => null !== $fields,
            'fields' => $fields ?? [],
        ]);
    }

    /**
     * Displays and handles the extra property definition edit form.
     *
     * Module-owned definitions are not editable: the grid only ever links such rows to
     * viewAction(), but a direct URL visit is still redirected there as a safety net — detected
     * from the form's hidden module_name field, no separate query needed.
     *
     * @param int $extraPropertyDefinitionId
     * @param Request $request
     * @param FormBuilderInterface $formBuilder
     * @param FormHandlerInterface $formHandler
     * @param AssociationExistenceChecker $associationChecker
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('update', 'AdminExtraPropertyDefinitions')", redirectRoute: 'admin_extra_property_definitions_index')]
    public function editAction(
        int $extraPropertyDefinitionId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.extra_property_definition_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.extra_property_definition_form_handler')]
        FormHandlerInterface $formHandler,
        AssociationExistenceChecker $associationChecker,
    ): Response|RedirectResponse {
        try {
            // is_edit: true — structural fields are read-only past creation. See
            // ExtraPropertyDefinitionFieldDefinitionType for the exact list.
            $form = $formBuilder->getFormFor($extraPropertyDefinitionId, [], ['is_edit' => true]);
        } catch (ExtraPropertyDefinitionNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_extra_property_definitions_index');
        }

        if (!empty($form->get('field_definition')->get('module_name')->getData())) {
            return $this->redirectToRoute('admin_extra_property_definitions_view', ['extraPropertyDefinitionId' => $extraPropertyDefinitionId]);
        }

        $form->handleRequest($request);

        try {
            $result = $formHandler->handleFor($extraPropertyDefinitionId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', [], 'Admin.Notifications.Success'));
                $this->flashAssociationExistenceWarnings($form, $associationChecker);

                return $this->redirectToRoute('admin_extra_property_definitions_edit', [
                    'extraPropertyDefinitionId' => $extraPropertyDefinitionId,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/edit.html.twig',
            [
                'extraPropertyDefinitionForm' => $form->createView(),
                'layoutTitle' => $this->trans(
                    'Editing extra property "%name%"',
                    ['%name%' => $form->get('field_definition')->get('property_name')->getData()],
                    'Admin.Advparameters.Feature'
                ),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminExtraPropertyDefinitions'),
            ]
        );
    }

    /**
     * Displays a module-owned extra property definition in read-only mode.
     *
     * Reuses the same form builder/type as createAction()/editAction() purely for display: no
     * FormHandlerInterface is involved and the request is never bound to the form, since nothing
     * here is ever submitted.
     *
     * @param int $extraPropertyDefinitionId
     * @param FormBuilderInterface $formBuilder
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity("is_granted('read', 'AdminExtraPropertyDefinitions')", redirectRoute: 'admin_extra_property_definitions_index')]
    public function viewAction(
        int $extraPropertyDefinitionId,
        #[Autowire(service: 'prestashop.core.form.builder.extra_property_definition_form_builder')]
        FormBuilderInterface $formBuilder,
    ): Response|RedirectResponse {
        try {
            // is_edit keeps the structural field set read-only, like the edit form. disabled => true
            // is a native Symfony option that cascades to every child, so the whole view form renders
            // as disabled inputs — making it clear that module-owned definitions cannot be changed here.
            $form = $formBuilder->getFormFor($extraPropertyDefinitionId, [], ['is_edit' => true, 'disabled' => true]);
        } catch (ExtraPropertyDefinitionNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_extra_property_definitions_index');
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/view.html.twig',
            [
                'extraPropertyDefinitionForm' => $form->createView(),
                'layoutTitle' => $this->trans(
                    'Viewing extra property "%name%"',
                    ['%name%' => $form->get('field_definition')->get('property_name')->getData()],
                    'Admin.Advparameters.Feature'
                ),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminExtraPropertyDefinitions'),
            ]
        );
    }

    /**
     * Deletes one extra property definition but keeps its physical SQL column.
     *
     * @param int $extraPropertyDefinitionId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_property_definitions_index')]
    #[AdminSecurity("is_granted('delete', 'AdminExtraPropertyDefinitions')", message: 'You do not have permission to delete this.')]
    public function deleteAction(int $extraPropertyDefinitionId): RedirectResponse
    {
        return $this->doDelete($extraPropertyDefinitionId, false);
    }

    /**
     * Deletes one extra property definition and also drops its physical SQL column.
     *
     * @param int $extraPropertyDefinitionId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_property_definitions_index')]
    #[AdminSecurity("is_granted('delete', 'AdminExtraPropertyDefinitions')", message: 'You do not have permission to delete this.')]
    public function deleteDropColumnAction(int $extraPropertyDefinitionId): RedirectResponse
    {
        return $this->doDelete($extraPropertyDefinitionId, true);
    }

    /**
     * @param int $extraPropertyDefinitionId
     * @param bool $dropColumn
     *
     * @return RedirectResponse
     */
    private function doDelete(int $extraPropertyDefinitionId, bool $dropColumn): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteExtraPropertyDefinitionCommand($extraPropertyDefinitionId, $dropColumn));
            $this->addFlash('success', $this->trans('Successful deletion.', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_extra_property_definitions_index');
    }

    /**
     * Deletes multiple extra property definitions but keeps their physical SQL columns.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_property_definitions_index')]
    #[AdminSecurity("is_granted('delete', 'AdminExtraPropertyDefinitions')", message: 'You do not have permission to delete this.')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        return $this->doBulkDelete($request, false);
    }

    /**
     * Deletes multiple extra property definitions and also drops their physical SQL columns.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_property_definitions_index')]
    #[AdminSecurity("is_granted('delete', 'AdminExtraPropertyDefinitions')", message: 'You do not have permission to delete this.')]
    public function bulkDeleteDropColumnAction(Request $request): RedirectResponse
    {
        return $this->doBulkDelete($request, true);
    }

    /**
     * @param Request $request
     * @param bool $dropColumn
     *
     * @return RedirectResponse
     */
    private function doBulkDelete(Request $request, bool $dropColumn): RedirectResponse
    {
        $ids = array_map('intval', $request->request->all('extra_property_definition_bulk_action'));

        if (empty($ids)) {
            return $this->redirectToRoute('admin_extra_property_definitions_index');
        }

        try {
            $this->dispatchCommand(new BulkDeleteExtraPropertyDefinitionCommand($ids, $dropColumn));
            $this->addFlash('success', $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'));
        } catch (BulkExtraPropertyException $e) {
            $skippedCount = count($e->getExceptions());
            $deletedCount = count($ids) - $skippedCount;

            if ($deletedCount > 0) {
                $this->addFlash('success', $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'));
            }

            $this->addFlash(
                'warning',
                $this->trans(
                    '%count% module-owned definition(s) were skipped and cannot be deleted from the back office.',
                    ['%count%' => $skippedCount],
                    'Admin.Advparameters.Notification'
                )
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_extra_property_definitions_index');
    }

    /**
     * Flashes one non-blocking warning per association target (formId/gridId/API path) the
     * catalogs do not know about — the save itself succeeded, these placements are simply
     * inert until the target exists. Called on the create/edit success paths only.
     */
    private function flashAssociationExistenceWarnings(FormInterface $form, AssociationExistenceChecker $associationChecker): void
    {
        $advanced = $form->getData()['advanced'] ?? [];

        // The associations are row collections; serializing them here yields the exact entry
        // strings the data handler passed to the command.
        $warnings = $associationChecker->check(
            AssociationRowSerializer::formEntries($advanced['associated_forms'] ?? []) ?: null,
            AssociationRowSerializer::gridEntries($advanced['associated_grids'] ?? []) ?: null,
            AssociationRowSerializer::apiEntries($advanced['associated_apis'] ?? []) ?: null
        );

        foreach ($warnings as $warning) {
            $this->addFlash('warning', $warning);
        }
    }

    /**
     * Maps domain exceptions to human-readable error messages for flash display.
     * ExtraPropertyRegistrationFailureException is keyed by its reason code.
     *
     * @return array<string, string|array<int, string>>
     */
    protected function getErrorMessages(): array
    {
        return [
            ExtraPropertyDefinitionNotFoundException::class => $this->trans(
                'The extra property definition was not found.',
                [],
                'Admin.Advparameters.Notification'
            ),
            ExtraPropertyRegistrationFailureException::class => [
                ExtraPropertyRegistrationFailureException::UNKNOWN => $this->trans(
                    'The extra property definition could not be saved.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::BASE_TABLE_MISSING => $this->trans(
                    'No database table exists for this entity, so the extra property cannot be created. Check the entity name.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::SCOPE_CONFLICT => $this->trans(
                    'This property is already registered on this entity with a different scope. Use the existing scope or delete the existing definition first.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::DESTRUCTIVE_CHANGE => $this->trans(
                    'This change would put existing data at risk (type or scope change, size decrease, nullable tightening, or removed choice value) and was refused. Delete and recreate the property instead.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::PERSISTENCE_FAILURE => $this->trans(
                    'The extra property definition could not be saved to the registry.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::SCHEMA_FAILURE => $this->trans(
                    'The database column for this extra property could not be created or updated.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ExtraPropertyRegistrationFailureException::INVALID_FORM_OPTIONS => $this->trans(
                    'The form options are not compatible with the form field type: the field could not be built. Fix the "Advanced form integration" card and try again.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ],
            InvalidExtraPropertyDefinitionException::class => $this->trans(
                'The submitted extra property definition is invalid. Check the form values and try again.',
                [],
                'Admin.Advparameters.Notification'
            ),
            ProtectedModuleExtraPropertyDefinitionException::class => $this->trans(
                'This extra property is managed by a module and cannot be modified from the back office.',
                [],
                'Admin.Advparameters.Notification'
            ),
        ];
    }
}
