<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform;

use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\ApiPlatform\Resources\FutureArgumentResource;
use Tests\Resources\ApiPlatform\Resources\FutureOperationResource;

/**
 * A resource using an operation class or argument of a newer core can't be instantiated: it is ignored instead of
 * breaking the container build (scopes extracted by a compiler pass) and every other endpoint.
 */
class FutureResourcesTest extends ApiTestCase
{
    public function testResourcesAreIgnored(): void
    {
        $resourceMetadataCollectionFactory = self::getContainer()->get('api_platform.metadata.resource.metadata_collection_factory');
        $this->assertCount(0, $resourceMetadataCollectionFactory->create(FutureOperationResource::class));
        $this->assertCount(0, $resourceMetadataCollectionFactory->create(FutureArgumentResource::class));

        $routes = array_map(fn ($route) => $route->getPath(), self::getContainer()->get(RouterInterface::class)->getRouteCollection()->all());
        $this->assertEmpty(array_filter($routes, fn (string $path) => str_starts_with($path, '/test/future/')));

        $scopes = array_merge(...array_map(
            fn (ApiResourceScopes $resourceScopes) => $resourceScopes->getScopes(),
            self::getContainer()->get(ApiResourceScopesExtractor::class)->getAllApiResourceScopes()
        ));
        $this->assertNotContains('future_operation_read', $scopes);
        $this->assertNotContains('future_argument_read', $scopes);
    }
}
