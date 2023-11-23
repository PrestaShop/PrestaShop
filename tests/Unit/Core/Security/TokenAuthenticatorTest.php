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

namespace Tests\Unit\Core\Security;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\OAuth2\AuthorisationServerInterface;
use PrestaShop\PrestaShop\Core\Security\OAuth2\TokenAuthenticator;
use PrestaShopBundle\Security\OAuth2\Entity\JwtTokenUser;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticatorTest extends TestCase
{
    protected $tokenAuthenticator;
    protected $authorizationServer;
    protected $request;

    public function setUp(): void
    {
        $psr7 = new Psr17Factory();
        $this->authorizationServer = $this->createMock(AuthorisationServerInterface::class);
        $this->tokenAuthenticator = new TokenAuthenticator(
            $this->authorizationServer,
            new PsrHttpFactory($psr7, $psr7, $psr7, $psr7)
        );
        $this->request = Request::create('/');
        parent::setUp();
    }

    public function testStart(): void
    {
        $response = $this->tokenAuthenticator->start($this->request, new AuthenticationException());
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('WWW-Authenticate'));
        $this->assertSame('Bearer', $response->headers->get('WWW-Authenticate'));
    }

    public function testOnAuthenticationFailure(): void
    {
        $response = $this->tokenAuthenticator->onAuthenticationFailure($this->request, new AuthenticationException());
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('WWW-Authenticate'));
        $this->assertSame('Bearer', $response->headers->get('WWW-Authenticate'));
    }

    public function testGetCredentials(): void
    {
        $credentials = $this->tokenAuthenticator->getCredentials($this->request);
        $this->assertTrue($credentials instanceof ServerRequestInterface);
    }

    public function testGetUser(): void
    {
        $this->authorizationServer->method('getUser')->willReturn(new JwtTokenUser('testUser', []));
        $serverRequestMock = $this->createMock(ServerRequestInterface::class);
        $user = $this->tokenAuthenticator->getUser(
            $serverRequestMock,
            $this->createMock(UserProviderInterface::class)
        );
        $this->assertInstanceOf(JwtTokenUser::class, $user);
    }

    public function testCheckCredentials(): void
    {
        $this->authorizationServer->expects($this->once())->method('isTokenValid');
        $this->tokenAuthenticator->checkCredentials(
            $this->createMock(ServerRequestInterface::class),
            $this->createMock(UserInterface::class)
        );
    }
}
