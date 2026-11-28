<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Delegate cookie validation and signing to Scheb, without granting a newer
 * trusted version to an authentication that started before a revocation.
 */
final class EmployeeTrustedDeviceManager implements TrustedDeviceManagerInterface
{
    public function __construct(
        private readonly TrustedDeviceManagerInterface $inner,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    public function isTrustedDevice(object $user, string $firewallName): bool
    {
        return $user instanceof Employee
            && $this->hasCurrentVersion($user)
            && $this->inner->isTrustedDevice($user, $firewallName);
    }

    public function canSetTrustedDevice(object $user, Request $request, string $firewallName): bool
    {
        return $user instanceof Employee
            && $this->matchesOriginalAuthentication($user, $firewallName)
            && $this->hasCurrentVersion($user)
            && $this->inner->canSetTrustedDevice($user, $request, $firewallName);
    }

    public function addTrustedDevice(object $user, string $firewallName): void
    {
        if (!$user instanceof Employee
            || $this->tokenStorage->getToken() instanceof TwoFactorTokenInterface
            || !$this->matchesOriginalAuthentication($user, $firewallName)
            || !$this->hasCurrentVersion($user)
        ) {
            return;
        }

        // The native manager reads this entity's scalar getter, without refreshing it.
        // A reset after this last check can therefore only leave an obsolete cookie.
        $this->inner->addTrustedDevice($user, $firewallName);
    }

    private function matchesOriginalAuthentication(Employee $user, string $firewallName): bool
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TwoFactorTokenInterface) {
            $token = $token->getAuthenticatedToken();
        }
        if (null === $token || !$token->hasAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)) {
            return false;
        }

        $snapshot = $token->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE);

        return is_array($snapshot)
            && ($snapshot['employeeId'] ?? null) === $user->getId()
            && ($snapshot['identifier'] ?? null) === $user->getUserIdentifier()
            && ($snapshot['firewall'] ?? null) === $firewallName
            && ($snapshot['version'] ?? null) === $user->getTrustedTokenVersion();
    }

    private function hasCurrentVersion(Employee $user): bool
    {
        try {
            return $this->employeeRepository->getTrustedDeviceVersion($user->getId()) === $user->getTrustedTokenVersion();
        } catch (EmployeeNotFoundException) {
            return false;
        }
    }
}
