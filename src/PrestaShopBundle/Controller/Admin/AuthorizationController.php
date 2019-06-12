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
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendResetPasswordEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\FailedToSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\PasswordResetTooFrequentException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShopBundle\Form\Admin\Login\ForgotPasswordType;
use PrestaShopBundle\Form\Admin\Login\LoginType;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $authenticationError = $authenticationUtils->getLastAuthenticationError();

        if (null !== $authenticationError) {
            $errorMessage = $this->getErrorMessageForException(
                $authenticationError,
                $this->getErrorMessages()
            );
        }

        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);

        return $this->renderLoginPage([
            'errorMessage' => $errorMessage ?? null,
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
    public function sendResetPasswordLinkAction(Request $request)
    {
        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);
        $forgotPasswordForm->handleRequest($request);

        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()) {
            try {
                $this->getCommandBus()->handle(
                    new SendResetPasswordEmailCommand($forgotPasswordForm->getData()['email'])
                );

                $this->addFlash(
                    'success',
                    $this->trans(
                        'Please, check your mailbox. A link to reset your password has been sent to you.',
                        'Admin.Login.Notification'
                    )
                );
            } catch (DomainException $e) {
                $this->getErrorMessageForException($e, $this->getErrorMessages());
            }
        }

        return $this->renderLoginPage([
            'showResetPasswordForm' => true,
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
        ]);
    }

    /**
     * Render the login page.
     *
     * @param array $templateVars
     *
     * @return Response
     */
    private function renderLoginPage(array $templateVars = [])
    {
        $loginForm = $this->createForm(LoginType::class);
        $languageDataProvider = $this->get('prestashop.adapter.data_provider.language');

        return $this->render('@PrestaShop/Admin/Login/index.html.twig', $templateVars + [
            'loginForm' => $loginForm->createView(),
            'shopName' => $this->configuration->get('PS_SHOP_NAME'),
            'prestashopVersion' => $this->configuration->get('_PS_VERSION_'),
            'imgDir' => $this->configuration->get('_PS_IMG_'),
            'languageIso' => $languageDataProvider->getLanguageIsoById(
                $this->configuration->get('PS_LANG_DEFAULT')
            ),
            'currentYear' => (new DateTime())->format('Y'),
        ]);
    }

    /**
     * Get all authorization error messages.
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $employeeDoesNotExistMessage = $this->trans(
            'The employee does not exist, or the password provided is incorrect.',
            'Admin.Login.Notification'
        );

        return [
            BadCredentialsException::class => $employeeDoesNotExistMessage,
            UsernameNotFoundException::class => $employeeDoesNotExistMessage,
            DomainConstraintException::class => $this->trans(
                'Invalid email address.',
                'Admin.Notifications.Error'
            ),
            EmployeeNotFoundException::class => $this->trans(
                'This account does not exist.',
                'Admin.Login.Notification'
            ),
            PasswordResetTooFrequentException::class => $this->trans(
                'You can reset your password every %interval% minute(s) only. Please try again later.',
                'Admin.Login.Notification',
                ['%interval%' => $this->configuration->get('PS_PASSWD_TIME_BACK')]
            ),
            FailedToSendEmailException::class => $this->trans(
                'An error occurred while attempting to reset your password.',
                'Admin.Login.Notification'
            ),
        ];
    }
}
