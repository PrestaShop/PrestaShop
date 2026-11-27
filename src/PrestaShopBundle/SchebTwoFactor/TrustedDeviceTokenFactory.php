<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenFactoryInterface;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Keep the version of the original authentication across user refreshes.
 */
final class TrustedDeviceTokenFactory implements TwoFactorTokenFactoryInterface
{
    public const AUTHENTICATION_ATTRIBUTE = 'prestashop.trusted_device_authentication';

    public function __construct(private readonly TwoFactorTokenFactoryInterface $inner)
    {
    }

    /**
     * @param string[] $twoFactorProviders
     */
    public function create(TokenInterface $authenticatedToken, string $firewallName, array $twoFactorProviders): TwoFactorTokenInterface
    {
        $user = $authenticatedToken->getUser();
        if ($user instanceof Employee && !$authenticatedToken->hasAttribute(self::AUTHENTICATION_ATTRIBUTE)) {
            // Scheb serializes this underlying token and returns it when 2FA completes.
            $authenticatedToken->setAttribute(self::AUTHENTICATION_ATTRIBUTE, [
                'employeeId' => $user->getId(),
                'identifier' => $user->getUserIdentifier(),
                'firewall' => $firewallName,
                'version' => $user->getTrustedTokenVersion(),
            ]);
        }

        return $this->inner->create($authenticatedToken, $firewallName, $twoFactorProviders);
    }
}
