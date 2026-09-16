<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentStatus;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\ShipmentQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ShipmentFilters;

class ShipmentQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';
    private const CONTEXT_SHOP_IDS = [1, 3];

    public function testItAlwaysExcludesDeletedShipmentsAndScopesOnTheContextShops(): void
    {
        $qb = $this->buildSearchQuery([]);

        $this->assertStringContainsString('s.`deleted` = 0', (string) $qb->getQueryPart('where'));
        $this->assertStringContainsString('o.`id_shop` IN (:context_shop_ids)', (string) $qb->getQueryPart('where'));
        $this->assertSame(self::CONTEXT_SHOP_IDS, $qb->getParameter('context_shop_ids'));
    }

    public function testTheCountQueryIsScopedTheSameWayAsTheSearchQuery(): void
    {
        $filters = ['id_shipment' => 42];

        $this->assertEquals(
            (string) $this->buildSearchQuery($filters)->getQueryPart('where'),
            (string) $this->buildCountQuery($filters)->getQueryPart('where')
        );
    }

    /**
     * @dataProvider provideFilters
     */
    public function testItTranslatesFiltersIntoConditions(array $filters, string $expectedCondition, array $expectedParameters): void
    {
        $qb = $this->buildSearchQuery($filters);

        $this->assertStringContainsString($expectedCondition, (string) $qb->getQueryPart('where'));

        foreach ($expectedParameters as $name => $value) {
            $this->assertSame($value, $qb->getParameter($name), $name);
        }
    }

    public static function provideFilters(): iterable
    {
        yield 'shipment id is matched exactly' => [
            ['id_shipment' => 42],
            's.`id_shipment` = :id_shipment',
            ['id_shipment' => 42],
        ];

        yield 'carrier is matched on its name, not its id' => [
            ['carrier' => 'My carrier'],
            'c.`name` = :carrier',
            ['carrier' => 'My carrier'],
        ];

        yield 'order reference is searched partially' => [
            ['order_reference' => 'ABC'],
            'o.`reference` LIKE :order_reference',
            ['order_reference' => '%ABC%'],
        ];

        yield 'tracking number is searched partially' => [
            ['tracking_number' => '123'],
            's.`tracking_number` LIKE :tracking_number',
            ['tracking_number' => '%123%'],
        ];

        yield 'customer is searched on both names at once' => [
            ['customer' => 'Doe'],
            'CONCAT(cu.`firstname`, \' \', cu.`lastname`) LIKE :customer',
            ['customer' => '%Doe%'],
        ];

        yield 'status is compared against the very expression it is derived from' => [
            ['status' => ShipmentStatus::PENDING->value],
            ShipmentStatus::getSqlExpression() . ' = :status',
            ['status' => 'pending'],
        ];

        yield 'a date range covers both bounds entirely' => [
            ['date_add' => ['from' => '2026-01-01', 'to' => '2026-01-31']],
            's.`date_add` >= :date_add_from',
            ['date_add_from' => '2026-01-01 0:0:0', 'date_add_to' => '2026-01-31 23:59:59'],
        ];
    }

    public function testAnEmptyDateBoundIsIgnored(): void
    {
        $qb = $this->buildSearchQuery(['date_add' => ['from' => '2026-01-01', 'to' => '']]);

        $this->assertStringContainsString('s.`date_add` >= :date_add_from', (string) $qb->getQueryPart('where'));
        $this->assertStringNotContainsString('date_add_to', (string) $qb->getQueryPart('where'));
    }

    public function testItSortsOnTheRequestedColumnAndKeepsPagingDeterministic(): void
    {
        $qb = $this->buildSearchQuery([], ['orderBy' => 'carrier', 'sortOrder' => 'asc']);

        $this->assertSame(['`carrier` asc', 's.`id_shipment` asc'], $qb->getQueryPart('orderBy'));
    }

    public function testItFallsBackToTheDefaultOrderingWhenTheSavedColumnNoLongerExists(): void
    {
        // 'shipment_number' is what the per-order grid used to store under this very filter id.
        $qb = $this->buildSearchQuery([], ['orderBy' => 'shipment_number', 'sortOrder' => 'asc']);

        $this->assertSame(['`date_add` desc', 's.`id_shipment` desc'], $qb->getQueryPart('orderBy'));
    }

    public function testItDoesNotSortTwiceOnTheIdentifier(): void
    {
        $qb = $this->buildSearchQuery([], ['orderBy' => 'id_shipment', 'sortOrder' => 'desc']);

        $this->assertSame(['`id_shipment` desc'], $qb->getQueryPart('orderBy'));
    }

    public function testUnknownFiltersAreIgnored(): void
    {
        $qb = $this->buildSearchQuery(['whatever' => 'anything']);

        $this->assertStringNotContainsString('whatever', (string) $qb->getQueryPart('where'));
    }

    public function testItEscapesPercentSignsInPartialSearches(): void
    {
        $qb = $this->buildSearchQuery(['tracking_number' => '50%']);

        $this->assertSame('%50\%%', $qb->getParameter('tracking_number'));
    }

    private function buildSearchQuery(array $filters, array $sorting = []): QueryBuilder
    {
        return $this->getQueryBuilder()->getSearchQueryBuilder($this->getFilters($filters, $sorting));
    }

    private function buildCountQuery(array $filters): QueryBuilder
    {
        return $this->getQueryBuilder()->getCountQueryBuilder($this->getFilters($filters));
    }

    private function getQueryBuilder(): ShipmentQueryBuilder
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn (): QueryBuilder => $this->createPartialMock(QueryBuilder::class, [])
        );

        $applicator = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $applicator->method('applyPagination')->willReturnSelf();
        $applicator->method('applySorting')->willReturnSelf();
        $applicator->method('applyDeterministicSorting')->willReturnSelf();

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getAssociatedShopIds')->willReturn(self::CONTEXT_SHOP_IDS);

        return new ShipmentQueryBuilder($connection, self::DB_PREFIX, $applicator, $shopContext);
    }

    private function getFilters(array $filters, array $sorting = []): ShipmentFilters
    {
        return new ShipmentFilters(['filters' => $filters] + $sorting + ShipmentFilters::getDefaults());
    }
}
