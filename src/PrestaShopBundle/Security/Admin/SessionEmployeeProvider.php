<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

use PrestaShopBundle\Utils\SafeUnserializeTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This service is able to get the logged in employee info from the Symfony sessions,
 * it is exactly doing the same thing as the internal Symfony ContextListener but "manually"
 *
 * This is useful for listeners that are executed before the ContextListener, so they
 * can init some contexts based on employee data for example.
 *
 * This should not be used in any other context, when you need to get the logged user you
 * should rely on the Symfony\Bundle\SecurityBundle\Security service instead.
 *
 * @internal
 */
class SessionEmployeeProvider
{
    use SafeUnserializeTrait;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $sessionKey = '_security_main',
    ) {
    }

    /**
     * Most of this code is inspired from the Symfony ContextListener, it's just that we need
     * to get the employee before the firewall listener in order to preset the PrestaShop contexts.
     */
    public function getEmployeeFromSession(?Request $request = null): ?SessionEmployeeInterface
    {
        $request = $request ?? $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }
        $session = $request->hasPreviousSession() ? $request->getSession() : null;
        if (null !== $session) {
            $token = $session->get($this->sessionKey);
            if (null !== $token) {
                $token = $this->safelyUnserialize($token);
                if ($token instanceof TokenInterface && $token->getUser() instanceof SessionEmployeeInterface) {
                    return $token->getUser();
                }
            }
        }

        return null;
    }
}
