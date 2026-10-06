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
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspLogStatusFilter;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\CspLogQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The CSP log grid is scoped to the current shop and must never leak another shop's violations.
 * These cover the shop restriction (including the fail-closed branch) and the status filter on the
 * generated count query, without a database.
 */
class CspLogQueryBuilderTest extends TestCase
{
    public function testItScopesTheQueryToTheResolvedShops(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1, 2], filters: []);

        $this->assertStringContainsString('c.id_shop IN (:shopIds)', $sql);
        $this->assertStringNotContainsString('1 = 0', $sql);
    }

    public function testItFailsClosedWhenTheScopeCannotBeResolved(): void
    {
        // No resolvable shop must never fall back to every shop's log.
        $sql = $this->countSql(resolvedShopIds: [], filters: []);

        $this->assertStringContainsString('1 = 0', $sql);
        $this->assertStringNotContainsString('c.id_shop IN', $sql);
    }

    public function testTheViolationsStatusFilterKeepsOnlyRowsWithoutARule(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['status' => CspLogStatusFilter::VIOLATIONS->value]);

        $this->assertStringContainsString('r.id_csp_rule IS NULL', $sql);
    }

    public function testTheAllowedStatusFilterKeepsOnlyRowsWithARule(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['status' => CspLogStatusFilter::ALLOWED->value]);

        $this->assertStringContainsString('r.id_csp_rule IS NOT NULL', $sql);
    }

    public function testATextFilterMatchesTheColumnWithLike(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['source' => 'cdn']);

        $this->assertStringContainsString('c.source LIKE :source', $sql);
    }

    public function testTheWeakeningFlagCoversWildcardsAndBroadSchemesOnScriptStyle(): void
    {
        $sql = $this->searchSql(resolvedShopIds: [1]);

        $this->assertStringContainsString('AS is_weakening', $sql);
        // Weakening keyword/wildcard list, any wildcard host, and broad schemes on script-/style-src.
        $this->assertStringContainsString(':weakeningSources', $sql);
        $this->assertStringContainsString("c.source LIKE '%*%'", $sql);
        $this->assertStringContainsString(':scriptStyleDirectives', $sql);
        $this->assertStringContainsString(':broadeningSchemes', $sql);
    }

    public function testTheCountQuerySkipsTheAllowListJoinWithoutAStatusFilter(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['source' => 'cdn']);

        $this->assertStringNotContainsString('ps_csp_rule', $sql);
    }

    public function testTheCountQueryJoinsTheAllowListOnlyForAStatusFilter(): void
    {
        $sql = $this->countSql(resolvedShopIds: [1], filters: ['status' => CspLogStatusFilter::ALLOWED->value]);

        $this->assertStringContainsString('ps_csp_rule', $sql);
    }

    public function testTheSearchQueryExposesTheShopNameForMultistoreViews(): void
    {
        $sql = $this->searchSql(resolvedShopIds: [1]);

        $this->assertStringContainsString('AS shop_name', $sql);
        $this->assertStringContainsString('ps_shop', $sql);
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

        $queryBuilder = new CspLogQueryBuilder(
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

        $queryBuilder = new CspLogQueryBuilder($connection, 'ps_', $applicator, $shopResolver, $this->frontRequestStack());

        return $queryBuilder->getSearchQueryBuilder($searchCriteria)->getSQL();
    }

    private function frontRequestStack(): RequestStack
    {
        $stack = new RequestStack();
        $stack->push(new Request());

        return $stack;
    }
}
