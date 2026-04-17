<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * This service centralizes the generation and validation of the admin query token to avoid duplicating code and rely
 * on a single homogeneous implementation in the whole back office. It can generate symfony CSRF tokens (not legacy
 * ones so far because not needed but it could) and validate both legacy and CSRF tokens.
 */
class UserTokenManager implements CacheClearerInterface
{
    /**
     * @var array
     */
    private $tokens = [];

    public function __construct(
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly SessionEmployeeProvider $sessionEmployeeProvider,
    ) {
    }

    public function getSymfonyToken(): string
    {
        // When user is logged we can get it from Security service
        if ($this->security->getUser()) {
            $userIdentifier = $this->security->getUser()->getUserIdentifier();
        } else {
            // When user is not initialized yet (like in LegacyRouterChecker) we fetch the employee based on saved session data
            $userIdentifier = $this->sessionEmployeeProvider->getEmployeeFromSession()?->getUserIdentifier();
        }

        if (empty($userIdentifier)) {
            return '';
        }

        // Do not generate token each time we need one, one token per request is enough and can be used for all generated URLs
        if (!isset($this->tokens[$userIdentifier])) {
            $this->tokens[$userIdentifier] = $this->tokenManager->getToken($userIdentifier)->getValue();
        }

        return $this->tokens[$userIdentifier];
    }

    public function isTokenValid(): bool
    {
        $request = $this->requestStack->getMainRequest();
        // Check usual CSRF _token value for migrated pages
        if ($request->query->has('_token') && $this->isCsrfTokenValid($request->query->get('_token'))) {
            return true;
        }

        // Legacy urls use token instead of _token as the URL parameter, so it's a valid alternative
        // Token can be posted via GET or POST parameters
        $legacyRequestToken = $request->get('token');
        if (!empty($legacyRequestToken) && $this->isCsrfTokenValid($legacyRequestToken)) {
            return true;
        }

        return false;
    }

    public function clear()
    {
        $this->tokens = [];
    }

    private function isCsrfTokenValid(string $tokenValue): bool
    {
        if (!$this->security->getUser()) {
            return false;
        }

        $token = new CsrfToken($this->security->getUser()->getUserIdentifier(), $tokenValue);

        return $this->tokenManager->isTokenValid($token);
    }
}
