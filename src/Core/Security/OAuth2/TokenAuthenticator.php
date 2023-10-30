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

namespace PrestaShop\PrestaShop\Core\Security\OAuth2;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * This class is responsible for authenticating api calls using the Authorization header
 *
 * @experimental
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var AuthorisationServerInterface
     */
    private $authorizationServer;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    public function __construct(AuthorisationServerInterface $authorizationServer, HttpMessageFactoryInterface $httpMessageFactory)
    {
        $this->authorizationServer = $authorizationServer;
        $this->httpMessageFactory = $httpMessageFactory;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return $this->returnWWWAuthenticateResponse();
    }

    public function supports(Request $request): bool
    {
        try {
            $authorization = $request->headers->get('Authorization') ?? null;
            if (null === $authorization) {
                return false;
            }
            $explode = explode(' ', $authorization);
            if (count($explode) >= 2) {
                $token = $explode[1];
                (new Parser(new JoseEncoder()))->parse($token);
            }
        } catch (InvalidTokenStructure $e) {
            return false;
        }

        // Every request to the API should be handled by this Authenticator
        return true;
    }

    public function getCredentials(Request $request): ServerRequestInterface
    {
        return $this->httpMessageFactory->createRequest($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $this->authorizationServer->getUser($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->authorizationServer->isTokenValid($credentials);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->returnWWWAuthenticateResponse();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // No response returned here, the request should keep running
        return null;
    }

    public function supportsRememberMe(): bool
    {
        // Stateless API, remember me feature doesn't apply here
        return false;
    }

    private function returnWWWAuthenticateResponse(): Response
    {
        return new Response(null, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);
    }
}
