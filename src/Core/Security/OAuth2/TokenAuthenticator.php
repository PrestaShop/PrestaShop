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

use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * This class is responsible for authenticating api calls using the Authorization header
 *
 * @experimental
 */
class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly AuthorisationServerInterface $authorizationServer,
        private readonly HttpMessageFactoryInterface $httpMessageFactory,
    ) {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return $this->returnWWWAuthenticateResponse();
    }

    public function supports(Request $request): bool
    {
        // Every request to the API should be handled by this Authenticator
        // A filter is already present for requests linked to the API in security.yml
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->returnWWWAuthenticateResponse($exception->getMessage());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // No response returned here, the request should keep running
        return null;
    }

    private function returnWWWAuthenticateResponse(?string $content = null): Response
    {
        return new Response($content, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);
    }

    public function authenticate(Request $request): Passport
    {
        $authorization = $request->headers->get('Authorization') ?? null;
        if (null === $authorization) {
            throw new CustomUserMessageAuthenticationException('No Authorization header provided');
        }
        if (!str_starts_with($authorization, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('Bearer token missing');
        }

        $credentials = $this->httpMessageFactory->createRequest($request);
        $userIdentifier = $this->authorizationServer->getUser($credentials);

        if (null === $userIdentifier) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        return new SelfValidatingPassport(new UserBadge($userIdentifier->getUserIdentifier()));
    }
}
