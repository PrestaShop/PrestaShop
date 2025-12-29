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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSL;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Security\Admin\EmployeeHomepageProvider;
use PrestaShopBundle\Security\Admin\Exception\InvalidResetPasswordTokenException;
use PrestaShopBundle\Security\Admin\Exception\PasswordResetTemporarilyBlockedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Throwable;

class LoginController extends PrestaShopAdminController
{
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly string $projectDir,
        private readonly string $adminFolderName,
    ) {
    }

    /**
     * This route and controller are defined in the firewall as login_path and check_path
     * so the controller doesn't need to handle the form submission logic, it is handled
     * internally by the FormLoginAuthenticator
     *
     * See https://symfony.com/doc/current/security.html#form-login
     *
     * @param Security $security
     *
     * @return Response
     */
    public function loginAction(
        Request $request,
        Security $security,
        AuthenticationUtils $authenticationUtils,
        #[Autowire(service: 'prestashop.admin.login.form_handler')]
        FormHandlerInterface $loginFormHandler,
        #[Autowire(service: 'prestashop.admin.request_password_reset.form_handler')]
        FormHandlerInterface $requestResetPasswordFormHandler,
    ): Response {
        $securityResponse = $this->checkRequiredActions($request);
        if ($securityResponse) {
            return $securityResponse;
        }

        if ($security->getUser()) {
            return $this->redirectToRoute('admin_homepage');
        }

        $loginForm = $loginFormHandler->getForm();
        $requestPasswordResetForm = $requestResetPasswordFormHandler->getForm();

        if ($authenticationUtils->getLastAuthenticationError() instanceof AuthenticationException) {
            $this->addFlash('error', $this->trans('The employee does not exist, or the password provided is incorrect.', [], 'Admin.Login.Notification'));
        }

        return $this->renderLoginPage($loginForm, $requestPasswordResetForm, false);
    }

    /**
     * This controller is not even called since the logout_path is defined in the firewall
     * so the logout path is watched and Symfony handles the logout part and the redirection
     * but we still need to define a route to benefit from the _legacy_link feature so it
     * doesn't hurt to have a consistent controller here anyway.
     *
     * See https://symfony.com/doc/current/security.html#logging-out
     *
     * @param Security $security
     *
     * @return RedirectResponse
     */
    public function logoutAction(Security $security): RedirectResponse
    {
        if ($security->getUser()) {
            $security->logout();
        }

        return $this->redirectToRoute('admin_login');
    }

    /**
     * Automatically redirects to the Employee configured homepage, or AdminDashboard
     * as a fallback, or to the login in case the employee is not logged in.
     */
    public function homepageAction(Security $security, EmployeeHomepageProvider $employeeHomepageProvider): RedirectResponse
    {
        $loggedUser = $security->getUser();
        if ($loggedUser instanceof Employee) {
            return $this->redirect($employeeHomepageProvider->getHomepageUrl());
        }

        return $this->redirectToRoute('admin_login');
    }

    public function requestPasswordResetAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.login.form_handler')]
        FormHandlerInterface $loginFormHandler,
        #[Autowire(service: 'prestashop.admin.request_password_reset.form_handler')]
        FormHandlerInterface $requestResetPasswordFormHandler,
    ): Response {
        $loginForm = $loginFormHandler->getForm();
        $requestPasswordResetForm = $requestResetPasswordFormHandler->getForm();
        $requestPasswordResetForm->handleRequest($request);

        if ($requestPasswordResetForm->isSubmitted()) {
            if ($requestPasswordResetForm->isValid()) {
                $infoMessage = $errorMessage = null;
                try {
                    $requestResetPasswordFormHandler->save($requestPasswordResetForm->getData());
                    $infoMessage = $this->trans('Please, check your mailbox.', [], 'Admin.Login.Notification') . '<br/>' .
                        $this->trans('If this email address has been registered in our store, you will receive a link to reset your password.', [], 'Admin.Login.Notification')
                    ;
                } catch (UserNotFoundException) {
                    // If the email doesn't match a known employee we still display a generic success message to avoid any hacker using this
                    // to find out the employee's emails via brute force.
                    $infoMessage = $this->trans('Please, check your mailbox.', [], 'Admin.Login.Notification') . '<br/>' .
                        $this->trans('If this email address has been registered in our store, you will receive a link to reset your password.', [], 'Admin.Login.Notification')
                    ;
                } catch (PasswordResetTemporarilyBlockedException) {
                    $validityDuration = (int) ($this->getConfiguration()->get('PS_PASSWD_TIME_BACK') ?: 360);
                    $errorMessage = $this->trans('You can reset your password every %interval% minute(s) only. Please try again later.', ['%interval%' => $validityDuration], 'Admin.Login.Notification');
                } catch (Throwable) {
                    $errorMessage = $this->trans('An error occurred while attempting to reset your password.', [], 'Admin.Login.Notification');
                }

                if (!empty($infoMessage)) {
                    $this->addFlash('info', $infoMessage);
                } elseif (!empty($errorMessage)) {
                    $this->addFlash('error', $errorMessage);
                }

                return $this->redirectToRoute('admin_login');
            }

            return $this->renderLoginPage($loginForm, $requestPasswordResetForm, true);
        }

        return $this->redirectToRoute('admin_login');
    }

    public function resetPasswordAction(
        #[Autowire(service: 'prestashop.admin.reset_password.form_handler')]
        FormHandlerInterface $resetPasswordFormHandler,
        Request $request,
        string $resetToken
    ): Response {
        $resetPasswordForm = $resetPasswordFormHandler->getForm();
        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isValid()) {
            try {
                $resetPasswordFormHandler->save(array_merge([
                    'resetToken' => $resetToken,
                ], $resetPasswordForm->getData()));
                $this->addFlash('success', $this->trans('The password has been changed successfully.', [], 'Admin.Login.Notification'));
            } catch (InvalidResetPasswordTokenException) {
                // Display generic error message with no details why it failed
                $this->addFlash('error', $this->trans('Your password reset request expired. Please start again.', [], 'Admin.Login.Notification'));

                return $this->redirectToRoute('admin_login');
            } catch (Throwable) {
                $this->addFlash('error', $this->trans('An error occurred while attempting to reset your password.', [], 'Admin.Login.Notification'));
            }

            return $this->redirectToRoute('admin_login');
        }

        return $this->render('@PrestaShop/Admin/Login/reset_password.html.twig', [
            'resetPasswordForm' => $resetPasswordForm->createView(),
            'imgDir' => $this->shopContext->getBaseURI() . 'img/',
            'shopName' => $this->getConfiguration()->get('PS_SHOP_NAME'),
        ]);
    }

    protected function renderLoginPage(FormInterface $loginForm, FormInterface $requestPasswordResetForm, bool $showRequestPasswordResetForm): Response
    {
        return $this->render('@PrestaShop/Admin/Login/login.html.twig', [
            'loginForm' => $loginForm->createView(),
            'requestPasswordResetForm' => $requestPasswordResetForm->createView(),
            'showRequestPasswordResetForm' => $showRequestPasswordResetForm,
            'imgDir' => $this->shopContext->getBaseURI() . 'img/',
            'shopName' => $this->getConfiguration()->get('PS_SHOP_NAME'),
        ]);
    }

    protected function checkRequiredActions(Request $request): ?Response
    {
        $requiredActions = [];
        $warningActions = [];

        // If install folder is still present display a warning to remove it
        if (is_dir($this->projectDir . '/install')) {
            $requiredActions[] = $this->trans('deleted the /install folder', [], 'Admin.Login.Notification');
        }

        // If admin folder is still named admin
        if ($this->adminFolderName === 'admin') {
            $randomName = sprintf(
                'admin%03d%s/',
                mt_rand(0, 999),
                mb_strtolower((new PasswordGenerator(new OpenSSL()))->generatePassword(16)),
            );
            $requiredActions[] = $this->trans('renamed the /admin folder (e.g. %s)', [$randomName], 'Admin.Login.Notification');
        }

        $sslEnabled = (bool) $this->getConfiguration()->get('PS_SSL_ENABLED');
        if ($sslEnabled && !$request->isSecure()) {
            $maintenanceIpConfiguration = $this->getConfiguration()->get('PS_MAINTENANCE_IP');
            if (empty($maintenanceIpConfiguration)) {
                $maintenanceIps = [];
            } else {
                $maintenanceIps = explode(',', $maintenanceIpConfiguration);
            }

            $maintenanceIps = array_merge(['127.0.0.1', '::1'], $maintenanceIps);
            $isMaintainer = false;
            foreach ($request->getClientIps() as $clientIp) {
                if (in_array($clientIp, $maintenanceIps)) {
                    $isMaintainer = true;
                    break;
                }
            }

            if (!$isMaintainer) {
                $securedUrl = str_replace('http://', 'https://', $this->shopContext->getBaseURL());

                $physicalUri = $this->shopContext->getPhysicalUri();
                if (str_ends_with($securedUrl, $physicalUri)) {
                    $securedUrl = substr($securedUrl, 0, -strlen($physicalUri));
                }

                $securedUrl .= $this->generateUrl('admin_login');

                $requiredActions[] = $this->trans(
                    'SSL is activated. Please connect using the following link to [1]log in to secure mode (https://)[/1]',
                    ['[1]' => '<a href="' . $securedUrl . '">', '[/1]' => '</a>'],
                    'Admin.Login.Notification'
                );
            } else {
                $warningActions[] = $this->trans('SSL is activated. However, your IP is allowed to enter unsecure mode for maintenance or local IP issues.', [], 'Admin.Login.Notification');
            }
        }

        if (!empty($requiredActions)) {
            return $this->render('@PrestaShop/Admin/Login/required_actions.html.twig', [
                'requiredActions' => $requiredActions,
                'imgDir' => $this->shopContext->getBaseURI() . 'img/',
                'shopName' => $this->getConfiguration()->get('PS_SHOP_NAME'),
            ]);
        }

        // Warning actions are not blocking but should still be indicated to the user
        foreach ($warningActions as $warningAction) {
            $this->addFlash('warning', $warningAction);
        }

        return null;
    }
}
