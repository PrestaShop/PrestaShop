<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use ObjectModel;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertiesBag;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ModuleFieldsBag;
use RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ObjectModel fixture with a lang-multishop definition, used to exercise
 * ExtraPropertiesBag::createForEntity() without a database.
 */
class ExtraPropertiesBagTestEntity extends ObjectModel
{
    public static $definition = [
        'table' => 'bag_test_entity',
        'primary' => 'id_bag_test_entity',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [],
    ];
}

class ExtraPropertiesBagTest extends TestCase
{
    private const ENTITY_TABLE = 'bag_test_entity';

    public function testNullContainerYieldsEmptyBag(): void
    {
        $bag = ExtraPropertiesBag::createForEntity(
            null,
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops()
        );

        $this->assertSame([], $bag->jsonSerialize());
    }

    public function testNonPositiveEntityIdYieldsEmptyBagWithoutTouchingServices(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->expects($this->never())->method('getExtraProperties');
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->expects($this->never())->method('getAllDefinitions');

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            0,
            null,
            ShopConstraint::allShops()
        );

        $this->assertSame([], $bag->jsonSerialize());
    }

    public function testNonObjectModelClassYieldsEmptyBagWithoutTouchingServices(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->expects($this->never())->method('getExtraProperties');
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->expects($this->never())->method('getAllDefinitions');

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            stdClass::class,
            5,
            null,
            ShopConstraint::allShops()
        );

        $this->assertSame([], $bag->jsonSerialize());
    }

    public function testContainerFailureYieldsEmptyBag(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(static function (): never {
            throw new RuntimeException('service not found');
        });

        $bag = ExtraPropertiesBag::createForEntity(
            $container,
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops()
        );

        $this->assertSame([], $bag->jsonSerialize());
    }

    public function testLoaderIsMemoizedAndValuesWrappedInModuleFieldsBag(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->expects($this->once())
            ->method('getExtraProperties')
            ->willReturn(['mymodule' => ['video_link' => 'https://example.com']]);
        $repository = $this->buildRepository($this->definition('video_link'));

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops()
        );

        // Two accesses, one load.
        $this->assertInstanceOf(ModuleFieldsBag::class, $bag['mymodule']);
        $this->assertSame('https://example.com', $bag['mymodule']['video_link']);
        $this->assertSame('https://example.com', $bag->jsonSerialize()['mymodule']['video_link']);
    }

    public function testJsonSerializeReturnsFullyFlattenedStructure(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->method('getExtraProperties')->willReturn([
            'mymodule' => [
                'video_link' => 'https://example.com',
                'is_dangerous' => true,
            ],
            '_core' => [
                // Lang-scoped value read without a langId: [id_lang => value] array.
                'promo_banner' => [1 => 'Hello', 2 => 'Bonjour'],
                'revision_code' => 42,
            ],
        ]);
        $repository = $this->buildRepository($this->definition('video_link'));

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops()
        );

        // assertSame on the whole structure also proves no ModuleFieldsBag leaks through:
        // the nested module entries must be plain arrays, identical to the reader output.
        $this->assertSame([
            'mymodule' => [
                'video_link' => 'https://example.com',
                'is_dangerous' => true,
            ],
            '_core' => [
                'promo_banner' => [1 => 'Hello', 2 => 'Bonjour'],
                'revision_code' => 42,
            ],
        ], $bag->jsonSerialize());
    }

    public function testJsonSerializeReflectsWrites(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->method('getExtraProperties')->willReturn([
            'mymodule' => ['video_link' => 'https://example.com', 'is_dangerous' => false],
        ]);
        $repository = $this->buildRepository($this->definition('video_link'));

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops()
        );
        $bag['mymodule']['video_link'] = 'https://changed.example';

        $this->assertSame([
            'mymodule' => ['video_link' => 'https://changed.example', 'is_dangerous' => false],
        ], $bag->jsonSerialize());
    }

    public function testFrontOfficeByDefaultPassesFilteredDefinitionsToReader(): void
    {
        $capturedDefinitions = null;
        $reader = $this->buildCapturingReader($capturedDefinitions);
        $repository = $this->buildRepository(
            $this->definition('fo_field', displayFront: true),
            $this->definition('bo_field', displayFront: false),
        );

        // No forFrontOffice argument: the default is true, consistent with
        // ExtraPropertyDefinition::$displayFront defaulting to true.
        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops(),
        );
        $bag->jsonSerialize();

        $this->assertInstanceOf(ExtraPropertyDefinitionCollection::class, $capturedDefinitions);
        $this->assertCount(1, $capturedDefinitions);
        $this->assertSame('fo_field', $capturedDefinitions->first()->getPropertyName());
    }

    public function testForFrontOfficeWithNoFrontOfficeFieldSkipsReader(): void
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->expects($this->never())->method('getExtraProperties');
        $repository = $this->buildRepository($this->definition('bo_field', displayFront: false));

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops(),
            forFrontOffice: true,
        );

        $this->assertSame([], $bag->jsonSerialize());
    }

    public function testBackOfficeReceivesUnfilteredEntityDefinitions(): void
    {
        $capturedDefinitions = null;
        $reader = $this->buildCapturingReader($capturedDefinitions);
        $repository = $this->buildRepository(
            $this->definition('fo_field', displayFront: true),
            $this->definition('bo_field', displayFront: false),
            $this->definition('other_entity_field', entityName: 'other_entity'),
        );

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            5,
            null,
            ShopConstraint::allShops(),
            forFrontOffice: false,
        );
        $bag->jsonSerialize();

        // Filtered by entity, but not by display_front.
        $this->assertCount(2, $capturedDefinitions);
    }

    public function testEntityMetadataAndContextArgumentsArePassedToReader(): void
    {
        $captured = [];
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->method('getExtraProperties')->willReturnCallback(
            static function (...$args) use (&$captured): array {
                $captured = $args;

                return [];
            }
        );
        $repository = $this->buildRepository($this->definition('video_link'));
        $shopConstraint = ShopConstraint::shop(3);

        $bag = ExtraPropertiesBag::createForEntity(
            $this->buildContainer($reader, $repository),
            ExtraPropertiesBagTestEntity::class,
            7,
            2,
            $shopConstraint
        );
        $bag->jsonSerialize();

        $this->assertSame(self::ENTITY_TABLE, $captured[0]);
        $this->assertSame('id_bag_test_entity', $captured[1]);
        $this->assertSame(7, $captured[2]);
        $this->assertSame(2, $captured[3]);
        $this->assertSame($shopConstraint, $captured[4]);
        // Fixture declares multilang + multilang_shop.
        $this->assertTrue($captured[5]);
    }

    private function buildContainer(
        ExtraPropertyReaderInterface $reader,
        ExtraPropertyDefinitionRepositoryInterface $repository,
    ): ContainerInterface {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(
            static fn (string $id): object => match ($id) {
                ExtraPropertyReaderInterface::class => $reader,
                ExtraPropertyDefinitionRepositoryInterface::class => $repository,
                default => throw new RuntimeException(sprintf('Unknown service "%s"', $id)),
            }
        );

        return $container;
    }

    private function buildRepository(ExtraPropertyDefinition ...$definitions): ExtraPropertyDefinitionRepositoryInterface
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection($definitions));

        return $repository;
    }

    private function buildCapturingReader(?ExtraPropertyDefinitionCollection &$capturedDefinitions): ExtraPropertyReaderInterface
    {
        $reader = $this->createMock(ExtraPropertyReaderInterface::class);
        $reader->method('getExtraProperties')->willReturnCallback(
            static function (...$args) use (&$capturedDefinitions): array {
                $capturedDefinitions = $args[6] ?? null;

                return [];
            }
        );

        return $reader;
    }

    private function definition(
        string $propertyName,
        bool $displayFront = true,
        string $entityName = self::ENTITY_TABLE,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: $entityName,
            propertyName: $propertyName,
            moduleName: 'mymodule',
            displayFront: $displayFront,
        );
    }
}
