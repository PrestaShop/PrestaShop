<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class EmployeeEmailCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly EmployeeEmailOtpManager $otpManager,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function generateAndSend(TwoFactorInterface $user): void
    {
        if ($user instanceof Employee) {
            $outcome = $this->otpManager->prepare($user, EmployeeEmailOtp::LOGIN);
            $request = $this->requestStack->getCurrentRequest();
            if (null !== $request && $request->hasSession()) {
                // Carry only presentation metadata across the initial login redirect.
                $request->getSession()->set('_employee_email_otp_preparation', [
                    'status' => $outcome->status,
                    'retry_at' => $outcome->retryAt,
                ]);
            }
        }
    }
}
