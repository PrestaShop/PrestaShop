<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use PrestaShopBundle\Security\OAuth2\Entity\AccessToken;

/**
 * Repository class responsible for managing PrestaShop's Authorization Server AccessToken entity
 *
 * @experimental
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        $token = new AccessToken();
        $token->setClient($clientEntity);
        if (!empty($userIdentifier)) {
            $token->setUserIdentifier($userIdentifier);
        }
        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }

        return $token;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
    }

    public function revokeAccessToken($tokenId): void
    {
        // @ToDo: revoke AccessToken
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        // @ToDo: check if AccessToken is revoked
        return false;
    }
}
