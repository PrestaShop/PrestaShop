<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Query\BusinessEntityQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\BusinessEntityFilters;

class BusinessEntityQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    private function getMockConnection(): Connection
    {
        $mock = $this->createMock(Connection::class);
        $mock->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $mock->method('createQueryBuilder')->willReturnCallback(fn () => new QueryBuilder($mock));

        return $mock;
    }

    private function getMockApplicator(): DoctrineSearchCriteriaApplicatorInterface
    {
        $mock = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $mock->method('applyPagination')->willReturnSelf();
        $mock->method('applySorting')->willReturnSelf();
        $mock->method('applyDeterministicSorting')->willReturnSelf();

        return $mock;
    }

    private function getMockShopContext(bool $isAllShop, array $associatedShopIds = []): ShopContext
    {
        $mock = $this->createMock(ShopContext::class);
        $mock->method('isAllShopContext')->willReturn($isAllShop);
        $mock->method('getAssociatedShopIds')->willReturn($associatedShopIds);

        return $mock;
    }

    private function createQueryBuilder(ShopContext $shopContext): BusinessEntityQueryBuilder
    {
        return new BusinessEntityQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockApplicator(),
            $shopContext
        );
    }

    private function getFilters(array $extraFilters = [], ?string $orderBy = null, ?string $orderWay = null): BusinessEntityFilters
    {
        $filters = new BusinessEntityFilters(BusinessEntityFilters::getDefaults());
        if ([] !== $extraFilters) {
            $filters->addFilter($extraFilters);
        }
        if (null !== $orderBy) {
            $filters->set('orderBy', $orderBy);
        }
        if (null !== $orderWay) {
            $filters->set('sortOrder', $orderWay);
        }

        return $filters;
    }

    public function testSearchQuerySelectsAllGridColumnFields(): void
    {
        // The SELECT aliases must stay aligned with the grid column `field` ids or the cells render
        // blank. status_label is intentionally NOT selected here — it is produced downstream by
        // BusinessEntityGridDataFactory (translated), so it must be absent from the raw SQL.
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters());

        $select = implode(' | ', $qb->getQueryPart('select'));
        $this->assertStringContainsString('be.id_business_entity', $select);
        $this->assertStringContainsString('be.name', $select);
        $this->assertStringContainsString('be.legal_name', $select);
        $this->assertStringContainsString('be.status', $select);
        $this->assertStringContainsString('s.name AS shop_name', $select);
        $this->assertStringContainsString('COUNT(DISTINCT becb.id_customer_b2b) AS customers_count', $select);
        $this->assertStringNotContainsString('status_label', $select);
        // Presentation constants must never be injected in the SELECT: they would leak into
        // "Show SQL query" and "Export to SQL Manager".
        $this->assertStringNotContainsString('badge', $select);
    }

    public function testSearchQueryAlwaysExcludesDeletedAndGroupsByEntity(): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters());

        $where = (string) $qb->getQueryPart('where');
        $this->assertStringContainsString('be.deleted = 0', $where);
        $this->assertSame(['be.id_business_entity'], $qb->getQueryPart('groupBy'));
    }

    public function testSearchQueryIsNotShopScopedInAllShopContext(): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters());

        $this->assertStringNotContainsString('id_shop IN', (string) $qb->getQueryPart('where'));
        $this->assertArrayNotHasKey('beShopIds', $qb->getParameters());
    }

    public function testSearchQueryIsShopScopedOutsideAllShopContext(): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(false, [1, 2]))
            ->getSearchQueryBuilder($this->getFilters());

        $this->assertStringContainsString('be.id_shop IN (:beShopIds)', (string) $qb->getQueryPart('where'));
        $this->assertSame([1, 2], $qb->getParameter('beShopIds'));
        // Without the array binding type the shop ids are bound as a single scalar and the IN ()
        // silently matches nothing: the listing comes back empty on every non-all-shop context.
        $this->assertSame(Connection::PARAM_INT_ARRAY, $qb->getParameterTypes()['beShopIds']);
    }

    /**
     * @dataProvider provideTextFilters
     */
    public function testTextFiltersAreParameterizedWithLike(string $filterName, string $value, string $expectedClause): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters([$filterName => $value]));

        $this->assertStringContainsString($expectedClause, (string) $qb->getQueryPart('where'));
        $this->assertSame('%' . $value . '%', $qb->getParameter($filterName));
    }

    public function provideTextFilters(): iterable
    {
        yield 'name' => ['name', 'Acme', 'be.name LIKE :name'];
        yield 'legal_name' => ['legal_name', 'Acme Ltd', 'be.legal_name LIKE :legal_name'];
        yield 'shop_name' => ['shop_name', 'Main shop', 's.name LIKE :shop_name'];
    }

    public function testIdFilterIsCastToInt(): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters(['id_business_entity' => '7']));

        $this->assertStringContainsString('be.id_business_entity = :id_business_entity', (string) $qb->getQueryPart('where'));
        $this->assertSame(7, $qb->getParameter('id_business_entity'));
    }

    public function testStatusFilterIsExactMatch(): void
    {
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters(['status' => 'pending']));

        $this->assertStringContainsString('be.status = :status', (string) $qb->getQueryPart('where'));
        $this->assertSame('pending', $qb->getParameter('status'));
    }

    /**
     * @dataProvider provideSortableColumns
     */
    public function testSortingAppliesWhitelistedColumnInBothDirections(string $orderBy, string $expectedColumn): void
    {
        // The direction has to be asserted too: an ORDER BY frozen on ASC would satisfy a test that
        // only looks at the column, while breaking AC4 for every descending click.
        foreach (['asc', 'desc'] as $orderWay) {
            $qb = $this->createQueryBuilder($this->getMockShopContext(true))
                ->getSearchQueryBuilder($this->getFilters([], $orderBy, $orderWay));

            $this->assertSame([$expectedColumn . ' ' . $orderWay], $qb->getQueryPart('orderBy'));
        }
    }

    public function provideSortableColumns(): iterable
    {
        yield 'id' => ['id_business_entity', 'be.id_business_entity'];
        yield 'name' => ['name', 'be.`name`'];
        yield 'legal_name' => ['legal_name', 'be.`legal_name`'];
        yield 'status' => ['status', 'be.`status`'];
        yield 'shop_name' => ['shop_name', 's.`name`'];
        yield 'customers_count aggregate alias' => ['customers_count', 'customers_count'];
    }

    public function testSortingIgnoresNonWhitelistedColumn(): void
    {
        // A syntactically valid column that is not in the whitelist (e.g. a column the grid does
        // not expose, or an injection attempt sneaking a real column name) must never reach ORDER BY.
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters([], 'created_at'));

        $this->assertEmpty($qb->getQueryPart('orderBy'));
    }

    /**
     * @dataProvider providePercentFilters
     */
    public function testTextFiltersEscapePercentInLikeBinding(string $filterName, string $value, string $expectedParameter): void
    {
        // '%' is escaped to '\%' before being bound, so it is treated as a literal character rather
        // than a LIKE wildcard. '_' and '\' are NOT escaped — that is the documented behaviour of
        // AbstractDoctrineQueryBuilder::escapePercent(), shared with the rest of the core.
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters([$filterName => $value]));

        $this->assertSame($expectedParameter, $qb->getParameter($filterName));
    }

    public function providePercentFilters(): iterable
    {
        yield 'name with percent' => ['name', 'da%ta', '%da\%ta%'];
        yield 'legal_name with percent' => ['legal_name', 'da%ta', '%da\%ta%'];
        yield 'shop_name with percent' => ['shop_name', 'da%ta', '%da\%ta%'];
    }

    /**
     * Pins the whole shape of the search query, not just fragments of it: a stray andWhere(), a lost
     * join or an extra bound parameter cannot slip through an assertEquals on every query part.
     *
     * @dataProvider dataProviderQueryBuilder
     *
     * @param array<string, mixed> $filters
     * @param int[] $shopIds
     * @param string[] $expectedWhereParts
     * @param array<string, mixed> $expectedParameters
     */
    public function testQueryBuild(
        array $filters,
        bool $isAllShopContext,
        array $shopIds,
        array $expectedWhereParts,
        array $expectedParameters
    ): void {
        $qb = $this->createQueryBuilder($this->getMockShopContext($isAllShopContext, $shopIds))
            ->getSearchQueryBuilder($this->getFilters($filters));

        $this->assertEquals($this->expectedSearchQueryParts($expectedWhereParts), $qb->getQueryParts());
        $this->assertEquals($expectedParameters, $qb->getParameters());
    }

    /**
     * Same fixtures as testQueryBuild: the count query must filter identically, and never group or
     * sort, or the AC8 total drifts from the listed rows.
     *
     * @dataProvider dataProviderQueryBuilder
     *
     * @param array<string, mixed> $filters
     * @param int[] $shopIds
     * @param string[] $expectedWhereParts
     * @param array<string, mixed> $expectedParameters
     */
    public function testCountQueryBuild(
        array $filters,
        bool $isAllShopContext,
        array $shopIds,
        array $expectedWhereParts,
        array $expectedParameters
    ): void {
        $qb = $this->createQueryBuilder($this->getMockShopContext($isAllShopContext, $shopIds))
            ->getCountQueryBuilder($this->getFilters($filters));

        $this->assertEquals($this->expectedCountQueryParts($expectedWhereParts), $qb->getQueryParts());
        $this->assertEquals($expectedParameters, $qb->getParameters());
    }

    public function dataProviderQueryBuilder(): iterable
    {
        yield 'no filter in all shop context' => [
            [],
            true,
            [],
            ['be.deleted = 0'],
            [],
        ];

        yield 'shop scoped, no filter' => [
            [],
            false,
            [1, 2],
            ['be.deleted = 0', 'be.id_shop IN (:beShopIds)'],
            ['beShopIds' => [1, 2]],
        ];

        yield 'several filters compose in a single AND' => [
            ['name' => 'acme', 'status' => 'pending'],
            false,
            [1, 2],
            [
                'be.deleted = 0',
                'be.id_shop IN (:beShopIds)',
                'be.name LIKE :name',
                'be.status = :status',
            ],
            ['beShopIds' => [1, 2], 'name' => '%acme%', 'status' => 'pending'],
        ];

        yield 'id filter is an exact match' => [
            ['id_business_entity' => '7'],
            true,
            [],
            ['be.deleted = 0', 'be.id_business_entity = :id_business_entity'],
            ['id_business_entity' => 7],
        ];

        // Submitting the filter row with the status placeholder on "All" sends status => '', and the
        // untouched text inputs come back empty too. Without the blank guard those would be bound as
        // empty values and the listing would come back empty on the most common interaction.
        yield 'blank filter values are ignored' => [
            ['name' => '', 'legal_name' => null, 'shop_name' => '', 'status' => '', 'id_business_entity' => ''],
            true,
            [],
            ['be.deleted = 0'],
            [],
        ];
    }

    /**
     * @param string[] $whereParts
     *
     * @return array<string, mixed>
     */
    private function expectedSearchQueryParts(array $whereParts): array
    {
        $parts = $this->expectedBaseQueryParts($whereParts);
        // The aggregate join belongs to the search query only: the count query must not pay for it.
        $parts['join']['be'][] = [
            'joinType' => 'left',
            'joinTable' => self::DB_PREFIX . 'business_entity_customer_b2b',
            'joinAlias' => 'becb',
            'joinCondition' => 'becb.id_business_entity = be.id_business_entity',
        ];

        return array_merge($parts, [
            'select' => [
                'be.id_business_entity',
                'be.name',
                'be.legal_name',
                'be.status',
                's.name AS shop_name',
                'COUNT(DISTINCT becb.id_customer_b2b) AS customers_count',
            ],
            'groupBy' => ['be.id_business_entity'],
            'orderBy' => ['be.id_business_entity asc'],
        ]);
    }

    /**
     * @param string[] $whereParts
     *
     * @return array<string, mixed>
     */
    private function expectedCountQueryParts(array $whereParts): array
    {
        return array_merge($this->expectedBaseQueryParts($whereParts), [
            'select' => ['COUNT(DISTINCT be.id_business_entity)'],
            'groupBy' => [],
            'orderBy' => [],
        ]);
    }

    /**
     * @param string[] $whereParts
     *
     * @return array<string, mixed>
     */
    private function expectedBaseQueryParts(array $whereParts): array
    {
        return [
            'select' => [],
            'distinct' => false,
            'from' => [
                ['table' => self::DB_PREFIX . 'business_entity', 'alias' => 'be'],
            ],
            'join' => [
                'be' => [
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'shop',
                        'joinAlias' => 's',
                        'joinCondition' => 's.id_shop = be.id_shop',
                    ],
                ],
            ],
            'set' => [],
            'where' => CompositeExpression::and(...$whereParts),
            'groupBy' => [],
            'having' => null,
            'orderBy' => [],
            'values' => [],
            'for_update' => null,
        ];
    }

    public function testCountQueryCountsDistinctEntitiesAndIsNeverPaginated(): void
    {
        // Paginating the count query would cap the total at the page size (AC8 shows a wrong
        // number of results), so the applicator must not be called at all on that builder.
        $applicator = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $applicator->expects($this->never())->method('applyPagination');

        $queryBuilder = new BusinessEntityQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $applicator,
            $this->getMockShopContext(true)
        );

        $qb = $queryBuilder->getCountQueryBuilder($this->getFilters());

        $this->assertSame(['COUNT(DISTINCT be.id_business_entity)'], $qb->getQueryPart('select'));
    }

    public function testSearchQueryTargetsThePrefixedTablesAndJoinsTheAggregateTable(): void
    {
        // Guards the aggregate wiring: dropping the becb join or mistyping a prefixed table name
        // leaves every other assertion of this suite green while breaking the Customers column.
        $sql = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters())
            ->getSQL();

        $this->assertStringContainsString('FROM ' . self::DB_PREFIX . 'business_entity be', $sql);
        $this->assertStringContainsString('LEFT JOIN ' . self::DB_PREFIX . 'business_entity_customer_b2b becb', $sql);
        $this->assertStringContainsString('becb.id_business_entity = be.id_business_entity', $sql);
        $this->assertStringContainsString('LEFT JOIN ' . self::DB_PREFIX . 'shop s', $sql);
        $this->assertStringContainsString('GROUP BY be.id_business_entity', $sql);
    }

    public function testCountQuerySharesTheSearchFiltersButNeverGroups(): void
    {
        // count and search must stay in parity: a filter applied to only one of them desyncs the
        // pagination total from the listed rows, and a GROUP BY on the count returns one row per
        // entity instead of the total.
        $filters = $this->getFilters(['name' => 'acme', 'status' => 'pending']);
        $queryBuilder = $this->createQueryBuilder($this->getMockShopContext(true));

        $searchQb = $queryBuilder->getSearchQueryBuilder($filters);
        $countQb = $queryBuilder->getCountQueryBuilder($filters);

        $this->assertSame(
            (string) $searchQb->getQueryPart('where'),
            (string) $countQb->getQueryPart('where')
        );
        $this->assertSame($searchQb->getParameters(), $countQb->getParameters());
        $this->assertEmpty($countQb->getQueryPart('groupBy'));
        $this->assertStringContainsString(
            'FROM ' . self::DB_PREFIX . 'business_entity be',
            $countQb->getSQL()
        );
    }

    public function testSortingByCustomersCountUsesTheAggregateAlias(): void
    {
        // customers_count is a SELECT alias (aggregate) rather than a be.* column, so its ORDER BY
        // path differs from a plain column and must still be whitelisted (AC4 + AC3 Customers column).
        $qb = $this->createQueryBuilder($this->getMockShopContext(true))
            ->getSearchQueryBuilder($this->getFilters([], 'customers_count'));

        $orderBy = $qb->getQueryPart('orderBy');
        $this->assertNotEmpty($orderBy);
        $this->assertStringContainsString('customers_count', $orderBy[0]);
    }
}
