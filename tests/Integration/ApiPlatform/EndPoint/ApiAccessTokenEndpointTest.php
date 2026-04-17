<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

class ApiAccessTokenEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::createApiClient(
            [
                'hook_read',
                'hook_write',
                'customer_group_read',
            ],
            9999
        );
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function getContentType(): iterable
    {
        yield 'form-urlencoded' => [
            'application/x-www-form-urlencoded',
        ];

        yield 'multipart' => [
            'multipart/form-data',
        ];
    }

    /**
     * @dataProvider getContentType
     */
    public function testApiAccessToken(string $contentType): void
    {
        $parameters = ['parameters' => [
            'client_id' => static::CLIENT_ID,
            'client_secret' => static::$clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => [
                'hook_read',
                'hook_write',
                'customer_group_read',
            ],
        ]];
        $options = [
            'extra' => $parameters,
            'headers' => [
                'content-type' => $contentType,
            ],
        ];
        $response = static::createClient()->request('POST', '/access_token', $options);
        $token = json_decode($response->getContent())->access_token;
        $decodedToken = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));

        static::assertEquals(9999, json_decode($response->getContent())->expires_in);
        static::assertEquals(
            [
                'is_authenticated',
                'hook_read',
                'hook_write',
                'customer_group_read',
            ],
            $decodedToken->scopes
        );
        static::assertEquals('Bearer', json_decode($response->getContent())->token_type);
        static::assertEquals(static::CLIENT_ID, $decodedToken->aud);
        static::assertNotNull($decodedToken->jti);
        static::assertNotNull($decodedToken->iat);
        static::assertNotNull($decodedToken->nbf);
        static::assertNotNull($decodedToken->exp);
        static::assertNotNull($decodedToken->sub);
    }

    public function testNonExistentScope(): void
    {
        $parameters = ['parameters' => [
            'client_id' => static::CLIENT_ID,
            'client_secret' => static::$clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => [
                'non_existent_scope',
            ],
        ]];
        $options = [
            'extra' => $parameters,
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $response = static::createClient()->request('POST', '/access_token', $options);
        $this->assertEquals(400, $response->getInfo('http_code'));
        $decodedResponse = json_decode($response->getContent(false), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertEquals([
            'error' => 'invalid_scope',
            'error_description' => 'The requested scope is invalid, unknown, or malformed',
            'hint' => 'Check the `non_existent_scope` scope',
            'message' => 'The requested scope is invalid, unknown, or malformed',
        ], $decodedResponse);
    }

    public function testUnauthorizedScope(): void
    {
        $parameters = ['parameters' => [
            'client_id' => static::CLIENT_ID,
            'client_secret' => static::$clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => [
                'customer_group_write',
            ],
        ]];
        $options = [
            'extra' => $parameters,
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $response = static::createClient()->request('POST', '/access_token', $options);
        $this->assertEquals(401, $response->getInfo('http_code'));
        $decodedResponse = json_decode($response->getContent(false), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertEquals([
            'error' => 'access_denied',
            'error_description' => 'The resource owner or authorization server denied the request.',
            'hint' => 'Usage of scope `customer_group_write` is not allowed for this client',
            'message' => 'The resource owner or authorization server denied the request.',
        ], $decodedResponse);
    }

    public function testInvalidCredentials(): void
    {
        // Test with non-existing API client
        $options = [
            'extra' => ['parameters' => [
                'client_id' => 'invalid_client',
                'client_secret' => 'invalid_secret',
                'grant_type' => 'client_credentials',
            ]],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $response = static::createClient()->request('POST', '/access_token', $options);
        $this->assertEquals(401, $response->getInfo('http_code'));
        $decodedResponse = json_decode($response->getContent(false), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertEquals([
            'error' => 'invalid_client',
            'error_description' => 'Client authentication failed',
            'message' => 'Client authentication failed',
        ], $decodedResponse);

        // Test with existing API client but invalid secret
        $options = [
            'extra' => ['parameters' => [
                'client_id' => static::CLIENT_ID,
                'client_secret' => 'invalid_secret',
                'grant_type' => 'client_credentials',
            ]],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $response = static::createClient()->request('POST', '/access_token', $options);
        $this->assertEquals(401, $response->getInfo('http_code'));
        $decodedResponse = json_decode($response->getContent(false), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertEquals([
            'error' => 'invalid_client',
            'error_description' => 'Client authentication failed',
            'message' => 'Client authentication failed',
        ], $decodedResponse);
    }
}
