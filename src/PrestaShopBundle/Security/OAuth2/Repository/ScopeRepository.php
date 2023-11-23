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

namespace PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use PrestaShopBundle\Security\OAuth2\Entity\ScopeEntity;

/**
 * Repository class responsible for managing PrestaShop's Authorization Server scopes
 * Empty implementation for now because scopes are not used yet
 *
 * @experimental
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    /** @var ApiResourceScopes[] */
    private array $apiResourceScopes;

    public function __construct(private readonly ApiResourceScopesExtractorInterface $scopesExtractor)
    {
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

    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        $finalizedScopes = [
            new ScopeEntity('is_authenticated'),
        ];

        foreach ($scopes as $scope) {
            $scopeEntity = $this->getScopeEntityByIdentifier($scope->getIdentifier());
            if ($scopeEntity instanceof ScopeEntityInterface) {
                $finalizedScopes[] = $scopeEntity;
            }
        }

        return $finalizedScopes;
    }
}
