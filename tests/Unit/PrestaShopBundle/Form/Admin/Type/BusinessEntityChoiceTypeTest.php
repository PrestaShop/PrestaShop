<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use PrestaShopBundle\Form\Admin\Type\BusinessEntityChoiceType;
use PrestaShopBundle\Form\Admin\Type\EntityChoiceType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class BusinessEntityChoiceTypeTest extends TypeTestCase
{
    private MockObject&ShopContext $shopContext;
    private bool $queried = false;
    private ?array $queriedShopIds = null;

    protected function getExtensions(): array
    {
        $this->shopContext = $this->createMock(ShopContext::class);

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('createByShopIdsQueryBuilder')->willReturnCallback(function (?array $shopIds) {
            $this->queried = true;
            $this->queriedShopIds = $shopIds;

            return $this->createMock(QueryBuilder::class);
        });

        $classMetadata = new ClassMetadata(BusinessEntity::class);
        $classMetadata->mapField(['fieldName' => 'id', 'id' => true, 'type' => 'integer']);
        $classMetadata->wakeupReflection(new RuntimeReflectionService());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getClassMetadata')->willReturn($classMetadata);
        $entityManager->method('getRepository')->willReturn($repository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($entityManager);

        return [
            new DoctrineOrmExtension($registry),
            new PreloadedExtension([new EntityChoiceType(), new BusinessEntityChoiceType($this->shopContext)], []),
        ];
    }

    public static function getShopScopes(): iterable
    {
        yield 'all shops context' => [true, [], [], null];
        yield 'shops group context' => [false, [1, 3], [], [1, 3]];
        yield 'explicit shops' => [false, [1, 3], ['shop_ids' => [2]], [2]];
    }

    /**
     * @param int[] $contextShopIds
     * @param array<string, mixed> $options
     * @param int[]|null $expectedShopIds
     */
    #[DataProvider('getShopScopes')]
    public function testItPassesShopIdsToQuery(bool $isAllShopContext, array $contextShopIds, array $options, ?array $expectedShopIds): void
    {
        $this->shopContext->method('isAllShopContext')->willReturn($isAllShopContext);
        $this->shopContext->method('getAssociatedShopIds')->willReturn($contextShopIds);

        $this->factory->create(BusinessEntityChoiceType::class, null, $options + ['choices' => []]);

        $this->assertTrue($this->queried);
        $this->assertSame($expectedShopIds, $this->queriedShopIds);
    }
}
