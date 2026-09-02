<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Provider;

use Doctrine\ORM\NoResultException;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiClientProvider implements UserProviderInterface
{
    public function __construct(
        private readonly ApiClientRepository $apiClientRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): ApiClient
    {
        try {
            // We only load internal API clients so no external issuer to specify
            $apiClient = $this->apiClientRepository->getByClientId($identifier);
        } catch (NoResultException) {
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
}
