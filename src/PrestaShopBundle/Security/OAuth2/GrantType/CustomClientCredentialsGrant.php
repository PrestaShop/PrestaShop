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

namespace PrestaShopBundle\Security\OAuth2\GrantType;

use DateInterval;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use PrestaShopBundle\Security\OAuth2\Entity\Client;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The default class does not allow to modify the lifetime of a token.
 * This class allow to set a different lifetime for each token.
 */
class CustomClientCredentialsGrant extends ClientCredentialsGrant
{
    /**
     * @return AccessTokenEntityInterface
     */
    protected function issueAccessToken(
        DateInterval $accessTokenTTL,
        ClientEntityInterface $client,
        $userIdentifier,
        array $scopes = []
    ) {
        /** @var Client $client */
        if ($client->getLifetime() !== null) {
            $accessTokenTTL = DateInterval::createFromDateString($client->getLifetime() . ' seconds');
        }

        return parent::issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }

    /**
     * By default, League client credentials implementation only accepts scopes to be passed via the `scope` parameters
     * and multiple scopes must be separated with spaces. We want to offer more possibilities:
     *   - scope: array of string, strings separated by commas or strings separated by spaces
     *   - scopes: array of string, strings separated by commas or strings separated by spaces
     *
     * When both parameters are provided however, we throw an exception as we cannot tell which one should be prioritized.
     *
     * @param string $parameter
     * @param ServerRequestInterface $request
     * @param string|null $default
     *
     * @return string|null
     *
     * @throws OAuthServerException
     */
    protected function getRequestParameter($parameter, ServerRequestInterface $request, $default = null)
    {
        // Any other parameter is not modified
        if ($parameter !== 'scope') {
            return parent::getRequestParameter($parameter, $request, $default);
        }

        // Scopes are handled separately
        $parsedBody = (array) $request->getParsedBody();
        if (isset($parsedBody['scope'], $parsedBody['scopes'])) {
            throw OAuthServerException::invalidRequest($parameter, 'Use either scope or scopes parameter, not both.');
        }

        if (isset($parsedBody['scope'])) {
            return $this->convertScopeParameterIntoLeagueExpectedScope($parsedBody['scope']);
        }
        if (isset($parsedBody['scopes'])) {
            return $this->convertScopeParameterIntoLeagueExpectedScope($parsedBody['scopes']);
        }

        return null;
    }

    private function convertScopeParameterIntoLeagueExpectedScope(array|string|null $scope): ?string
    {
        if (empty($scope)) {
            return null;
        }

        $scopes = [];
        if (is_string($scope)) {
            // Prioritize separation by comma, else use space
            if (str_contains($scope, ',')) {
                $scopes = explode(',', $scope);
            } else {
                $scopes = explode(' ', $scope);
            }
            $scopes = array_map(fn (string $scope) => trim($scope), $scopes);
        } elseif (is_array($scope)) {
            $scopes = array_map(fn (string $scope) => trim($scope), $scope);
        }

        return implode(' ', $scopes);
    }
}
