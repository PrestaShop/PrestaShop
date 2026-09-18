<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\DataTransformer\IdToEntityTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToEntityTransformerTest extends TestCase
{
    private MockObject&EntityManagerInterface $entityManager;

    /**
     * @var array<string, ClassMetadata>
     */
    private array $classMetadata = [];

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturnCallback($this->getClassMetadata(...));
    }

    public static function getTransformValues(): iterable
    {
        yield 'integer id' => [1, new SingleIdEntity(1)];
        yield 'string id' => ['id', new SingleIdEntity('id')];
        yield 'subclass' => [2, new ChildSingleIdEntity(2)];
    }

    public static function getInvalidReverseTransformValues(): iterable
    {
        yield 'integer' => [1];
        yield 'string' => ['id'];
        yield 'class mismatch' => [new stdClass()];
    }

    public function testConstructorThrowsOnCompositeIdentifier(): void
    {
        $this->expectException(LogicException::class);

        $this->createTransformer(CompositeIdEntity::class);
    }

    #[DataProvider('getTransformValues')]
    public function testTransform(mixed $id, object $entity): void
    {
        $this->entityManager->expects($this->once())->method('find')->with(SingleIdEntity::class, $id)->willReturn($entity);

        $this->assertSame($entity, $this->createTransformer()->transform($id));
    }

    public function testTransformNull(): void
    {
        $this->entityManager->expects($this->never())->method('find');

        $this->assertNull($this->createTransformer()->transform(null));
    }

    public function testTransformThrowsOnEntityNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Entity not found.');

        $this->createTransformer()->transform(1);
    }

    #[DataProvider('getTransformValues')]
    public function testReverseTransform(mixed $id, object $entity): void
    {
        $this->assertSame($id, $this->createTransformer()->reverseTransform($entity));
    }

    public function testReverseTransformNull(): void
    {
        $this->assertNull($this->createTransformer()->reverseTransform(null));
    }

    public function testReverseTransformObjectIdWithoutAssociation(): void
    {
        $id = new DateTimeImmutable('2000-01-01 00:00:00');

        $this->assertSame($id, $this->createTransformer()->reverseTransform(new SingleIdEntity($id)));
    }

    public function testReverseTransformReturnsDerivedId(): void
    {
        $simple = new SingleIdEntity(123);
        $entity = new DerivedIdEntity($simple);
        $transformer = $this->createTransformer(DerivedIdEntity::class);

        $this->assertSame(123, $transformer->reverseTransform($entity));
    }

    #[DataProvider('getInvalidReverseTransformValues')]
    public function testReverseTransformThrowsOnUnexpectedType(mixed $value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessageMatches('/^Expected an instance of/');

        $this->createTransformer()->reverseTransform($value);
    }

    private function createTransformer(string $class = SingleIdEntity::class): IdToEntityTransformer
    {
        return new IdToEntityTransformer($this->entityManager, $class);
    }

    private function getClassMetadata(string $class): ClassMetadata
    {
        return $this->classMetadata[$class] ??= $this->createClassMetadata($class);
    }

    private function createClassMetadata(string $class): ClassMetadata
    {
        $metadata = new ClassMetadata($class);

        switch ($class) {
            case SingleIdEntity::class:
            case ChildSingleIdEntity::class:
                $metadata->mapField(['fieldName' => 'id', 'id' => true]);
                break;
            case DerivedIdEntity::class:
                $metadata->mapOneToOne(['fieldName' => 'entity', 'targetEntity' => SingleIdEntity::class, 'id' => true]);
                break;
            case CompositeIdEntity::class:
                $metadata->mapField(['fieldName' => 'a', 'id' => true]);
                $metadata->mapField(['fieldName' => 'b', 'id' => true]);
                break;
            default:
                $this->fail(\sprintf('Unexpected class "%s".', $class));
        }

        $metadata->wakeupReflection(new RuntimeReflectionService());

        return $metadata;
    }
}

class SingleIdEntity
{
    public function __construct(
        public readonly mixed $id,
    ) {
    }
}

class ChildSingleIdEntity extends SingleIdEntity
{
}

class DerivedIdEntity
{
    public function __construct(
        public readonly SingleIdEntity $entity,
    ) {
    }
}

class CompositeIdEntity
{
    public function __construct(
        public readonly int $a,
        public readonly int $b,
    ) {
    }
}
