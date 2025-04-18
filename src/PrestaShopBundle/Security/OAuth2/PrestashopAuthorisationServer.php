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

namespace PrestaShopBundle\Security\OAuth2;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer as LeagueResourceServer;
use PrestaShop\PrestaShop\Core\Security\OAuth2\AuthorisationServerInterface;
use PrestaShop\PrestaShop\Core\Security\OAuth2\JwtTokenUser;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class responsible for validating a PrestaShop token (issued by our AccessTokenController)
 * the implementation is based on League OAuth2 library just like in the controller.
 */
class PrestashopAuthorisationServer implements AuthorisationServerInterface
{
    public function __construct(
        private readonly LeagueResourceServer $resourceServer,
        private readonly HttpMessageFactoryInterface $httpMessageFactory,
    ) {
    }

    public function isTokenValid(Request $request): bool
    {
        // Manually check that the authorization header is as expected (league resource server is a bit more lax about the
        // Bearer keyword absence)
        $authorization = $request->headers->get('Authorization') ?? null;
        if (null === $authorization) {
            return false;
        }
        if (!str_starts_with($authorization, 'Bearer ')) {
            return false;
        }

        try {
            $serverRequest = $this->httpMessageFactory->createRequest($request);
            $this->resourceServer->validateAuthenticatedRequest($serverRequest);
        } catch (OAuthServerException) {
            return false;
        }

        return true;
    }

    public function getJwtTokenUser(Request $request): ?JwtTokenUser
    {
        try {
            $serverRequest = $this->httpMessageFactory->createRequest($request);
            $validatedRequest = $this->resourceServer->validateAuthenticatedRequest($serverRequest);
        } catch (OAuthServerException) {
            return null;
        }

        return new JwtTokenUser(
            $validatedRequest->getAttribute('oauth_client_id'),
            $validatedRequest->getAttribute('oauth_scopes') ?? []
        );
    }
}
