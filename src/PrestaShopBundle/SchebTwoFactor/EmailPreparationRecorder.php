<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\PreparationRecorderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class EmailPreparationRecorder implements PreparationRecorderInterface
{
    public function __construct(
        private readonly PreparationRecorderInterface $inner,
        private readonly RequestStack $requestStack,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function isTwoFactorProviderPrepared(string $firewallName, string $providerName): bool
    {
        // When multiple 2FA methods are available, avoid preparing the email provider
        // until the employee explicitly selects it from the login form.
        if ($this->shouldSkipEmailPreparation($providerName)) {
            return true;
        }

        return $this->inner->isTwoFactorProviderPrepared($firewallName, $providerName);
    }

    public function setTwoFactorProviderPrepared(string $firewallName, string $providerName): void
    {
        $this->inner->setTwoFactorProviderPrepared($firewallName, $providerName);
    }

    private function shouldSkipEmailPreparation(string $providerName): bool
    {
        if ('email' !== $providerName) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || '2fa_login' !== $request->attributes->get('_route')) {
            return false;
        }

        if ($request->query->has('preferProvider')) {
            return false;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token instanceof TwoFactorTokenInterface) {
            return false;
        }

        // A single email provider should keep the default bundle behavior and send the code immediately.
        return count($token->getTwoFactorProviders()) > 1;
    }
}
