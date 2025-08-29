<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
