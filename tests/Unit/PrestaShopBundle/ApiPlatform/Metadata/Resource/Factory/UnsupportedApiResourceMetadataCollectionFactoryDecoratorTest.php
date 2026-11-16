<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\Resource\Factory\AttributesResourceMetadataCollectionFactory;
use Error;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory\UnsupportedApiResourceMetadataCollectionFactoryDecorator;
use Psr\Log\LoggerInterface;
use Tests\Resources\ApiPlatform\InvalidResources\ConstructorErrorResource;
use Tests\Resources\ApiPlatform\InvalidResources\MissingModuleClassResource;
use Tests\Resources\ApiPlatform\Resources\FutureArgumentResource;
use Tests\Resources\ApiPlatform\Resources\FutureOperationResource;
use TypeError;

class UnsupportedApiResourceMetadataCollectionFactoryDecoratorTest extends TestCase
{
    /**
     * @dataProvider getUnsupportedResources
     */
    public function testUnsupportedResourceIsIgnored(string $resourceClass, string $expectedError): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning')->with($this->stringContains($expectedError));
        $factory = new UnsupportedApiResourceMetadataCollectionFactoryDecorator(new AttributesResourceMetadataCollectionFactory(), $logger);

        $this->assertCount(0, $factory->create($resourceClass));
    }

    public static function getUnsupportedResources(): iterable
    {
        yield 'operation class of a newer core' => [FutureOperationResource::class, 'Class "PrestaShopBundle\ApiPlatform\Metadata\CQRSFutureOperation" not found'];
        yield 'operation argument of a newer core' => [FutureArgumentResource::class, 'Unknown named parameter $futureArgument'];
    }

    /**
     * @dataProvider getInvalidResources
     */
    public function testOtherErrorsAreThrown(string $resourceClass, string $expectedError): void
    {
        $factory = new UnsupportedApiResourceMetadataCollectionFactoryDecorator(new AttributesResourceMetadataCollectionFactory());

        $this->expectException($expectedError);
        $factory->create($resourceClass);
    }

    public static function getInvalidResources(): iterable
    {
        yield 'missing class of the module' => [MissingModuleClassResource::class, Error::class];
        yield 'argument rejected by the operation' => [ConstructorErrorResource::class, TypeError::class];
    }
}
