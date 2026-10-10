<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\CspRuleQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The CSP allow-list grid (curated csp_rule rows) is scoped to the current shop and surface and must
 * never leak another shop's rules. These cover the shop restriction (including the fail-closed branch)
 * and the generated columns, without a database.
 */
class CspRuleQueryBuilderTest extends TestCase
{
    public function testItScopesTheQueryToTheResolvedShops(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1, 2], filters: []);

        $this->assertStringContainsString('c.id_shop IN (:shopIds)', $sql);
        $this->assertStringNotContainsString('1 = 0', $sql);
    }

    public function testItFailsClosedWhenTheScopeCannotBeResolved(): void
    {
        // No resolvable shop must never fall back to every shop's allow-list.
        $sql = $this->countSql(resolvedShopIds: [], filters: []);

        $this->assertStringContainsString('1 = 0', $sql);
        $this->assertStringNotContainsString('c.id_shop IN', $sql);
    }

    public function testATextFilterMatchesTheColumnWithLike(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['source' => 'cdn']);

        $this->assertStringContainsString('c.source LIKE :source', $sql);
    }

    public function testTheSearchQueryExposesTheShopNameAndWeakeningFlag(): void
    {
        $sql = $this->searchSql(resolvedShopIds: [1]);

        $this->assertStringContainsString('AS shop_name', $sql);
        $this->assertStringContainsString('ps_shop', $sql);
        $this->assertStringContainsString('AS is_weakening', $sql);
    }

    /**
     * @param list<int> $resolvedShopIds
     * @param array<string, mixed> $filters
     */
    private function countSql(array $resolvedShopIds, array $filters): string
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('createQueryBuilder')->willReturnCallback(fn (): QueryBuilder => new QueryBuilder($connection));
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());

        $shopResolver = $this->createMock(ShopListResolverInterface::class);
        $shopResolver->method('resolveShopIds')->willReturn($resolvedShopIds);

        $searchCriteria = $this->createMock(ShopSearchCriteriaInterface::class);
        $searchCriteria->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));
        $searchCriteria->method('getFilters')->willReturn($filters);

        $queryBuilder = new CspRuleQueryBuilder(
            $connection,
            'ps_',
            $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class),
            $shopResolver,
            $this->frontRequestStack()
        );

        return $queryBuilder->getCountQueryBuilder($searchCriteria)->getSQL();
    }

    /**
     * @param list<int> $resolvedShopIds
     */
    private function searchSql(array $resolvedShopIds): string
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('createQueryBuilder')->willReturnCallback(fn (): QueryBuilder => new QueryBuilder($connection));
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());

        $shopResolver = $this->createMock(ShopListResolverInterface::class);
        $shopResolver->method('resolveShopIds')->willReturn($resolvedShopIds);

        $searchCriteria = $this->createMock(ShopSearchCriteriaInterface::class);
        $searchCriteria->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));
        $searchCriteria->method('getFilters')->willReturn([]);

        // The search query runs the sorting/pagination applicator; return it from each call so the
        // fluent chain in getSearchQueryBuilder resolves.
        $applicator = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $applicator->method('applySorting')->willReturnSelf();
        $applicator->method('applyDeterministicSorting')->willReturnSelf();
        $applicator->method('applyPagination')->willReturnSelf();

        $queryBuilder = new CspRuleQueryBuilder($connection, 'ps_', $applicator, $shopResolver, $this->frontRequestStack());

        return $queryBuilder->getSearchQueryBuilder($searchCriteria)->getSQL();
    }

    private function frontRequestStack(): RequestStack
    {
        $stack = new RequestStack();
        $stack->push(new Request());

        return $stack;
    }
}
