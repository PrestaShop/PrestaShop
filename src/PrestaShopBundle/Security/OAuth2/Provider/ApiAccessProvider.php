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

use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiAccessProvider implements UserProviderInterface
{
    public function __construct(
        private readonly ApiClientRepository $apiClientRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): ApiClient
    {
        $apiClient = $this->apiClientRepository->findOneBy(['clientId' => $identifier]);

        if (!$apiClient instanceof ApiClient) {
            throw new UserNotFoundException('Api Client not found');
        }

        return $apiClient;
    }

    public function refreshUser(UserInterface $apiClient): ApiClient
    {
        if (!$apiClient instanceof ApiClient) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $apiClient::class));
        }

        return $this->loadUserByIdentifier($apiClient->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === ApiClient::class;
    }

    public function loadUserByUsername(string $username): ApiClient
    {
        return $this->loadUserByIdentifier($username);
    }
}
