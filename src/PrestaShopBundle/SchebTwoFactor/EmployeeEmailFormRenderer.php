<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmailOtpPreparation;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\PreparationRecorderInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class EmployeeEmailFormRenderer implements TwoFactorFormRendererInterface
{
    public function __construct(
        private readonly TwoFactorFormRendererInterface $inner,
        private readonly EmployeeEmailOtpManager $otpManager,
        private readonly PreparationRecorderInterface $preparationRecorder,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function renderForm(Request $request, array $templateVars): Response
    {
        if ($request->hasSession()) {
            $previous = $request->getSession()->remove('_employee_email_otp_preparation');
            if (is_array($previous)) {
                $templateVars['emailPreparationStatus'] = $previous['status'];
                $templateVars['emailRetryAt'] = $previous['retry_at'];
            }
        }
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TwoFactorTokenInterface && $token->getUser() instanceof Employee) {
            $firewall = $token->getFirewallName();
            // Prepare before rendering, instead of letting kernel.response discover a delivery limit too late.
            if (!$this->preparationRecorder->isTwoFactorProviderPrepared($firewall, 'email')) {
                try {
                    $outcome = $this->otpManager->prepare($token->getUser(), EmployeeEmailOtp::LOGIN);
                    $templateVars['emailPreparationStatus'] = $outcome->status;
                    $templateVars['emailRetryAt'] = $outcome->retryAt;
                } catch (EmployeeTwoFactorException $e) {
                    if (EmployeeTwoFactorException::DISABLED !== $e->getCode()) {
                        throw $e;
                    }
                    $templateVars['emailPreparationStatus'] = EmailOtpPreparation::CHANGED;
                }
                $this->preparationRecorder->setTwoFactorProviderPrepared($firewall, 'email');
            }
            $templateVars['emailChallengeAvailable'] = $this->otpManager->hasValidChallenge($token->getUser()->getId(), EmployeeEmailOtp::LOGIN);
        }

        return $this->inner->renderForm($request, $templateVars);
    }
}
