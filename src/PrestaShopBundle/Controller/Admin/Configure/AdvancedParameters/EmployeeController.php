<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\GenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\InitializeEmployeeTotpSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\PrepareEmployeeBackupCodeRegenerationCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\RegenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeeTwoFactorCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ToggleEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotDeleteEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;
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
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\UserTokenManager;
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
        EmployeeRepository $employeeRepository,
        ConfigurationInterface $configuration,
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

        /** @var Employee|null $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);
        $twoFactorData = $isRestrictedAccess && $configuration->get('PS_BACKOFFICE_2FA')
            ? $this->dispatchQuery(new GetEmployeeTwoFactorSettings($employeeId))
            : null;

        try {
            $employeeForm = $formBuilder->getFormFor((int) $employeeId, [], [
                'is_restricted_access' => $isRestrictedAccess,
                'is_for_editing' => true,
                'can_manage_two_factor_requirement' => $this->getEmployeeContext()->isSuperAdmin() && !$isRestrictedAccess,
                'qr_code_src' => $twoFactorData?->getQrCodeSrc() ?? '',
                'two_factor_totp_secret' => $twoFactorData?->getTotpSecret() ?? '',
            ]);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_employees_index');
        }

        try {
            $employeeForm->handleRequest($request);
            $result = $formHandler->handleFor($employeeId, $employeeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $updateResult = $result->getIdentifiableObjectId();
                if ($isRestrictedAccess && $updateResult instanceof GeneratedEmployeeBackupCodes) {
                    $this->flashBackupCodes($updateResult);
                }

                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            if ($isRestrictedAccess && $employeeForm->isSubmitted() && $employeeForm->isValid() && $employeeForm->has('two_factor_enabled')) {
                $this->addFlash('warning', $this->trans(
                    'Some changes may already have been saved. Review your two-factor authentication settings and generate or regenerate backup codes if necessary.',
                    [],
                    'Admin.Advparameters.Notification'
                ));
            }
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
            'canResetTwoFactor' => $this->getEmployeeContext()->isSuperAdmin()
                && $this->getEmployeeContext()->getEmployee()->getId() !== $employeeId
                && null !== $employee
                && ($employee->isTotpAuthenticationEnabled() || $employee->isEmailAuthEnabled()),
            'hasBackupCodes' => !empty($employee?->getTwoFactorBackupCodes()),
            'canManageBackupCodes' => $isRestrictedAccess
                && null !== $employee
                && $employee->getTwoFactorEnabled()
                && ($employee->isTotpAuthenticationEnabled() || $employee->getTwoFactorEmailEnabled()),
            'editableEmployeeId' => $employeeId,
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

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function resetTwoFactorAction(int $employeeId): RedirectResponse
    {
        if (!$this->getEmployeeContext()->isSuperAdmin()) {
            $this->addFlash(
                'error',
                $this->trans('You do not have permission to update this.', [], 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        if ($this->getEmployeeContext()->getEmployee()->getId() === $employeeId) {
            $this->addFlash(
                'error',
                $this->trans('You cannot reset 2FA on your own account from this page.', [], 'Admin.Advparameters.Notification')
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        try {
            $this->dispatchCommand(new ResetEmployeeTwoFactorCommand($employeeId));

            $this->addFlash(
                'success',
                $this->trans('2FA has been reset for this employee.', [], 'Admin.Notifications.Success')
            );
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')", redirectRoute: 'admin_employees_index')]
    public function initializeTotpSecretAction(int $employeeId, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('initialize_totp_' . $employeeId, (string) $request->request->get('_token_initialize_totp'))) {
            throw $this->createAccessDeniedException();
        }
        try {
            $this->dispatchCommand(new InitializeEmployeeTotpSecretCommand($employeeId));
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
            }
            $settings = $this->dispatchQuery(new GetEmployeeTwoFactorSettings($employeeId));

            return $this->json([
                'qrCodeSrc' => $settings->getQrCodeSrc(),
                'totpSecret' => $settings->getTotpSecret(),
            ], Response::HTTP_OK, ['Cache-Control' => 'no-store']);
        } catch (EmployeeException $e) {
            $message = $this->getErrorMessageForException($e, $this->getErrorMessages($e));
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => $message], Response::HTTP_BAD_REQUEST, ['Cache-Control' => 'no-store']);
            }
            $this->addFlash('error', $message);

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')", redirectRoute: 'admin_employees_index')]
    public function generateBackupCodesAction(int $employeeId, Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('generate_backup_codes_' . $employeeId, (string) $request->request->get('_token_generate_backup_codes'))) {
            throw $this->createAccessDeniedException();
        }
        try {
            try {
                $result = $this->dispatchCommand(new GenerateEmployeeBackupCodesCommand($employeeId));
            } catch (EmployeeTwoFactorException $e) {
                if (EmployeeTwoFactorException::CONFIRMATION_REQUIRED !== $e->getCode()) {
                    throw $e;
                }
                $provider = $this->dispatchCommand(new PrepareEmployeeBackupCodeRegenerationCommand($employeeId));

                return $this->redirectToRoute('admin_employees_confirm_regenerate_backup_codes', ['employeeId' => $employeeId, 'preferProvider' => $provider]);
            }
            $this->flashBackupCodes($result);
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')", redirectRoute: 'admin_employees_index')]
    public function prepareRegenerateBackupCodesAction(int $employeeId, Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('prepare_backup_codes_' . $employeeId, (string) $request->request->get('_token_prepare_backup_codes'))) {
            throw $this->createAccessDeniedException();
        }
        try {
            $provider = $this->dispatchCommand(new PrepareEmployeeBackupCodeRegenerationCommand(
                $employeeId,
                (string) $request->query->get('preferProvider'),
                $request->query->getBoolean('sendCode')
            ));
            if ('email' === $provider) {
                $this->addFlash('info', $this->trans('Use the verification code sent to your email address.', [], 'Admin.Login.Notification'));
            }

            return $this->redirectToRoute('admin_employees_confirm_regenerate_backup_codes', ['employeeId' => $employeeId, 'preferProvider' => $provider]);
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')", redirectRoute: 'admin_employees_index')]
    public function confirmRegenerateBackupCodesAction(int $employeeId, Request $request, UserTokenManager $userTokenManager): Response
    {
        return $this->renderBackupCodeConfirmation($employeeId, $request, $userTokenManager);
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')", redirectRoute: 'admin_employees_index')]
    public function regenerateBackupCodesAction(int $employeeId, Request $request, UserTokenManager $userTokenManager): Response
    {
        if (!$this->isCsrfTokenValid('regenerate_backup_codes_' . $employeeId, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }
        try {
            $result = $this->dispatchCommand(new RegenerateEmployeeBackupCodesCommand(
                $employeeId,
                (string) $request->query->get('preferProvider'),
                (string) $request->request->get('_auth_code')
            ));
            $this->flashBackupCodes($result, true);
        } catch (EmployeeTwoFactorException $e) {
            if (EmployeeTwoFactorException::INVALID_CODE === $e->getCode()) {
                return $this->renderBackupCodeConfirmation($employeeId, $request, $userTokenManager, 'code_invalid');
            }
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
    }

    private function renderBackupCodeConfirmation(int $employeeId, Request $request, UserTokenManager $userTokenManager, ?string $authenticationError = null): Response
    {
        try {
            $settings = $this->dispatchQuery(new GetEmployeeTwoFactorSettings($employeeId, (string) $request->query->get('preferProvider')));
        } catch (EmployeeException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_employees_index');
        }
        if (!$settings->isEnabled() || !$settings->hasBackupCodes()) {
            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }
        $adminQueryToken = $userTokenManager->getSymfonyToken();
        $provider = $settings->getPreferredProvider();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/confirm_regenerate_backup_codes.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeId' => $employeeId,
            'authenticationError' => $authenticationError,
            'emailCodePreparationRequired' => $settings->isEmailCodePreparationRequired(),
            'authenticationErrorData' => [],
            'checkPathRoute' => 'admin_employees_regenerate_backup_codes',
            'checkPathUrl' => $this->generateUrl('admin_employees_regenerate_backup_codes', ['employeeId' => $employeeId, 'preferProvider' => $provider, '_token' => $adminQueryToken]),
            'authCodeParameterName' => '_auth_code',
            'displayTrustedOption' => false,
            'isCsrfProtectionEnabled' => true,
            'csrfParameterName' => '_token',
            'csrfTokenId' => 'regenerate_backup_codes_' . $employeeId,
            'submitLabel' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
            'availableTwoFactorProviders' => $settings->getAvailableProviders(),
            'providerSwitchRoute' => 'admin_employees_prepare_regenerate_backup_codes',
            'providerSwitchRouteParams' => ['employeeId' => $employeeId, 'sendCode' => 1, '_token' => $adminQueryToken],
            'providerSwitchMethod' => 'post',
            'providerSwitchCsrfTokenId' => 'prepare_backup_codes_' . $employeeId,
            'providerSwitchCsrfParameter' => '_token_prepare_backup_codes',
            'twoFactorProvider' => $provider,
            'logoutPath' => null,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
        ]);
    }

    private function flashBackupCodes(GeneratedEmployeeBackupCodes $result, bool $regenerated = false): void
    {
        foreach ($result->getBackupCodes() as $backupCode) {
            $this->addFlash('backup_codes', $backupCode);
        }
        $this->addFlash('success', $this->trans(
            $regenerated
                ? 'Backup codes regenerated successfully. Save them now: they will not be shown again.'
                : 'Backup codes generated successfully. Save them now: they will not be shown again.',
            [],
            'Admin.Notifications.Success'
        ));
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
            $tabDataProvider->getViewableTabs($request->query->getInt('profileId'), $this->getEmployeeContext()->getEmployee()->getLanguageId())
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
            EmployeeTwoFactorException::class => [
                EmployeeTwoFactorException::NOT_SELF => $this->trans('Backup codes can only be managed from the employee self-service profile page.', [], 'Admin.Advparameters.Notification'),
                EmployeeTwoFactorException::DISABLED => $this->trans('Two-factor authentication must be enabled before generating backup codes.', [], 'Admin.Advparameters.Notification'),
                EmployeeTwoFactorException::NOT_CONFIGURED => $this->trans('Enable at least one two-factor authentication method before regenerating backup codes.', [], 'Admin.Advparameters.Notification'),
                EmployeeTwoFactorException::INVALID_CODE => $this->trans('code_invalid', [], 'Admin.Login.Notification'),
                EmployeeTwoFactorException::CONCURRENT_UPDATE => $this->trans('Your two-factor authentication settings changed during this operation. Reload your profile and try again.', [], 'Admin.Advparameters.Notification'),
            ],
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
