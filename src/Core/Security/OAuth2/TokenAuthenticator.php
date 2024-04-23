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

use Doctrine\ORM\NoResultException;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use PrestaShopBundle\Security\OAuth2\PrestashopAuthorisationServer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class is responsible for authenticating api calls using the Authorization header
 *
 * @experimental
 */
class TokenAuthenticator extends AbstractAuthenticator
{
    /**
     * @param iterable|AuthorisationServerInterface[] $authorizationServers
     */
    public function __construct(
        private readonly iterable $authorizationServers,
        private readonly TranslatorInterface $translator,
        private readonly ApiClientRepository $apiClientRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(Request $request): bool
    {
        // Every request to the API should be handled by this Authenticator
        // A filter is already present for requests linked to the API in security.yml
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->returnWWWAuthenticateResponse($this->translator->trans($exception->getMessageKey(), $exception->getMessageData()));
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
        $authorizationServer = $this->getAuthorizationServer($request);

        // No authorization server found, return a hint in the exception to help debug a little
        if (null === $authorizationServer) {
            // These two hints are probably not adapted for all the possible authorization servers, but probably most
            // of them, and at the very least they are true for the internal PrestashopAuthorizationServer
            $authorization = $request->headers->get('Authorization') ?? null;
            if (null === $authorization) {
                $this->logger->error('TokenAuthenticator: No Authorization header provided');
                throw new CustomUserMessageAuthenticationException(json_encode('No Authorization header provided'));
            }
            if (!str_starts_with($authorization, 'Bearer ')) {
                $this->logger->error('TokenAuthenticator: Bearer token missing');
                throw new CustomUserMessageAuthenticationException(json_encode('Bearer token missing'));
            }

            // Default hint, simply could not find a matching authorization server
            $this->logger->error('TokenAuthenticator: No authorization server matching your credentials');
            throw new CustomUserMessageAuthenticationException(json_encode('No authorization server matching your credentials'));
        }

        $jwtTokenUser = $authorizationServer->getJwtTokenUser($request);
        if (null === $jwtTokenUser) {
            $this->logger->error('TokenAuthenticator: Invalid credentials');
            throw new CustomUserMessageAuthenticationException(json_encode('Invalid credentials'));
        }

        // Specific check for external authorization server (PrestashopAuthorisationServer is the only internal implementation)
        if (!$authorizationServer instanceof PrestashopAuthorisationServer) {
            if (empty($jwtTokenUser->getExternalIssuer())) {
                $this->logger->error('TokenAuthenticator: No external issuer specified');
                throw new CustomUserMessageAuthenticationException(json_encode('No external issuer specified'));
            }
            $this->autoSaveApiClient($jwtTokenUser);
        }

        // Returns passport purely based on JWT token here, we set a specific loader that returns the
        // user object directly since it was already resolved by our authorization server
        return new SelfValidatingPassport(new UserBadge($jwtTokenUser->getUserIdentifier(), fn () => $jwtTokenUser));
    }

    /**
     * All clients are saved in DB, this allows keeping track of connections and it is used by the
     * ApiClientContext to initialize correctly, thus we can use this Context service even for clients
     * from external authorization servers.
     *
     * @param JwtTokenUser $jwtTokenUser
     */
    private function autoSaveApiClient(JwtTokenUser $jwtTokenUser): void
    {
        try {
            $this->apiClientRepository->getByClientId($jwtTokenUser->getUserIdentifier(), $jwtTokenUser->getExternalIssuer());
        } catch (NoResultException) {
            $apiClient = new ApiClient();
            $apiClient
                ->setClientId($jwtTokenUser->getUserIdentifier())
                ->setClientName($jwtTokenUser->getUserIdentifier())
                ->setExternalIssuer($jwtTokenUser->getExternalIssuer())
                ->setLifetime(3600)
                ->setEnabled(true)
            ;
            $this->apiClientRepository->save($apiClient);
        }
    }

    private function getAuthorizationServer(Request $request): ?AuthorisationServerInterface
    {
        foreach ($this->authorizationServers as $authorizationServer) {
            $isTokenValid = $authorizationServer->isTokenValid($request);
            $this->logger->debug('TokenAuthenticator check token via ' . get_class($authorizationServer) . ' => ' . ($isTokenValid ? 'valid token' : 'invalid token'));
            if ($isTokenValid) {
                return $authorizationServer;
            }
        }

        return null;
    }
}
