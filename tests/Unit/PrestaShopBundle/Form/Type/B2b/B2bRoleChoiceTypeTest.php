<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Type\B2b;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Role;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\B2bRoleLang;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use PrestaShopBundle\Form\Type\B2b\B2bRoleChoiceType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class B2bRoleChoiceTypeTest extends TypeTestCase
{
    private ClassMetadata $classMetadata;
    private MockObject&LanguageContext $languageContext;
    private MockObject&EntityManagerInterface $entityManager;

    /**
     * @var B2bRole[]
     */
    private array $roles;
    private ?int $qbLanguageId = null;

    protected function getExtensions(): array
    {
        $this->classMetadata ??= $this->createClassMetadata();
        $this->languageContext = $this->createMock(LanguageContext::class);

        $repository = $this->createMock(B2bRoleRepository::class);
        $repository->method('createByLanguageIdQueryBuilder')->willReturnCallback(function (int $languageId) {
            $this->qbLanguageId = $languageId;

            return $this->createMock(QueryBuilder::class);
        });

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);
        $this->entityManager->method('getRepository')->willReturn($repository);
        $this->entityManager->method('contains')->willReturn(true);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);

        return [
            new DoctrineOrmExtension($registry),
            new PreloadedExtension([new B2bRoleChoiceType($this->languageContext)], []),
        ];
    }

    public static function getLanguageIds(): iterable
    {
        yield from [[1], [4]];
    }

    public static function getChoiceLabels(): iterable
    {
        yield 'language 1' => [1, [
            'Administrator',
            'Buyer',
            'Custom role',
        ]];
        yield 'language 2' => [2, [
            'Localized admin',
            'Localized buyer',
            'Custom role',
        ]];
    }

    public static function getFixtureIndexes(): iterable
    {
        yield from [[0], [2]];
    }

    #[DataProvider('getLanguageIds')]
    public function testItQueriesForDataInContextLanguage(int $languageId): void
    {
        $this->languageContext->method('getId')->willReturn($languageId);

        $this->createForm();

        $this->assertSame($languageId, $this->qbLanguageId);
    }

    #[DataProvider('getChoiceLabels')]
    public function testChoiceLabelGeneration(int $languageId, array $labels): void
    {
        $this->languageContext->method('getId')->willReturn($languageId);

        $roles = $this->getRoles();
        $view = $this->createForm()->createView();

        $this->assertEquals([
            1 => new ChoiceView($roles[0], '1', $labels[0]),
            2 => new ChoiceView($roles[1], '2', $labels[1]),
            4 => new ChoiceView($roles[2], '4', $labels[2]),
        ], $view->vars['choices']);
    }

    #[DataProvider('getFixtureIndexes')]
    public function testInitialDataTransformation(int $index): void
    {
        $role = $this->getRoles()[$index];
        $id = $role->getIdRole();

        $this->entityManager->method('find')->with(B2bRole::class, $id)->willReturn($role);

        $form = $this->createForm(['input' => 'id'], $id);

        $this->assertSame($role, $form->getNormData());
        $this->assertSame((string) $id, $form->getViewData());
    }

    #[DataProvider('getFixtureIndexes')]
    public function testValidDataSubmission(int $index): void
    {
        $this->submitValidData($index);
    }

    #[DataProvider('getFixtureIndexes')]
    public function testValidDataSubmissionWithIdInput(int $index): void
    {
        $this->submitValidData($index, ['input' => 'id']);
    }

    private function submitValidData(int $index, array $options = []): void
    {
        $role = $this->getRoles()[$index];
        $id = $role->getIdRole();
        $expectedData = 'id' === ($options['input'] ?? null) ? $id : $role;

        $form = $this->createForm($options);
        $form->submit((string) $id);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($expectedData, $form->getData());
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createForm(array $options = [], mixed $data = null): FormInterface
    {
        return $this->factory->create(B2bRoleChoiceType::class, $data, [
            'choices' => $this->getRoles(), // trying to mock the default QB result would be way too much trouble
            ...$options,
        ]);
    }

    /**
     * @return B2bRole[]
     */
    private function getRoles(): array
    {
        return $this->roles ??= [
            $this->createRole(1, Role::ADMIN, [
                1 => 'Administrator',
                2 => 'Localized admin',
            ]),
            $this->createRole(2, Role::BUYER, [
                2 => 'Localized buyer',
            ]),
            $this->createRole(4, 'CUSTOM_ROLE'),
        ];
    }

    /**
     * @param array<int, string> $names
     */
    private function createRole(int $id, string $role, array $names = []): B2bRole
    {
        $entity = (new B2bRole())->setRole($role);
        $this->classMetadata->setIdentifierValues($entity, ['id' => $id]);

        foreach ($names as $languageId => $name) {
            $entity->addTranslation(new B2bRoleLang($this->createLanguage($languageId), $name));
        }

        return $entity;
    }

    private function createLanguage(int $id): Lang
    {
        $language = $this->createMock(Lang::class);
        $language->method('getId')->willReturn($id);

        return $language;
    }

    private function createClassMetadata(): ClassMetadata
    {
        $metadata = new ClassMetadata(B2bRole::class);
        $metadata->mapField(['fieldName' => 'id', 'id' => true, 'type' => 'integer']);
        $metadata->wakeupReflection(new RuntimeReflectionService());

        return $metadata;
    }
}
