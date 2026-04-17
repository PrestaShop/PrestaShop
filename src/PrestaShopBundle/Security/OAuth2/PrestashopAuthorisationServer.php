<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
