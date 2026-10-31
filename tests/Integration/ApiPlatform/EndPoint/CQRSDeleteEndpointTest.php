<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use Symfony\Component\HttpFoundation\Response;
use Tests\Resources\DatabaseDump;

/**
 * Runs the CQRSDelete operation with a CQRSQuery of Tests\Resources\ApiPlatform\Resources\OpenApiResponsesResource.
 */
class CQRSDeleteEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createApiClient();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['zone']);
    }

    public function testDeleteWithQueryReturnsTheMappedResource(): void
    {
        $bearerToken = $this->getBearerToken();
        $response = static::createClient()->request('DELETE', '/test/open-api/zones/1/delete-with-query', [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        // The zone is read back after the command (which disabled it), its enabled field mapped to active
        $this->assertSame(
            [
                'zoneId' => 1,
                'name' => 'Europe',
                'active' => false,
            ],
            json_decode($response->getContent(), true)
        );
    }
}
