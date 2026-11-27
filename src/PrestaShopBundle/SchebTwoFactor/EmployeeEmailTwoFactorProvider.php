<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;

final class EmployeeEmailTwoFactorProvider implements TwoFactorProviderInterface
{
    public function __construct(
        private readonly TwoFactorProviderInterface $inner,
        private readonly EmployeeEmailOtpManager $otpManager,
        private readonly TwoFactorFormRendererInterface $formRenderer,
    ) {
    }

    public function beginAuthentication(AuthenticationContextInterface $context): bool
    {
        return $this->inner->beginAuthentication($context);
    }

    public function prepareAuthentication(object $user): void
    {
        try {
            $this->inner->prepareAuthentication($user);
        } catch (EmployeeTwoFactorException $e) {
            // A reset while authentication is pending must not turn kernel.response into a 500.
            if (EmployeeTwoFactorException::DISABLED !== $e->getCode()) {
                throw $e;
            }
        }
    }

    public function validateAuthenticationCode(object $user, string $authenticationCode): bool
    {
        return $user instanceof Employee && $this->otpManager->consumeLogin($user->getId(), $authenticationCode);
    }

    public function getFormRenderer(): TwoFactorFormRendererInterface
    {
        return $this->formRenderer;
    }
}
