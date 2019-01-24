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

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
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
            $this->addFlash('error', $this->getErrorForEmployeeException($e));
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
            $this->addFlash('error', $this->getErrorForEmployeeException($e));
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
            $this->addFlash('error', $this->getErrorForEmployeeException($e));
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
            $this->addFlash('error', $this->getErrorForEmployeeException($e));
        }

        return $this->redirectToRoute('admin_employees_index');
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
        $employeeForm = $this->getEmployeeFormBuilder()->getFormFor($employeeId);
        $employeeForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/edit.html.twig', [
            'layoutTitle' => $this->trans('Employees', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeForm' => $employeeForm->createView(),
        ]);
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getEmployeeFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.employee_form_builder');
    }

    /**
     * Get human readable error message for thrown employee exception.
     *
     * @param EmployeeException $e
     *
     * @return string
     */
    protected function getErrorForEmployeeException(EmployeeException $e)
    {
        $type = get_class($e);
        $code = $e->getCode();

        $errorMessages = [
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
        ];

        if (!isset($errorMessages[$type])) {
            return $this->getFallbackErrorMessage($type, $e->getCode());
        }

        if (!is_array($errorMessages[$type])) {
            return $errorMessages[$type];
        }

        if (isset($errorMessages[$type][$code])) {
            return $errorMessages[$type][$code];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }
}
