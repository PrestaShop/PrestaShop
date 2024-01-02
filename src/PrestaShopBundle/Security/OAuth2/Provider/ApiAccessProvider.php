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

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Provider;

use PrestaShopBundle\Entity\ApiAccess;
use PrestaShopBundle\Entity\Repository\ApiAccessRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiAccessProvider implements UserProviderInterface
{
    public function __construct(
        private readonly ApiAccessRepository $apiAccessRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): ApiAccess
    {
        $apiAccess = $this->apiAccessRepository->findOneBy(['clientId' => $identifier]);

        if (!$apiAccess instanceof ApiAccess) {
            throw new UserNotFoundException('Api Access not found');
        }

        return $apiAccess;
    }

    public function refreshUser(UserInterface $apiAccess): ApiAccess
    {
        if (!$apiAccess instanceof ApiAccess) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $apiAccess::class));
        }

        return $this->loadUserByIdentifier($apiAccess->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === ApiAccess::class;
    }

    public function loadUserByUsername(string $username): ApiAccess
    {
        return $this->loadUserByIdentifier($username);
    }
}
