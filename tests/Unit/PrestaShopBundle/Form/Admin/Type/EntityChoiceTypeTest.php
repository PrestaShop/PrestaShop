<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShopBundle\Form\Admin\Type\EntityChoiceType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class EntityChoiceTypeTest extends TypeTestCase
{
    private MockObject&EntityManagerInterface $entityManager;

    /**
     * @var TestEntity[]
     */
    private array $entities;

    protected function getExtensions(): array
    {
        $classMetadata = new ClassMetadata(TestEntity::class);
        $classMetadata->mapField(['fieldName' => 'id', 'id' => true, 'type' => 'integer']);
        $classMetadata->wakeupReflection(new RuntimeReflectionService());

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata);
        $this->entityManager->method('contains')->willReturn(true);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);

        return [
            new DoctrineOrmExtension($registry),
            new PreloadedExtension([new EntityChoiceType()], []),
        ];
    }

    public static function getFixtureIndexes(): iterable
    {
        yield from [[0], [2]];
    }

    #[DataProvider('getFixtureIndexes')]
    public function testInitialDataTransformation(int $index): void
    {
        $entity = $this->getEntities()[$index];

        $this->entityManager->method('find')->with(TestEntity::class, $entity->id)->willReturn($entity);

        $form = $this->createForm($entity->id);

        $this->assertSame($entity, $form->getNormData());
        $this->assertSame((string) $entity->id, $form->getViewData());
    }

    #[DataProvider('getFixtureIndexes')]
    public function testValidDataSubmissionWithIdInput(int $index): void
    {
        $id = $this->getEntities()[$index]->id;

        $form = $this->createForm();
        $form->submit((string) $id);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($id, $form->getData());
    }

    private function createForm(mixed $data = null): FormInterface
    {
        return $this->factory->create(EntityChoiceType::class, $data, [
            'class' => TestEntity::class,
            'choices' => $this->getEntities(),
            'input' => 'id',
        ]);
    }

    /**
     * @return TestEntity[]
     */
    private function getEntities(): array
    {
        return $this->entities ??= [
            new TestEntity(1),
            new TestEntity(2),
            new TestEntity(4),
        ];
    }
}

class TestEntity
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
