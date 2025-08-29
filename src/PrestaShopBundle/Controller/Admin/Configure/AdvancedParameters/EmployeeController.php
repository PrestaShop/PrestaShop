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
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\FormLanguageChangerInterface;
use PrestaShop\PrestaShop\Core\Employee\NavigationMenuTogglerInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\EmployeeFilters;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSL;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShop\PrestaShop\Core\Team\Employee\Configuration\OptionsCheckerInterface;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeController handles pages under "Configure > Advanced Parameters > Team > Employees".
 */
class EmployeeController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        EmployeeFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.employee')]
        GridFactoryInterface $employeeGridFactory,
        #[Autowire(service: 'prestashop.admin.employee_options.form_handler')]
        ConfigurationFormHandlerInterface $employeeOptionsFormHandler,
        OptionsCheckerInterface $employeeOptionsChecker,
        DocumentationLinkProviderInterface $helperCardDocumentationLinkProvider,
    ): Response {
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();
        $employeeGrid = $employeeGridFactory->getGrid($filters);

        $showcaseCardIsClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed($this->getEmployeeContext()->getEmployee()->getId(), ShowcaseCard::EMPLOYEES_CARD)
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
    public function saveOptionsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.employee_options.form_handler')]
        ConfigurationFormHandlerInterface $employeeOptionsFormHandler,
    ): RedirectResponse {
        $employeeOptionsForm = $employeeOptionsFormHandler->getForm();
        $employeeOptionsForm->handleRequest($request);

        if ($employeeOptionsForm->isSubmitted()) {
            $errors = $employeeOptionsFormHandler->save($employeeOptionsForm->getData());

            if (!empty($errors)) {
                $this->addFlashErrors($errors);

                return $this->redirectToRoute('admin_employees_index');
            }

            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function toggleStatusAction(int $employeeId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleEmployeeStatusCommand((int) $employeeId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkStatusEnableAction(Request $request): RedirectResponse
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

        try {
            $this->dispatchCommand(
                new BulkUpdateEmployeeStatusCommand($employeeIds, true)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkStatusDisableAction(Request $request): RedirectResponse
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

        try {
            $this->dispatchCommand(
                new BulkUpdateEmployeeStatusCommand($employeeIds, false)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteAction(int $employeeId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteEmployeeCommand((int) $employeeId));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $employeeIds = $request->request->all('employee_employee_bulk');

        try {
            $this->dispatchCommand(new BulkDeleteEmployeeCommand($employeeIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.employee_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.employee_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $employeeForm = $formBuilder->getForm($request->request->all('employee'));
        $employeeForm->handleRequest($request);

        try {
            $result = $formHandler->handle($employeeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        $templateVars = [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeForm' => $employeeForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New employee', [], 'Admin.Navigation.Menu'),
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/create.html.twig',
            $templateVars
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    public function editAction(
        int $employeeId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.employee_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.employee_form_handler')]
        FormHandlerInterface $formHandler,
        EmployeeFormAccessCheckerInterface $formAccessChecker,
    ): Response {
        // If employee is editing his own profile - he doesn't need to have access to the edit form.
        if ($this->getEmployeeContext()->getEmployee()->getId() != $employeeId) {
            if (!$this->isGranted(Permission::UPDATE, $request->get('_legacy_controller'))) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'You do not have permission to update this.',
                        [],
                        'Admin.Notifications.Error'
                    )
                );

                return $this->redirectToRoute('admin_employees_index');
            }
        }

        if (!$formAccessChecker->canAccessEditFormFor($employeeId)) {
            $this->addFlash(
                'error',
                $this->trans('You cannot edit the SuperAdmin profile.', [], 'Admin.Advparameters.Notification')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        $isRestrictedAccess = $formAccessChecker->isRestrictedAccess((int) $employeeId);

        try {
            $employeeForm = $formBuilder->getFormFor((int) $employeeId, [], [
                'is_restricted_access' => $isRestrictedAccess,
                'is_for_editing' => true,
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_employees_index');
        }

        try {
            $employeeForm->handleRequest($request);
            $result = $formHandler->handleFor($employeeId, $employeeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        try {
            $editableEmployee = $this->dispatchQuery(new GetEmployeeForEditing((int) $employeeId));
        } catch (EmployeeNotFoundException) {
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
                [
                    '%firstname%' => $editableEmployee->getFirstname()->getValue(),
                    '%lastname%' => $editableEmployee->getLastName()->getValue(),
                ],
                'Admin.Navigation.Menu',
            ),
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/edit.html.twig',
            $templateVars
        );
    }

    public function toggleNavigationMenuAction(
        Request $request,
        NavigationMenuTogglerInterface $navigationMenuToggler,
    ): Response {
        $navigationMenuToggler->toggleNavigationMenuInCookies($request->request->getBoolean('shouldCollapse'));

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function changeFormLanguageAction(
        Request $request,
        FormLanguageChangerInterface $formLanguageChanger,
    ): Response {
        if ((bool) $this->getConfiguration()->get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')) {
            $formLanguageChanger->changeLanguageInCookies($request->request->get('language_iso_code'));
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
    public function getAccessibleTabsAction(
        Request $request,
        TabDataProvider $tabDataProvider,
    ): JsonResponse {
        return $this->json(
            $tabDataProvider->getViewableTabs($request->query->get('profileId'), $this->getEmployeeContext()->getEmployee()->getLanguageId())
        );
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function generatePasswordAction(): JsonResponse
    {
        return $this->json([
            'password' => mb_strtolower((new PasswordGenerator(new OpenSSL()))->generatePassword(16, PasswordGenerator::PASSWORDGEN_FLAG_ALPHANUMERIC)),
        ]);
    }

    /**
     * Get human readable error messages.
     *
     * @param Exception $e
     *
     * @return array
     */
    protected function getErrorMessages(Exception $e): array
    {
        return [
            UploadedImageConstraintException::class => $this->trans(
                'Image format not recognized, allowed formats are: %s',
                [
                    implode(', ', ImageManager::MIME_TYPE_SUPPORTED),
                ],
                'Admin.Notifications.Error',
            ),
            InvalidEmployeeIdException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                [],
                'Admin.Notifications.Error'
            ),
            EmployeeNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            AdminEmployeeException::class => [
                AdminEmployeeException::CANNOT_CHANGE_LAST_ADMIN => $this->trans(
                    'You cannot disable or delete the administrator account.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ],
            EmployeeCannotChangeItselfException::class => [
                EmployeeCannotChangeItselfException::CANNOT_CHANGE_STATUS => $this->trans(
                    'You cannot disable or delete your own account.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ],
            CannotDeleteEmployeeException::class => $this->trans(
                'Can\'t delete #%id%',
                [
                    '%id%' => $e instanceof CannotDeleteEmployeeException ? $e->getEmployeeId()->getValue() : 0,
                ],
                'Admin.Notifications.Error',
            ),
            MissingShopAssociationException::class => $this->trans(
                'The employee must be associated with at least one shop.',
                [],
                'Admin.Advparameters.Notification'
            ),
            InvalidProfileException::class => $this->trans(
                'The provided profile is invalid',
                [],
                'Admin.Advparameters.Notification'
            ),
            EmailAlreadyUsedException::class => sprintf(
                '%s %s',
                $this->trans(
                    'An account already exists for this email address:',
                    [],
                    'Admin.Orderscustomers.Notification'
                ),
                $e instanceof EmailAlreadyUsedException ? $e->getEmail() : ''
            ),
            EmployeeConstraintException::class => [
                EmployeeConstraintException::INCORRECT_PASSWORD => $this->trans(
                    'Your current password is invalid.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                EmployeeConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Email', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                EmployeeConstraintException::INVALID_FIRST_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('First name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                EmployeeConstraintException::INVALID_LAST_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Last name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                EmployeeConstraintException::INVALID_PASSWORD => $this->trans(
                    'The password doesn\'t meet the password policy requirements.',
                    [],
                    'Admin.Notifications.Error'
                ),
                EmployeeConstraintException::INVALID_HOMEPAGE => $this->trans(
                    'The selected default page is not accessible by the selected profile.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
