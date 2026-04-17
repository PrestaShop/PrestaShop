<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Security\OAuth2\Entity\ScopeEntity;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Repository class responsible for managing PrestaShop's Authorization Server scopes,
 * based on our scopes extractor that extract scopes from the ApiPlatform resources in
 * which scopes are defined. The ApiPlatform resources can come from modules so the list
 * is dynamic based on which modules are installed.
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    /** @var ApiResourceScopes[] */
    private array $apiResourceScopes;

    public function __construct(
        private readonly ApiResourceScopesExtractorInterface $scopesExtractor,
        private readonly UserProviderInterface $apiClientProvider,
    ) {
        $this->apiResourceScopes = $this->scopesExtractor->getEnabledApiResourceScopes();
    }

    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        foreach ($this->apiResourceScopes as $apiResourceScope) {
            if (in_array($identifier, $apiResourceScope->getScopes())) {
                return new ScopeEntity($identifier);
            }
        }

        return null;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null,
        $authCodeId = null
    ) {
        $finalizedScopes = [
            new ScopeEntity('is_authenticated'),
        ];

        /** @var ApiClient $apiClient */
        $apiClient = $this->apiClientProvider->loadUserByIdentifier($clientEntity->getIdentifier());
        foreach ($scopes as $scope) {
            if (!in_array($scope->getIdentifier(), $apiClient->getScopes())) {
                $hint = \sprintf(
                    'Usage of scope `%s` is not allowed for this client',
                    \htmlspecialchars($scope->getIdentifier(), ENT_QUOTES, 'UTF-8', false)
                );
                throw OAuthServerException::accessDenied($hint);
            }
            $finalizedScopes[] = $scope;
        }

        return $finalizedScopes;
    }
}
