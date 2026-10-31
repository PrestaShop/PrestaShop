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
 * Runs the write collection operations of Tests\Resources\ApiPlatform\Resources\CollectionOperationsResource.
 */
class CQRSCollectionOperationsEndpointTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['feature_product', 'feature_value', 'feature_value_lang']);
        self::createApiClient();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['feature_product', 'feature_value', 'feature_value_lang']);
    }

    public function testCreateCollection(): void
    {
        $featureValues = $this->request('POST', '/test/collections/products/1/feature-values', [
            'featureValues' => [
                ['featureId' => 2, 'featureValueId' => 7],
            ],
        ], Response::HTTP_CREATED);

        $this->assertSame([
            ['productId' => 1, 'featureId' => 2, 'featureValueId' => 7, 'custom' => false],
        ], $featureValues);
    }

    public function testCreateCollectionWithoutQuery(): void
    {
        $featureValues = $this->request('POST', '/test/collections/products/3/feature-value-ids', [
            'featureValues' => [
                ['featureId' => 1, 'featureValueId' => 6],
                ['featureId' => 2, 'featureValueId' => 8],
            ],
        ], Response::HTTP_CREATED);

        // The command only returns the IDs of the feature values
        $this->assertSame([
            ['productId' => 3, 'featureValueId' => 6],
            ['productId' => 3, 'featureValueId' => 8],
        ], $featureValues);
    }

    public function testUpdateCollection(): void
    {
        $featureValues = $this->request('PUT', '/test/collections/products/1/feature-values', [
            'featureValues' => [
                ['featureId' => 1, 'featureValueId' => 2],
            ],
        ]);

        $this->assertSame([
            ['productId' => 1, 'featureId' => 1, 'featureValueId' => 2, 'custom' => false],
        ], $featureValues);
    }

    public function testPartialUpdateCollection(): void
    {
        $featureValues = $this->request('PATCH', '/test/collections/products/1/feature-values', [
            'featureValues' => [
                ['featureId' => 1, 'featureValueId' => 3],
                ['featureId' => 2, 'featureValueId' => 7],
            ],
        ]);

        $this->assertSame([
            ['productId' => 1, 'featureId' => 1, 'featureValueId' => 3, 'custom' => false],
            ['productId' => 1, 'featureId' => 2, 'featureValueId' => 7, 'custom' => false],
        ], $featureValues);
    }

    public function testDeleteCollectionWithBody(): void
    {
        $featureValues = $this->request('DELETE', '/test/collections/products/2/feature-values/bulk-delete', [
            'featureValueIds' => [7],
        ]);

        $this->assertSame([
            ['productId' => 2, 'featureId' => 1, 'featureValueId' => 4, 'custom' => false],
        ], $featureValues);
    }

    public function testDeleteCollection(): void
    {
        $featureValues = $this->request('DELETE', '/test/collections/products/2/feature-values');

        $this->assertSame([], $featureValues);
    }

    public function testDeleteCollectionWithoutQuery(): void
    {
        $options = ['auth_bearer' => $this->getBearerToken()];
        $response = static::createClient()->request('DELETE', '/test/collections/products/3/feature-value-ids', $options);

        // The command returns nothing
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertSame('', $response->getContent());
    }

    /**
     * The test operations have no scope, any token is accepted.
     */
    private function request(string $method, string $uri, ?array $data = null, int $expectedStatus = Response::HTTP_OK): array
    {
        $options = ['auth_bearer' => $this->getBearerToken()];
        if (null !== $data) {
            $options['json'] = $data;
            if ('PATCH' === $method) {
                $options['headers'] = ['Content-Type' => 'application/merge-patch+json'];
            }
        }
        $response = static::createClient()->request($method, $uri, $options);
        self::assertResponseStatusCodeSame($expectedStatus);

        return json_decode($response->getContent(), true);
    }
}
