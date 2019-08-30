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

namespace PrestaShopBundle\Controller\Admin;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetPasswordCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendResetPasswordEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\FailedToSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidPasswordException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\PasswordResetTooFrequentException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\ResetPasswordInformationMissingException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\ResetPasswordTokenExpiredException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\UnableToResetPasswordException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForPasswordReset;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\PasswordResettingEmployee;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Security\Exception\UnableToRenameAdminDirectoryException;
use PrestaShopBundle\Form\Admin\Login\ForgotPasswordType;
use PrestaShopBundle\Form\Admin\Login\LoginType;
use PrestaShopBundle\Form\Admin\Login\ResetPasswordType;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class AuthorizationController responsible for employee login page.
 */
class AuthorizationController extends FrameworkBundleAdminController
{
    /**
     * Render and handle login page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $authenticationError = $authenticationUtils->getLastAuthenticationError();
        $loginForm = $this->createForm(LoginType::class, [
            'redirect_url' => $request->query->get('redirect'),
        ]);

        if (null !== $authenticationError) {
            $this->addError(
                $this->getErrorMessageForException(
                    $authenticationError,
                    $this->getErrorMessages()
                )
            );
        }

        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);

        return $this->renderLoginPage($request, [
            'loginForm' => $loginForm->createView(),
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
        ]);
    }

    /**
     * Handle sending reset password link to the employee.
     *
     * @DemoRestricted(redirectRoute="_admin_login")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $loginForm = $this->createForm(LoginType::class);
        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);
        $forgotPasswordForm->handleRequest($request);
        $showForgotPasswordForm = true;

        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()) {
            $successMessage = $this->trans(
                'Check your mailbox and click on the link to reset your password.',
                'Admin.Login.Notification'
            );

            try {
                $this->getCommandBus()->handle(
                    new SendResetPasswordEmailCommand($forgotPasswordForm->getData()['email'])
                );

                $this->addSuccess($successMessage);
                $showForgotPasswordForm = false;
            } catch (EmployeeNotFoundException $e) {
                // Not showing an error message when employee is not found
                $this->addSuccess($successMessage);
                $showForgotPasswordForm = false;
            } catch (DomainException $e) {
                $this->addError($this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->renderLoginPage($request, [
            'loginForm' => $loginForm->createView(),
            'showLoginForm' => false,
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
            'showForgotPasswordForm' => $showForgotPasswordForm,
        ]);
    }

    /**
     * Handle password resetting.
     *
     * @DemoRestricted(redirectRoute="_admin_login")
     *
     * @param Request $request
     * @param int $employeeId
     * @param string $resetToken generated password reset token
     *
     * @return Response
     */
    public function resetPasswordAction(Request $request, $employeeId, $resetToken)
    {
        try {
            /** @var PasswordResettingEmployee $employee */
            $employee = $this->getQueryBus()->handle(new GetEmployeeForPasswordReset((int) $employeeId));
        } catch (DomainException $e) {
            $this->addError($this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('_admin_login');
        }

        $resetPasswordForm = $this->createForm(ResetPasswordType::class, null, [
            'email' => $employee->getEmail()->getValue(),
        ]);
        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            try {
                $this->getCommandBus()->handle(new ResetPasswordCommand(
                    $employee->getEmployeeId()->getValue(),
                    $employee->getEmail()->getValue(),
                    $resetToken,
                    $resetPasswordForm->getData()['reset_password']
                ));

                $this->addSuccess($this->trans(
                    'Your password has been changed successfully.',
                    'Admin.Login.Notification'
                ));

                return $this->redirectToRoute('_admin_login');
            } catch (EmployeeNotFoundException $e) {
                $this->addError($this->trans(
                    'An error occurred while attempting to reset your password.',
                    'Admin.Login.Notification'
                ));
            } catch (DomainException $e) {
                $this->addError($this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->renderLoginPage($request, [
            'showLoginForm' => false,
            'showForgotPasswordForm' => false,
            'showResetPasswordForm' => true,
            'resetPasswordForm' => $resetPasswordForm->createView(),
            'employeeId' => $employeeId,
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Render the login page.
     *
     * @param Request $request
     * @param array $templateVars
     *
     * @return Response
     */
    private function renderLoginPage(Request $request, array $templateVars = [])
    {
        $tools = $this->get('prestashop.adapter.tools');
        $languageDataProvider = $this->get('prestashop.adapter.data_provider.language');
        $secureModeChecker = $this->get('prestashop.adapter.security.secure_mode_checker');
        $boAccessPrerequisitesChecker = $this->get(
            'prestashop.adapter.security.backoffice_access_prerequisites_checker'
        );
        $adminDirectoryRenamer = $this->get('prestashop.adapter.security.admin_directory_renamer');
        $isInsecureMode = false;
        $canAccessInsecureMode = false;
        $installDirectoryExists = $boAccessPrerequisitesChecker->installDirectoryExists();
        $adminDirectoryRenamed = $boAccessPrerequisitesChecker->isAdminDirectoryRenamed();
        $newAdminDirectoryName = '';

        if (!$adminDirectoryRenamed) {
            try {
                $newAdminDirectoryName = $adminDirectoryRenamer->renameToRandomName();

                return $this->redirect(sprintf(
                    '%s/%s%s',
                    $tools->getShopDomainSsl(true),
                    $newAdminDirectoryName,
                    $this->generateUrl('_admin_login', [], UrlGeneratorInterface::RELATIVE_PATH)
                ));
            } catch (UnableToRenameAdminDirectoryException $e) {
                $newAdminDirectoryName = $e->getDestinationName();
            }
        }

        if ($secureModeChecker->isSslActivated() && !$secureModeChecker->isSslUsed()) {
            $isInsecureMode = true;
            $canAccessInsecureMode = $secureModeChecker->canIpAccessInsecureMode($request->getClientIp());
        }

        $templateVars += [
            'shopName' => $this->configuration->get('PS_SHOP_NAME'),
            'prestashopVersion' => $this->configuration->get('_PS_VERSION_'),
            'imgDir' => $this->configuration->get('_PS_IMG_'),
            'languageIso' => $languageDataProvider->getLanguageIsoById(
                $this->configuration->get('PS_LANG_DEFAULT')
            ),
            'currentYear' => (new DateTime())->format('Y'),
            'showLoginForm' => true,
            'showForgotPasswordForm' => false,
            'showResetPasswordForm' => false,
            'isInsecureMode' => $isInsecureMode,
            'canAccessInsecureMode' => $canAccessInsecureMode,
            'secureUrl' => $secureModeChecker->secureUrl(
                $this->generateUrl('_admin_login', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ),
            'installDirectoryExists' => $installDirectoryExists,
            'adminDirectoryRenamed' => $adminDirectoryRenamed,
            'newAdminDirectoryName' => $newAdminDirectoryName,
            'newAdminDirectoryUrl' => sprintf(
                '%s%s%s',
                $tools->getShopDomainSsl(true),
                $this->configuration->get('__PS_BASE_URI__'),
                $newAdminDirectoryName
            ),
        ];

        if (!$adminDirectoryRenamed || $installDirectoryExists) {
            $templateVars['showLoginForm'] = false;
            $templateVars['showForgotPasswordForm'] = false;
            $templateVars['showResetPasswordForm'] = false;
        }

        return $this->render('@PrestaShop/Admin/Login/index.html.twig', $templateVars);
    }

    /**
     * Get all authorization error messages.
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $invalidCredentialsMessage = $this->trans(
            'Invalid email address or password, please try again.',
            'Admin.Login.Notification'
        );

        $missingInformationMessage = $this->trans(
            'It looks like something went wrong, please try again.',
            'Admin.Login.Notification'
        );

        return [
            BadCredentialsException::class => $invalidCredentialsMessage,
            UsernameNotFoundException::class => $invalidCredentialsMessage,
            DomainConstraintException::class => $invalidCredentialsMessage,
            EmployeeNotFoundException::class => $invalidCredentialsMessage,
            PasswordResetTooFrequentException::class => $this->trans(
                'You can reset your password every %interval% minute(s) only. Please try again later.',
                'Admin.Login.Notification',
                ['%interval%' => $this->configuration->get('PS_PASSWD_TIME_BACK')]
            ),
            FailedToSendEmailException::class => $this->trans(
                'An error occurred while attempting to reset your password.',
                'Admin.Login.Notification'
            ),
            ResetPasswordInformationMissingException::class => $missingInformationMessage,
            InvalidEmployeeIdException::class => $missingInformationMessage,
            InvalidPasswordException::class => $this->trans(
                'The password is not in a valid format.',
                'Admin.Login.Notification'
            ),
            ResetPasswordTokenExpiredException::class => $this->trans(
                'Your password reset request expired. Please start again.',
                'Admin.Login.Notification'
            ),
            UnableToResetPasswordException::class => $this->trans(
                'An error occurred while attempting to change your password.',
                'Admin.Login.Notification'
            ),
            EmployeeConstraintException::class => [
                EmployeeConstraintException::INVALID_PASSWORD => $this->trans(
                    'Password should be at least 8 characters long',
                    'Admin.Login.Notification'
                ),
            ],
        ];
    }
}
