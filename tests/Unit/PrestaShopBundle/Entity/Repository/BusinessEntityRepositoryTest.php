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
    /**
     * @var array<string, mixed>
     */
    private array $capturedParameters = [];

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

    public function testFindByIdsRestrictsToTheGivenShopsWhenScoped(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->findByIds([5, 9], [1, 2]));

        $this->assertStringContainsString('be.idShop IN (:shopIds)', $dql);
        $this->assertStringContainsString('be.id IN (:businessEntityIds)', $dql);
        $this->assertStringContainsString(
            'be.deleted = false',
            $dql,
            'the scoped branch is the one a single-shop back office takes, and it was the only one left unasserted'
        );
        $this->assertStringNotContainsString(
            ' OR ',
            $dql,
            'the three clauses must be conjunctive: one orWhere would make the whole filter dead and expose every row'
        );
        $this->assertSame([5, 9], $this->capturedParameters['businessEntityIds'] ?? null);
        $this->assertSame([1, 2], $this->capturedParameters['shopIds'] ?? null, 'a clause without its bound parameter throws at runtime');
    }

    public function testFindByIdsDoesNotRestrictShopsInAllShopContext(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->findByIds([5, 9], null));

        $this->assertStringNotContainsString('idShop', $dql);
        $this->assertStringContainsString('be.id IN (:businessEntityIds)', $dql);
    }

    public function testFindByIdsNeverReturnsAnAlreadyDeletedEntity(): void
    {
        $dql = $this->captureDql(static fn (BusinessEntityRepository $repository) => $repository->findByIds([5, 9], null));

        $this->assertStringContainsString(
            'be.deleted = false',
            $dql,
            'without this guard a bulk delete would mark an already deleted entity again, log it twice, and report a success'
        );
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
        $this->capturedParameters = [];

        $query = $this->createMock(Query::class);
        $query->method('setParameters')->willReturnCallback(
            function (iterable $parameters) use ($query): Query {
                foreach ($parameters as $parameter) {
                    $this->capturedParameters[$parameter->getName()] = $parameter->getValue();
                }

                return $query;
            }
        );
        $query->method('setFirstResult')->willReturnSelf();
        $query->method('setMaxResults')->willReturnSelf();
        $query->method('getOneOrNullResult')->willReturn(null);
        $query->method('getSingleScalarResult')->willReturn(0);
        $query->method('getResult')->willReturn([]);

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
