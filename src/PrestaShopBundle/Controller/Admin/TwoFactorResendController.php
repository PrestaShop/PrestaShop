<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmailOtpPreparation;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShopBundle\Entity\Employee\Employee;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TwoFactorResendController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Security $security,
        private readonly EmployeeEmailOtpManager $otpManager,
    ) {
    }

    public function resend(Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('2fa_resend', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->security->getUser();
        if (!$user instanceof Employee) {
            throw $this->createAccessDeniedException();
        }
        try {
            $outcome = $this->otpManager->prepare($user, EmployeeEmailOtp::LOGIN, true);
        } catch (EmployeeTwoFactorException $e) {
            if (EmployeeTwoFactorException::DISABLED !== $e->getCode()) {
                throw $e;
            }
            throw $this->createAccessDeniedException();
        }
        $message = match ($outcome->status) {
            EmailOtpPreparation::SENT => $this->trans('Code resent. Please check your spam folder as well.', [], 'Admin.Login.Notification'),
            EmailOtpPreparation::THROTTLED => $this->trans('Too many requests for an authentication code. Please try again later.', [], 'Admin.Login.Notification'),
            EmailOtpPreparation::FAILED => $this->trans('The authentication email could not be sent. Please try again later.', [], 'Admin.Login.Notification'),
            default => $this->trans('Your authentication code changed. Please request another code.', [], 'Admin.Login.Notification'),
        };
        $this->addFlash(EmailOtpPreparation::SENT === $outcome->status ? 'success' : 'error', $message);

        return $this->redirectToRoute('2fa_login', ['preferProvider' => 'email']);
    }
}
