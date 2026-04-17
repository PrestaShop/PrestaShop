<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use LogicException;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Security\OAuth2\Entity\Client;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Repository class responsible for managing PrestaShop's Authorization Server Client entity
 *
 * @experimental
 */
class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private readonly UserProviderInterface $userProvider,
        private readonly UserPasswordHasherInterface $passwordEncoder
    ) {
    }

    public function getClientEntity($clientIdentifier): ?Client
    {
        $user = $this->getUser($clientIdentifier);

        if ($user === null) {
            return null;
        }

        $client = new Client();
        $client->setIdentifier($user->getUserIdentifier());
        if ($user instanceof ApiClient) {
            $client->setLifetime($user->getLifetime());
        }

        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        if ($grantType !== 'client_credentials' || $clientSecret === null) {
            return false;
        }

        $client = $this->getUser($clientIdentifier);

        if ($client === null) {
            return false;
        }

        if (!$client instanceof PasswordAuthenticatedUserInterface) {
            throw new LogicException(sprintf('The class %s should implement %s.', $client::class, PasswordAuthenticatedUserInterface::class));
        }

        return $this->passwordEncoder->isPasswordValid($client, $clientSecret);
    }

    private function getUser($clientIdentifier): ?UserInterface
    {
        try {
            return $this->userProvider->loadUserByIdentifier($clientIdentifier);
        } catch (UserNotFoundException) {
            return null;
        }
    }
}
