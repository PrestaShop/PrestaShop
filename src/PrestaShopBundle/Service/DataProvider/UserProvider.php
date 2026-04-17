<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\DataProvider;

use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Security\Admin\SessionEmployeeProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Old convenient way to access User, if exists. Prefer using the Security service to get the connected user.
 * This service is only used in legacy context now.
 */
class UserProvider
{
    public function __construct(
        private readonly Security $security,
        private readonly SessionEmployeeProvider $sessionEmployeeProvider,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @see \Symfony\Bundle\FrameworkBundle\Controller::getUser()
     */
    public function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();
        if ($user instanceof Employee) {
            return $user;
        }

        // Since this service is used in legacy context it may be called early in the process when the FirewallListener has not been
        // executed yet, therefore the Security::getUser still returns null, so we use this fallback to unserialize an Employee
        // entity from the session token for backward compatibility
        if ($this->requestStack->getCurrentRequest()) {
            $sessionEmployee = $this->sessionEmployeeProvider->getEmployeeFromSession($this->requestStack->getCurrentRequest());
            if ($sessionEmployee instanceof Employee) {
                return $sessionEmployee;
            }
        }

        return null;
    }

    public function logout(): void
    {
        $this->security->logout(false);
    }
}
