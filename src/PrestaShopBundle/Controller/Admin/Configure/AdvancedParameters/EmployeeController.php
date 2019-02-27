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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Search\Filters\EmployeeFilters;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\ToggleEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\CannotDeleteEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeStatus;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeController handles pages under "Configure > Advanced Parameters > Team > Employees".
 */
class EmployeeController extends FrameworkBundleAdminController
{
    /**
     * Show employees list & options page.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, EmployeeFilters $filters)
    {
        $employeeOptionsFormHandler = $this->get('prestashop.admin.employee_options.form_handler');
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();

        $employeeOptionsChecker = $this->get('prestashop.core.team.employee.configuration.options_checker');

        $employeeGridFactory = $this->get('prestashop.core.grid.factory.employee');
        $employeeGrid = $employeeGridFactory->getGrid($filters);

        $helperCardDocumentationLinkProvider =
            $this->get('prestashop.core.util.helper_card.documentation_link_provider');

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/index.html.twig', [
            'employeeOptionsForm' => $employeeOptionsForm->createView(),
            'canOptionsBeChanged' => $employeeOptionsChecker->canBeChanged(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeGrid' => $this->presentGrid($employeeGrid),
            'helperCardDocumentationLink' => $helperCardDocumentationLinkProvider->getLink('team'),
        ]);
    }

    /**
     * Save employee options.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveOptionsAction(Request $request)
    {
        $employeeOptionsFormHandler = $this->get('prestashop.admin.employee_options.form_handler');
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();
        $employeeOptionsForm->handleRequest($request);

        if ($employeeOptionsForm->isSubmitted()) {
            $errors = $employeeOptionsFormHandler->save($employeeOptionsForm->getData());

            if (!empty($errors)) {
                $this->flashErrors($errors);

                return $this->redirectToRoute('admin_employees_index');
            }

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Toggle given employee status.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_employees_index")
     *
     * @param int $employeeId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($employeeId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleEmployeeStatusCommand(new EmployeeId($employeeId)));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Update status for employees in bulk action.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param string $newStatus
     *
     * @return RedirectResponse
     */
    public function bulkStatusUpdateAction(Request $request, $newStatus)
    {
        $employeeIds = $request->request->get('employee_employee_bulk');

        try {
            $this->getCommandBus()->handle(
                new BulkUpdateEmployeeStatusCommand($employeeIds, new EmployeeStatus($newStatus))
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Delete employee.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $employeeId
     *
     * @return RedirectResponse
     */
    public function deleteAction($employeeId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteEmployeeCommand(new EmployeeId($employeeId)));

            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Delete employees in bulk actions.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $employeeIds = $request->request->get('employee_employee_bulk');

        try {
            $this->getCommandBus()->handle(new BulkDeleteEmployeeCommand($employeeIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Show employee creation form page and handle it's submit.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $employeeForm = $this->getEmployeeFormBuilder()->getForm();
        $employeeForm->handleRequest($request);

        try {
            $result = $this->getEmployeeFormHandler()->handle($employeeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_index');
            }
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $templateVars = [
            'employeeForm' => $employeeForm->createView(),
            'showAddonsConnectButton' => false,
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/create.html.twig',
            $templateVars + $this->getFormTemplateVariables($request)
        );
    }

    /**
     * Show Employee edit page.
     *
     * @DemoRestricted(redirectRoute="admin_employees_index")
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_employees_index"
     * )
     *
     * @param int $employeeId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($employeeId, Request $request)
    {
        $formAccessChecker = $this->get('prestashop.adapter.employee.form_access_checker');

        if (!$formAccessChecker->canAccessEditFormFor($employeeId)) {
            $this->addFlash(
                'error',
                $this->trans('You cannot edit the SuperAdmin profile.', 'Admin.Advparameters.Notification')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        $isRestrictedAccess = $formAccessChecker->isRestrictedAccess((int) $employeeId);
        $canAccessAddonsConnect = $formAccessChecker->canAccessAddonsConnect();

        $employeeForm = $this->getEmployeeFormBuilder()->getFormFor((int) $employeeId, [], [
            'is_restricted_access' => $isRestrictedAccess,
            'is_for_editing' => true,
            'show_addons_connect_button' => $canAccessAddonsConnect,
        ]);

        try {
            $employeeForm->handleRequest($request);
            $result = $this->getEmployeeFormHandler()->handleFor((int) $employeeId, $employeeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_index');
            }
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $editableEmployee = $this->getQueryBus()->handle(new GetEmployeeForEditing((int) $employeeId));

        $templateVars = [
            'employeeForm' => $employeeForm->createView(),
            'isRestrictedAccess' => $isRestrictedAccess,
            'showAddonsConnectButton' => $canAccessAddonsConnect,
            'editableEmployee' => $editableEmployee,
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/edit.html.twig',
            $templateVars + $this->getFormTemplateVariables($request)
        );
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getEmployeeFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.employee_form_builder');
    }

    /**
     * @return FormHandler
     */
    protected function getEmployeeFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.employee_form_handler');
    }

    /**
     * Get human readable error messages.
     *
     * @param EmployeeException $e
     *
     * @return array
     */
    protected function getErrorMessages($e)
    {
        return [
            InvalidEmployeeIdException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                'Admin.Notifications.Error'
            ),
            EmployeeNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            AdminEmployeeException::class => [
                AdminEmployeeException::CANNOT_CHANGE_LAST_ADMIN => $this->trans(
                    'You cannot disable or delete the administrator account.',
                    'Admin.Advparameters.Notification'
                ),
            ],
            EmployeeCannotChangeItselfException::class => [
                EmployeeCannotChangeItselfException::CANNOT_CHANGE_STATUS => $this->trans(
                    'You cannot disable or delete your own account.',
                    'Admin.Advparameters.Notification'
                ),
            ],
            CannotDeleteEmployeeException::class => $this->trans(
                'Can\'t delete #%id%',
                'Admin.Notifications.Error',
                [
                    '%id%' => $e instanceof CannotDeleteEmployeeException ? $e->getEmployeeId()->getValue() : 0,
                ]
            ),
            MissingShopAssociationException::class => $this->trans(
                'The employee must be associated with at least one shop.',
                'Admin.Advparameters.Notification'
            ),
            InvalidProfileException::class => $this->trans(
                'The provided profile is invalid',
                'Admin.Advparameters.Notification'
            ),
            EmailAlreadyUsedException::class => sprintf(
                '%s %s',
                $this->trans(
                    'An account already exists for this email address:',
                    'Admin.Orderscustomers.Notification'
                ),
                $e instanceof EmailAlreadyUsedException ? $e->getEmail() : ''
            ),
            EmployeeConstraintException::class => [
                EmployeeConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Email', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INVALID_FIRST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Firstname', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INVALID_LAST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Lastname', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INCORRECT_PASSWORD => $this->trans(
                    'Your current password is invalid.',
                    'Admin.Advparameters.Notification'
                ),
            ],
        ];
    }

    /**
     * Get template variables that are same between create and edit forms.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getFormTemplateVariables(Request $request)
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');

        return [
            'level' => $this->authorizationLevel($request->attributes->get('_legacy_controller')),
            'requireAddonsSearch' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'superAdminProfileId' => $configuration->get('_PS_ADMIN_PROFILE_'),
            'getTabsUrl' => $this->generateUrl('admin_profiles_get_tabs'),
            'errorMessage' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
        ];
    }
}
