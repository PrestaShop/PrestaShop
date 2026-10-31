<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use Symfony\Component\HttpFoundation\Response;

/**
 * Error payloads of Tests\Resources\ApiPlatform\Resources\OpenApiResponsesResource.
 */
class ErrorResponseEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createApiClient();
    }

    public function testMappedExceptionStatusIsInThePayload(): void
    {
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('GET', '/test/open-api/zones/9999', [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        // ZoneNotFoundException is mapped to 404 by exceptionToStatus, the payload used to say 500
        $payload = json_decode($response->getContent(false), true);
        $this->assertSame(Response::HTTP_NOT_FOUND, $payload['status']);
    }
}
