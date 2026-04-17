<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

class ApiClientEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createApiClient();
    }

    public function getProtectedEndpoints(): iterable
    {
        yield 'get endpoint' => [
            'GET',
            '/api-clients/infos',
            'application/json',
            // The endpoint is protected when you have no token, however it doesn't require any particular scope
            false,
        ];
    }

    public function testGetInfos()
    {
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/api-clients/infos', [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(200);

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);

        $this->assertEquals(
            [
                'apiClientId' => 1,
                'clientId' => self::CLIENT_ID,
                'clientName' => self::CLIENT_NAME,
                'description' => '',
                'externalIssuer' => null,
                'enabled' => true,
                'lifetime' => 10000,
                'scopes' => [],
            ],
            $decodedResponse
        );
    }
}
