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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ToggleEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotDeleteEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\EmployeeFilters;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class EmployeeController handles pages under "Configure > Advanced Parameters > Team > Employees".
 */
class EmployeeController extends FrameworkBundleAdminController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    /**
     * Show employees list & options page.
     *
     * @param Request $request
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, EmployeeFilters $filters)
    {
        $employeeOptionsFormHandler = $this->get('prestashop.admin.employee_options.form_handler');
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();

        $employeeOptionsChecker = $this->get('prestashop.core.team.employee.configuration.options_checker');

        $employeeGridFactory = $this->get('prestashop.core.grid.factory.employee');
        $employeeGrid = $employeeGridFactory->getGrid($filters);

        $helperCardDocumentationLinkProvider =
            $this->get(DocumentationLinkProviderInterface::class);

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $this->getContext()->employee->id, ShowcaseCard::EMPLOYEES_CARD)
        );

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/index.html.twig', [
            'employeeOptionsForm' => $employeeOptionsForm->createView(),
            'canOptionsBeChanged' => $employeeOptionsChecker->canBeChanged(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeGrid' => $this->presentGrid($employeeGrid),
            'helperCardDocumentationLink' => $helperCardDocumentationLinkProvider->getLink('team'),
            'showcaseCardName' => ShowcaseCard::EMPLOYEES_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClosed,
            'enableSidebar' => true,
        ]);
    }

    /**
     * Save employee options.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
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

            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Toggle given employee status.
     *
     * @param int $employeeId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function toggleStatusAction($employeeId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleEmployeeStatusCommand((int) $employeeId));

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
     * Bulk enables employee status action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkStatusEnableAction(Request $request)
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

        try {
            $this->getCommandBus()->handle(
                new BulkUpdateEmployeeStatusCommand($employeeIds, true)
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
     * Bulk disables employee status action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkStatusDisableAction(Request $request)
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

        try {
            $this->getCommandBus()->handle(
                new BulkUpdateEmployeeStatusCommand($employeeIds, false)
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
     * @param int $employeeId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteAction($employeeId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteEmployeeCommand((int) $employeeId));

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    /**
     * Delete employees in bulk actions.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkDeleteAction(Request $request)
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

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
     * @param Request $request
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(Request $request)
    {
        $employeeForm = $this->getEmployeeFormBuilder()->getForm();
        $employeeForm->handleRequest($request);

        try {
            $result = $this->getEmployeeFormHandler()->handle($employeeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $templateVars = [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeForm' => $employeeForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New employee', 'Admin.Navigation.Menu'),
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/create.html.twig',
            $templateVars
        );
    }

    /**
     * Show Employee edit page.
     *
     * @param int $employeeId
     * @param Request $request
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    public function editAction($employeeId, Request $request)
    {
        $contextEmployeeProvider = $this->get('prestashop.adapter.data_provider.employee');

        // If employee is editing his own profile - he doesn't need to have access to the edit form.
        if ($contextEmployeeProvider->getId() != $employeeId) {
            if (!$this->isGranted(Permission::UPDATE, $request->get('_legacy_controller'))) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'You do not have permission to update this.',
                        'Admin.Notifications.Error'
                    )
                );

                return $this->redirectToRoute('admin_employees_index');
            }
        }

        $formAccessChecker = $this->get('prestashop.adapter.employee.form_access_checker');

        if (!$formAccessChecker->canAccessEditFormFor($employeeId)) {
            $this->addFlash(
                'error',
                $this->trans('You cannot edit the SuperAdmin profile.', 'Admin.Advparameters.Notification')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        $isRestrictedAccess = $formAccessChecker->isRestrictedAccess((int) $employeeId);

        try {
            $employeeForm = $this->getEmployeeFormBuilder()->getFormFor((int) $employeeId, [], [
                'is_restricted_access' => $isRestrictedAccess,
                'is_for_editing' => true,
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_employees_index');
        }

        try {
            $employeeForm->handleRequest($request);
            $result = $this->getEmployeeFormHandler()->handleFor((int) $employeeId, $employeeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                // If we are editing our own profile, we must set a new token before redirect to avoid compromised page
                // todo: to be improved when UserProvider is also improved.
                // @see https://github.com/PrestaShop/PrestaShop/pull/32861
                $redirectParameters = ['employeeId' => $result->getIdentifiableObjectId()];
                if ($contextEmployeeProvider->getId() === $result->getIdentifiableObjectId()) {
                    $newToken = $this->csrfTokenManager
                        ->getToken($employeeForm->get('email')->getData())
                        ->getValue();
                    $redirectParameters['_token'] = $newToken;
                }

                return $this->redirectToRoute('admin_employees_edit', $redirectParameters);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        try {
            $editableEmployee = $this->getQueryBus()->handle(new GetEmployeeForEditing((int) $employeeId));
        } catch (EmployeeNotFoundException $e) {
            return $this->redirectToRoute('admin_employees_index');
        }

        $templateVars = [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeForm' => $employeeForm->createView(),
            'isRestrictedAccess' => $isRestrictedAccess,
            'editableEmployee' => $editableEmployee,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing %lastname% %firstname%\'s profile',
                'Admin.Navigation.Menu',
                [
                    '%firstname%' => $editableEmployee->getFirstname()->getValue(),
                    '%lastname%' => $editableEmployee->getLastName()->getValue(),
                ]
            ),
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/edit.html.twig',
            $templateVars
        );
    }

    /**
     * Change navigation menu status for employee.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function toggleNavigationMenuAction(Request $request)
    {
        $navigationToggler = $this->get('prestashop.adapter.employee.navigation_menu_toggler');
        $navigationToggler->toggleNavigationMenuInCookies($request->request->getBoolean('shouldCollapse'));

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Change employee form language.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changeFormLanguageAction(Request $request)
    {
        $configuration = $this->getConfiguration();

        if ($configuration->getBoolean('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')) {
            $languageChanger = $this->get('prestashop.adapter.employee.form_language_changer');
            $languageChanger->changeLanguageInCookies($request->request->get('language_iso_code'));
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Get tabs which are accessible for given profile.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function getAccessibleTabsAction(Request $request)
    {
        $profileId = $request->query->get('profileId');
        $tabsDataProvider = $this->get(TabDataProvider::class);
        $contextEmployeeProvider = $this->get('prestashop.adapter.data_provider.employee');

        return $this->json(
            $tabsDataProvider->getViewableTabs($profileId, $contextEmployeeProvider->getLanguageId())
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
     * @param Exception $e
     *
     * @return array
     */
    protected function getErrorMessages(Exception $e)
    {
        return [
            UploadedImageConstraintException::class => $this->trans(
                'Image format not recognized, allowed formats are: %s',
                'Admin.Notifications.Error',
                [
                    implode(', ', ImageManager::MIME_TYPE_SUPPORTED),
                ]
            ),
            InvalidEmployeeIdException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                'Admin.Notifications.Error'
            ),
            EmployeeNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
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
                EmployeeConstraintException::INCORRECT_PASSWORD => $this->trans(
                    'Your current password is invalid.',
                    'Admin.Advparameters.Notification'
                ),
                EmployeeConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Email', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INVALID_FIRST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('First name', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INVALID_LAST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Last name', 'Admin.Global'))]
                ),
                EmployeeConstraintException::INVALID_PASSWORD => $this->trans(
                    'The password doesn\'t meet the password policy requirements.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
