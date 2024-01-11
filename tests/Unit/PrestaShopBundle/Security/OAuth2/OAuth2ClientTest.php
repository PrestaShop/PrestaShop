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

namespace Tests\Unit\PrestaShopBundle\Security\OAuth2;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer as LeagueResourceServer;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Security\OAuth2\PrestashopAuthorisationServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuth2ClientTest extends TestCase
{
    private $leagueResourceServer;
    private $client;

    public function setUp(): void
    {
        $this->leagueResourceServer = $this->createMock(LeagueResourceServer::class);
        $this->client = new PrestashopAuthorisationServer($this->leagueResourceServer);
        parent::setUp();
    }

    /**
     * Only testing that the client_id exists here (password doesn't matter)
     *
     * @dataProvider getUserDataProvider
     */
    public function testGetUser(ServerRequestInterface $request, bool $exists): void
    {
        if ($exists) {
            $this->leagueResourceServer->method('validateAuthenticatedRequest')->willReturnCallback(function (ServerRequestInterface $request) {
                return $request->withAttribute('oauth_client_id', $request->getParsedBody()['client_id'] ?? null);
            });
            $user = $this->client->getUser($request);

            $this->assertTrue($user instanceof UserInterface);
        } else {
            $this
                ->leagueResourceServer
                ->method('validateAuthenticatedRequest')
                ->willThrowException(new OAuthServerException('Client authentication failed', 4, 'invalid_client', 401))
            ;
            $user = $this->client->getUser($request);
            $this->assertNull($user);
        }
    }

    /**
     * only testing that validateAuthenticatedRequest is called here
     *
     * @dataProvider getUserDataProvider
     */
    public function testIsTokenValid(ServerRequestInterface $request): void
    {
        $this->leagueResourceServer->expects($this->once())->method('validateAuthenticatedRequest');
        $this->client->isTokenValid($request);
    }

    public function getUserDataProvider(): iterable
    {
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'myclientid', 'client_secret' => 'myclientsecret']),
            true, // client_id exists
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'mywrongclientid', 'client_secret' => 'myclientsecret']),
            false, // client_id doesn't exists
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'myclientid', 'client_secret' => 'mywrongclientsecret']),
            true,
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'bad_client_id', 'client_secret' => 'myclientsecret']),
            false,
        ];
    }
}
