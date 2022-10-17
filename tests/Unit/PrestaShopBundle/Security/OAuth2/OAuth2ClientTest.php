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

use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Security\OAuth2\OAuth2Client;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuth2ClientTest extends TestCase
{
    private $resourceServer;
    private $client;

    public function setUp(): void
    {
        $userProvider = new InMemoryUserProvider(['myclientid' => ['password' => 'myclientsecret2']]);
        $this->resourceServer = $this->createMock(ResourceServer::class);
        $this->resourceServer->method('validateAuthenticatedRequest')->willReturnCallback(function (ServerRequestInterface $request) {
            return $request->withAttribute('oauth_client_id', $request->getParsedBody()['client_id'] ?? null);
        });
        $this->client = new OAuth2Client($this->resourceServer, $userProvider);
        parent::setUp();
    }

    /**
     * @dataProvider getUserDataProvider
     */
    public function testGetUser(ServerRequestInterface $request, bool $exists): void
    {
        $user = $this->client->getUser($request);
        if ($exists) {
            $this->assertTrue($user instanceof UserInterface);
        } else {
            $this->assertNull($user);
        }
    }

    /**
     * @dataProvider getUserDataProvider
     */
    public function testIsTokenValid(ServerRequestInterface $request): void
    {
        $this->resourceServer->expects($this->once())->method('validateAuthenticatedRequest');
        $this->client->isTokenValid($request);
    }

    public function getUserDataProvider(): iterable
    {
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'myclientid', 'client_secret' => 'myclientsecret']),
            true,
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'mywrongclientid', 'client_secret' => 'myclientsecret']),
            false,
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_id' => 'myclientid', 'client_secret' => 'mywrongclientsecret']),
            true,
        ];
        yield [
            (new ServerRequest('POST', '/'))
                ->withParsedBody(['client_secret' => 'myclientsecret']),
            false,
        ];
    }
}
