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

namespace Tests\Integration\ApiPlatform;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\Resetter\ApiClientResetter;

class ApiSecurityTest extends ApiTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        ApiClientResetter::resetApiClient();
    }

    public function testAuthenticationWithoutScopeNeededSuccess()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function testAuthenticationWithoutTokenFailed()
    {
        self::createApiClient();
        $response = static::createClient()->request('GET', '/test/unscoped/product/1');

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('"No Authorization header provided"', $response->getContent(false));
    }

    public function testAuthenticationWithoutBearerFailed()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('"Bearer token missing"', $response->getContent(false));
    }

    public function testAuthenticationWithInvalidCredential()
    {
        self::createApiClient();
        $response = static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer toto',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        // The internal server doesn't recognize the token format, so the authenticator has no server to use
        $this->assertEquals('"No authorization server matching your credentials"', $response->getContent(false));
    }

    public function testAuthenticationWithLowerCaseBearerFailed()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('"Bearer token missing"', $response->getContent(false));
    }

    public function testAuthenticationWithScopeSuccess()
    {
        self::createApiClient(['product_read']);
        $bearerToken = $this->getBearerToken(['product_read']);
        $response = static::createClient()->request('GET', '/test/scoped/product/1', [
            'auth_bearer' => $bearerToken,
        ]);

        self::assertResponseStatusCodeSame(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function testAuthenticationWithoutScopeInJWTTokenFailed()
    {
        // API Client does have the scope associated
        self::createApiClient(['product_read']);
        // But the token is generated without containing the required scope
        $bearerToken = $this->getBearerToken();
        static::createClient()->request('GET', '/test/scoped/product/1', [
            'auth_bearer' => $bearerToken,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider getValidScopeParameters
     *
     * @param string $scopeParameter
     * @param string|array $scopes
     * @param array $expectedScopes
     *
     * @return void
     */
    public function testAuthenticationWithDifferentScopesSuccess(string $scopeParameter, string|array $scopes, array $expectedScopes): void
    {
        self::createApiClient(['product_read', 'product_write']);
        $options = [
            'extra' => [
                'parameters' => [
                    'client_id' => static::CLIENT_ID,
                    'client_secret' => static::$clientSecret,
                    'grant_type' => 'client_credentials',
                ],
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $options['extra']['parameters'][$scopeParameter] = $scopes;
        $response = static::createClient([], [])->request('POST', '/access_token', $options);
        self::assertResponseStatusCodeSame(200);
        $parsedResponse = json_decode($response->getContent(), true);
        $this->assertNotEmpty($parsedResponse);
        $this->assertArrayHasKey('access_token', $parsedResponse);
        $accessToken = $parsedResponse['access_token'];
        $this->assertNotEmpty($accessToken);

        $tokenParser = new Parser(new JoseEncoder());
        $token = $tokenParser->parse($accessToken);
        $this->assertInstanceOf(Plain::class, $token);
        $this->assertTrue($token->claims()->has('scopes'));
        $tokenScopes = $token->claims()->get('scopes');
        $this->assertIsArray($tokenScopes);
        $this->assertEquals($expectedScopes, $tokenScopes);
    }

    public function getValidScopeParameters(): iterable
    {
        yield 'empty string scope' => ['scope', '', ['is_authenticated']];
        yield 'single string scope' => ['scope', 'product_read', ['is_authenticated', 'product_read']];
        yield 'multiple string scope with comma' => ['scope', 'product_read,product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scope with space' => ['scope', 'product_read product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scope with comma and space' => ['scope', 'product_read, product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scope with comma and many spaces' => ['scope', '  product_read ,  product_write ', ['is_authenticated', 'product_read', 'product_write']];

        yield 'single array scope' => ['scope', ['product_read'], ['is_authenticated', 'product_read']];
        yield 'multiple array scope' => ['scope', ['product_read', 'product_write'], ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple array scope with spaces' => ['scope', [' product_read', 'product_write '], ['is_authenticated', 'product_read', 'product_write']];

        yield 'empty string scopes' => ['scopes', '', ['is_authenticated']];
        yield 'single string scopes' => ['scopes', 'product_read', ['is_authenticated', 'product_read']];
        yield 'multiple string scopes with comma' => ['scopes', 'product_read,product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scopes with space' => ['scopes', 'product_read product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scopes with comma and space' => ['scopes', 'product_read, product_write', ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple string scopes with comma and many spaces' => ['scopes', '  product_read ,  product_write ', ['is_authenticated', 'product_read', 'product_write']];

        yield 'single array scopes' => ['scopes', ['product_read'], ['is_authenticated', 'product_read']];
        yield 'multiple array scopes' => ['scopes', ['product_read', 'product_write'], ['is_authenticated', 'product_read', 'product_write']];
        yield 'multiple array scopes with spaces' => ['scopes', [' product_read', 'product_write '], ['is_authenticated', 'product_read', 'product_write']];
    }
}
