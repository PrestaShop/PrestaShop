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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Old convenient way to access User, if exists. Prefer using the Security service to get the connected user.
 */
class UserProvider
{
    public const ANONYMOUS_USER = 'ANONYMOUS_USER';

    public function __construct(
        private readonly Security $security,
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

        return null;
    }

    public function getUsername(): string
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return self::ANONYMOUS_USER;
    }

    public function logout(): void
    {
        $this->security->logout(false);
    }
}
