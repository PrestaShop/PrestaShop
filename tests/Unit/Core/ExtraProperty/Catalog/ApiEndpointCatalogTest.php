<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalog;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class ApiEndpointCatalogTest extends TestCase
{
    public function testEndpointsAreReadFromTheOpenApiDocumentAndSorted(): void
    {
        $paths = new Paths();
        $paths->addPath('/products/{productId}', new PathItem(get: new Operation(), patch: new Operation(), delete: new Operation()));
        $paths->addPath('/products', new PathItem(get: new Operation(), post: new Operation()));
        // No operation at all: not an endpoint.
        $paths->addPath('/empty', new PathItem());

        $catalog = $this->createCatalog($paths);
        $entries = $catalog->getAll();

        $this->assertSame([
            ['uriTemplate' => '/products', 'methods' => ['GET', 'POST']],
            ['uriTemplate' => '/products/{productId}', 'methods' => ['DELETE', 'GET', 'PATCH']],
        ], $entries);
    }

    public function testHasUriTemplateNormalizesThePath(): void
    {
        $paths = new Paths();
        $paths->addPath('/products', new PathItem(get: new Operation()));

        $catalog = $this->createCatalog($paths);

        $this->assertTrue($catalog->hasUriTemplate('/products'));
        $this->assertTrue($catalog->hasUriTemplate('products/'));
        $this->assertTrue($catalog->hasUriTemplate(' products '));
        $this->assertFalse($catalog->hasUriTemplate('/ghosts'));
    }

    public function testABrokenDocumentYieldsAnEmptyCatalogAndIsNotCached(): void
    {
        $factory = $this->createMock(OpenApiFactoryInterface::class);
        $factory->method('__invoke')->willThrowException(new RuntimeException('a module resource is broken'));
        $cache = new ArrayAdapter();

        $catalog = new ApiEndpointCatalog($factory, new NullLogger(), $cache);

        $this->assertSame([], $catalog->getAll());
        $this->assertFalse($catalog->hasUriTemplate('/products'));
        // The failure was not cached: a healthy instance sharing the pool scans again.
        $this->assertFalse($cache->getItem('api_endpoints')->isHit());
    }

    public function testTheDocumentIsGeneratedOnce(): void
    {
        $paths = new Paths();
        $paths->addPath('/products', new PathItem(get: new Operation()));

        $factory = $this->createMock(OpenApiFactoryInterface::class);
        $factory->expects($this->once())->method('__invoke')
            ->willReturn(new OpenApi(new Info('Admin API', '1.0'), [], $paths));

        $catalog = new ApiEndpointCatalog($factory, new NullLogger(), new ArrayAdapter());
        $catalog->getAll();
        $catalog->getAll();
        $this->assertTrue($catalog->hasUriTemplate('/products'));
    }

    private function createCatalog(Paths $paths): ApiEndpointCatalog
    {
        $factory = $this->createMock(OpenApiFactoryInterface::class);
        $factory->method('__invoke')->willReturn(new OpenApi(new Info('Admin API', '1.0'), [], $paths));

        return new ApiEndpointCatalog($factory, new NullLogger(), new ArrayAdapter());
    }
}
