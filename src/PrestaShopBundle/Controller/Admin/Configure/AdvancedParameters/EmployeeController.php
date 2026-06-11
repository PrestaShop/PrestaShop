<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\TotMailer\TwoFactorAuthCodePrestashopMailer;
use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeeTwoFactorCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SetEmployeeTwoFactorSecretCommand;
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
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\EmployeeBackupCodeManager;
use PrestaShopBundle\SchebTwoFactor\TotpSecretEncryptor;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
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
        TotpAuthenticatorInterface $totpAuthenticator,
        EmployeeRepository $employeeRepository,
        TotpSecretEncryptor $totpSecretEncryptor,
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

        $twoFactorData = $this->buildTwoFactorFormData(
            $isRestrictedAccess,
            $configuration,
            $employeeRepository,
            $totpAuthenticator,
            $totpSecretEncryptor,
        );

        try {
            $employeeForm = $formBuilder->getFormFor((int) $employeeId, [], [
                'is_restricted_access' => $isRestrictedAccess,
                'is_for_editing' => true,
                'can_manage_two_factor_requirement' => $this->getEmployeeContext()->isSuperAdmin() && !$isRestrictedAccess,
                'qr_code_src' => $twoFactorData['qr_code_src'],
                'two_factor_totp_secret' => $twoFactorData['two_factor_totp_secret'],
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

        /** @var Employee|null $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);

        $templateVars = [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeForm' => $employeeForm->createView(),
            'isRestrictedAccess' => $isRestrictedAccess,
            'canResetTwoFactor' => $this->getEmployeeContext()->isSuperAdmin() && $this->getEmployeeContext()->getEmployee()->getId() !== $employeeId,
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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function generateBackupCodesAction(
        int $employeeId,
        Request $request,
        EmployeeFormAccessCheckerInterface $formAccessChecker,
        EmployeeRepository $employeeRepository,
        EmployeeBackupCodeManager $employeeBackupCodeManager,
        EntityManagerInterface $entityManager,
        ConfigurationInterface $configuration,
    ): RedirectResponse {
        if (!$this->isCsrfTokenValid('generate_backup_codes_' . $employeeId, (string) $request->request->get('_token_generate_backup_codes'))) {
            throw $this->createAccessDeniedException();
        }

        if ($this->getEmployeeContext()->getEmployee()->getId() !== $employeeId) {
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

        if (!$formAccessChecker->isRestrictedAccess($employeeId)) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Backup codes can only be generated from the employee self-service profile page.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        if (!(bool) $configuration->get('PS_BACKOFFICE_2FA')) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Two-factor authentication must be enabled before generating backup codes.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        /** @var Employee|null $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);

        if (null === $employee) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        if (
            !$employee->getTwoFactorEnabled()
            || (!$employee->isTotpAuthenticationEnabled() && !$employee->getTwoFactorEmailEnabled())
        ) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Enable at least one two-factor authentication method before generating backup codes.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        if (!empty($employee->getTwoFactorBackupCodes())) {
            return $this->redirectToRoute('admin_employees_confirm_regenerate_backup_codes', ['employeeId' => $employeeId]);
        }

        $backupCodeSet = $employeeBackupCodeManager->generateBackupCodeSet();

        $employee->setTwoFactorBackupCodes($backupCodeSet['hashedBackupCodes']);
        $entityManager->persist($employee);
        $entityManager->flush();

        $request->getSession()->getFlashBag()->set('backup_codes', $backupCodeSet['plainBackupCodes']);
        $this->addFlash(
            'success',
            $this->trans(
                'Backup codes generated successfully. Save them now: they will not be shown again.',
                [],
                'Admin.Notifications.Success'
            )
        );

        return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function confirmRegenerateBackupCodesAction(
        int $employeeId,
        Request $request,
        EmployeeFormAccessCheckerInterface $formAccessChecker,
        EmployeeRepository $employeeRepository,
        ConfigurationInterface $configuration,
        TwoFactorAuthCodePrestashopMailer $twoFactorAuthCodePrestashopMailer,
        UserTokenManager $userTokenManager,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getEmployeeContext()->getEmployee()->getId() !== $employeeId) {
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

        if (!$formAccessChecker->isRestrictedAccess($employeeId)) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Backup codes can only be managed from the employee self-service profile page.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        /** @var Employee|null $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);

        if (null === $employee) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_employees_index');
        }

        if (
            !(bool) $configuration->get('PS_BACKOFFICE_2FA')
            || !$employee->getTwoFactorEnabled()
            || (!$employee->isTotpAuthenticationEnabled() && !$employee->getTwoFactorEmailEnabled())
        ) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Enable at least one two-factor authentication method before regenerating backup codes.',
                    [],
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        if (empty($employee->getTwoFactorBackupCodes())) {
            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        $availableTwoFactorProviders = [];
        if ($employee->isTotpAuthenticationEnabled()) {
            $availableTwoFactorProviders[] = 'totp';
        }
        if ($employee->getTwoFactorEmailEnabled()) {
            $availableTwoFactorProviders[] = 'email';
        }

        $preferredProvider = (string) $request->query->get('preferProvider');
        $twoFactorProvider = in_array($preferredProvider, $availableTwoFactorProviders, true)
            ? $preferredProvider
            : $availableTwoFactorProviders[0];

        $adminQueryToken = $userTokenManager->getSymfonyToken();
        $shouldSendEmailCode = $twoFactorProvider === 'email'
            && ($request->query->getBoolean('sendCode') || null === $employee->getEmailAuthCode());

        if ($shouldSendEmailCode) {
            $employee->setEmailAuthCode(RandomString::generateFromCharacters('0123456789', 6));
            $entityManager->persist($employee);
            $entityManager->flush();
            $twoFactorAuthCodePrestashopMailer->sendAuthCode($employee);

            $this->addFlash(
                'info',
                $this->trans(
                    'A verification code has been sent to your email address.',
                    [],
                    'Admin.TwoFactor.Login'
                )
            );

            if ($request->query->getBoolean('sendCode')) {
                return $this->redirectToRoute('admin_employees_confirm_regenerate_backup_codes', [
                    'employeeId' => $employeeId,
                    'preferProvider' => $twoFactorProvider,
                    '_token' => $adminQueryToken,
                ]);
            }
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/confirm_regenerate_backup_codes.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'employeeId' => $employeeId,
            'authenticationError' => null,
            'authenticationErrorData' => [],
            'checkPathRoute' => 'admin_employees_regenerate_backup_codes',
            'checkPathUrl' => $this->generateUrl('admin_employees_regenerate_backup_codes', ['employeeId' => $employeeId, 'preferProvider' => $twoFactorProvider, '_token' => $adminQueryToken]),
            'authCodeParameterName' => '_auth_code',
            'displayTrustedOption' => false,
            'isCsrfProtectionEnabled' => true,
            'csrfParameterName' => '_token',
            'csrfTokenId' => 'regenerate_backup_codes_' . $employeeId,
            'submitLabel' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
            'availableTwoFactorProviders' => $availableTwoFactorProviders,
            'providerSwitchRoute' => 'admin_employees_confirm_regenerate_backup_codes',
            'providerSwitchRouteParams' => ['employeeId' => $employeeId, 'sendCode' => 1, '_token' => $adminQueryToken],
            'twoFactorProvider' => $twoFactorProvider,
            'logoutPath' => null,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_employees_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_employees_index')]
    public function regenerateBackupCodesAction(
        int $employeeId,
        Request $request,
        EmployeeFormAccessCheckerInterface $formAccessChecker,
        EmployeeRepository $employeeRepository,
        EmployeeBackupCodeManager $employeeBackupCodeManager,
        EntityManagerInterface $entityManager,
        ConfigurationInterface $configuration,
        TotpAuthenticatorInterface $totpAuthenticator,
        TwoFactorAuthCodePrestashopMailer $twoFactorAuthCodePrestashopMailer,
        UserTokenManager $userTokenManager,
    ): Response {
        if (!$this->isCsrfTokenValid('regenerate_backup_codes_' . $employeeId, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($this->getEmployeeContext()->getEmployee()->getId() !== $employeeId) {
            return $this->redirectToRoute('admin_employees_index');
        }

        if (!$formAccessChecker->isRestrictedAccess($employeeId)) {
            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        /** @var Employee|null $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);

        if (
            null === $employee
            || !(bool) $configuration->get('PS_BACKOFFICE_2FA')
            || !$employee->getTwoFactorEnabled()
            || (!$employee->isTotpAuthenticationEnabled() && !$employee->getTwoFactorEmailEnabled())
        ) {
            return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
        }

        $authCode = trim((string) $request->request->get('_auth_code'));
        $adminQueryToken = $userTokenManager->getSymfonyToken();
        $availableTwoFactorProviders = [];
        if ($employee->isTotpAuthenticationEnabled()) {
            $availableTwoFactorProviders[] = 'totp';
        }
        if ($employee->getTwoFactorEmailEnabled()) {
            $availableTwoFactorProviders[] = 'email';
        }

        $preferredProvider = (string) $request->query->get('preferProvider');
        $twoFactorProvider = in_array($preferredProvider, $availableTwoFactorProviders, true)
            ? $preferredProvider
            : $availableTwoFactorProviders[0];

        if (!$this->isBackupCodeRegenerationVerificationCodeValid($employee, $twoFactorProvider, $authCode, $totpAuthenticator)) {
            if ($twoFactorProvider === 'email' && null === $employee->getEmailAuthCode()) {
                $employee->setEmailAuthCode(RandomString::generateFromCharacters('0123456789', 6));
                $entityManager->persist($employee);
                $entityManager->flush();
                $twoFactorAuthCodePrestashopMailer->sendAuthCode($employee);
            }

            return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Employee/confirm_regenerate_backup_codes.html.twig', [
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'employeeId' => $employeeId,
                'authenticationError' => 'code_invalid',
                'authenticationErrorData' => [],
                'checkPathRoute' => 'admin_employees_regenerate_backup_codes',
                'checkPathUrl' => $this->generateUrl('admin_employees_regenerate_backup_codes', ['employeeId' => $employeeId, 'preferProvider' => $twoFactorProvider, '_token' => $adminQueryToken]),
                'authCodeParameterName' => '_auth_code',
                'displayTrustedOption' => false,
                'isCsrfProtectionEnabled' => true,
                'csrfParameterName' => '_token',
                'csrfTokenId' => 'regenerate_backup_codes_' . $employeeId,
                'submitLabel' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
                'availableTwoFactorProviders' => $availableTwoFactorProviders,
                'providerSwitchRoute' => 'admin_employees_confirm_regenerate_backup_codes',
                'providerSwitchRouteParams' => ['employeeId' => $employeeId, 'sendCode' => 1, '_token' => $adminQueryToken],
                'twoFactorProvider' => $twoFactorProvider,
                'logoutPath' => null,
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Regenerate backup codes', [], 'Admin.Advparameters.Feature'),
            ]);
        }

        $backupCodeSet = $employeeBackupCodeManager->generateBackupCodeSet();
        $employee->setTwoFactorBackupCodes($backupCodeSet['hashedBackupCodes']);
        $employee->setEmailAuthCode(null);
        $entityManager->persist($employee);
        $entityManager->flush();

        $request->getSession()->getFlashBag()->set('backup_codes', $backupCodeSet['plainBackupCodes']);
        $this->addFlash(
            'success',
            $this->trans(
                'Backup codes regenerated successfully. Save them now: they will not be shown again.',
                [],
                'Admin.Notifications.Success'
            )
        );

        return $this->redirectToRoute('admin_employees_edit', ['employeeId' => $employeeId]);
    }

    private function isBackupCodeRegenerationVerificationCodeValid(
        Employee $employee,
        string $twoFactorProvider,
        string $authCode,
        TotpAuthenticatorInterface $totpAuthenticator,
    ): bool {
        if ($authCode === '') {
            return false;
        }

        if ($twoFactorProvider === 'totp') {
            return $totpAuthenticator->checkCode($employee, $authCode);
        }

        $expectedCode = $employee->getEmailAuthCode();

        return null !== $expectedCode && hash_equals($expectedCode, $authCode);
    }

    private function buildTwoFactorFormData(
        bool $isRestrictedAccess,
        $configuration,
        EmployeeRepository $employeeRepository,
        TotpAuthenticatorInterface $totpAuthenticator,
        $totpSecretEncryptor
    ): array {
        if (!$isRestrictedAccess || !$configuration->get('PS_BACKOFFICE_2FA')) {
            return [
                'qr_code_src' => '',
                'two_factor_totp_secret' => '',
            ];
        }

        $employeeId = (int) $this->getEmployeeContext()->getEmployee()->getId();

        /** @var Employee $employee */
        $employee = $employeeRepository->findOneBy(['id' => $employeeId]);

        if ($employee->getTwoFactorSecret()) {
            $twoFactorTotpSecretPlain = $employee->getTwoFactorTotpSecretPlain();
        } else {
            $twoFactorTotpSecretPlain = $totpAuthenticator->generateSecret();

            $this->dispatchCommand(
                new SetEmployeeTwoFactorSecretCommand(
                    $employeeId,
                    $totpSecretEncryptor->encrypt($twoFactorTotpSecretPlain),
                    $twoFactorTotpSecretPlain
                )
            );
        }

        $qrCodeContent = $totpAuthenticator->getQRContent($employee);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrCodeContent)
            ->size(220)
            ->margin(10)
            ->build();

        return [
            'qr_code_src' => 'data:image/png;base64,' . base64_encode($result->getString()),
            'two_factor_totp_secret' => $twoFactorTotpSecretPlain,
        ];
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
