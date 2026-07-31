<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;

/**
 * Pins the multishop guard of the repository at DQL level. Dropping either
 * `if (null !== $shopIds)` block would silently expose entities of another shop through the
 * guessable /business-entities/{id}/view URL, and no other test in the suite would fail.
 */
class BusinessEntityRepositoryTest extends TestCase
{
    public function testFindByIdRestrictsToTheGivenShopsWhenScoped(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->findById(5, [1, 2]));

        $this->assertStringContainsString('be.idShop IN (:shopIds)', $dql);
        $this->assertStringContainsString('be.id = :businessEntityId', $dql);
        $this->assertStringContainsString('be.deleted = false', $dql);
    }

    public function testFindByIdDoesNotRestrictShopsInAllShopContext(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->findById(5, null));

        $this->assertStringNotContainsString('idShop', $dql);
        $this->assertStringContainsString('be.id = :businessEntityId', $dql);
    }

    public function testGetPendingCountRestrictsToTheGivenShopsWhenScoped(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->getPendingCount([1, 2]));

        $this->assertStringContainsString('be.idShop IN (:shopIds)', $dql);
        $this->assertStringContainsString('COUNT(be.id)', $dql);
        $this->assertStringContainsString('be.status = :status', $dql);
    }

    public function testGetPendingCountDoesNotRestrictShopsInAllShopContext(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->getPendingCount(null));

        $this->assertStringNotContainsString('idShop', $dql);
        $this->assertStringContainsString('COUNT(be.id)', $dql);
    }

    /**
     * Runs a repository method against an entity manager that records the DQL it is asked to
     * execute, and returns that DQL. No database and no metadata driver are involved: DQL is pure
     * string assembly.
     */
    private function captureDql(callable $call): string
    {
        $capturedDql = '';

        $query = $this->createMock(Query::class);
        $query->method('setParameters')->willReturnSelf();
        $query->method('setFirstResult')->willReturnSelf();
        $query->method('setMaxResults')->willReturnSelf();
        $query->method('getOneOrNullResult')->willReturn(null);
        $query->method('getSingleScalarResult')->willReturn(0);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('createQueryBuilder')->willReturnCallback(
            static fn (): QueryBuilder => new QueryBuilder($entityManager)
        );
        $entityManager->method('createQuery')->willReturnCallback(
            static function (string $dql) use (&$capturedDql, $query): Query {
                $capturedDql = $dql;

                return $query;
            }
        );

        $call(new BusinessEntityRepository($entityManager, new ClassMetadata(BusinessEntity::class)));

        return $capturedDql;
    }
}
