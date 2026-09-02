<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Shop;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolver;

/**
 * Covers the resolver's own responsibilities — explicit-id pass-through, memoization over
 * ShopRepository (which owns the actual query, covered by the integration test) and the
 * deterministic representative-shop rule — against a stubbed installation: shops 1 and 2
 * in group 1, shops 3 and 4 in group 2, PS_SHOP_DEFAULT = 1.
 */
class ShopListResolverTest extends TestCase
{
    private const SHOPS_BY_GROUP = [1 => [1, 2], 2 => [3, 4]];
    private const DEFAULT_SHOP_ID = 1;

    private int $repositoryCallCount = 0;

    public function testExplicitIdsPassThroughWithoutTouchingTheRepository(): void
    {
        $resolver = $this->buildResolver();

        $this->assertSame([3], $resolver->resolveShopIds(ShopConstraint::shop(3)));
        $this->assertSame([4, 2], $resolver->resolveShopIds(ShopCollection::shops([4, 2])));
        $this->assertSame(3, $resolver->resolveRepresentativeShopId(ShopConstraint::shop(3)));
        $this->assertSame(0, $this->repositoryCallCount);
    }

    public function testGroupAndAllShopsScopesDelegateToTheRepository(): void
    {
        $resolver = $this->buildResolver();

        $this->assertSame([1, 2], $resolver->resolveShopIds(ShopConstraint::shopGroup(1)));
        $this->assertSame([3, 4], $resolver->resolveShopIds(ShopConstraint::shopGroup(2)));
        $this->assertSame([1, 2, 3, 4], $resolver->resolveShopIds(ShopConstraint::allShops()));
    }

    public function testGroupAndAllShopsLookupsAreMemoizedPerScope(): void
    {
        $resolver = $this->buildResolver();

        $resolver->resolveShopIds(ShopConstraint::shopGroup(1));
        $resolver->resolveShopIds(ShopConstraint::shopGroup(1));
        $resolver->resolveShopIds(ShopConstraint::allShops());
        $resolver->resolveShopIds(ShopConstraint::allShops());
        // Distinct scopes never share a memo entry.
        $resolver->resolveShopIds(ShopConstraint::shopGroup(2));

        $this->assertSame(3, $this->repositoryCallCount);
    }

    public function testRepresentativeShopIsTheDefaultShopWhenInScope(): void
    {
        $resolver = $this->buildResolver();

        $this->assertSame(self::DEFAULT_SHOP_ID, $resolver->resolveRepresentativeShopId(ShopConstraint::allShops()));
        $this->assertSame(self::DEFAULT_SHOP_ID, $resolver->resolveRepresentativeShopId(ShopConstraint::shopGroup(1)));
        $this->assertSame(self::DEFAULT_SHOP_ID, $resolver->resolveRepresentativeShopId(ShopCollection::shops([2, 1])));
    }

    public function testRepresentativeShopFallsBackToTheLowestShopIdOfTheScope(): void
    {
        $resolver = $this->buildResolver();

        // The default shop (1) belongs to group 1, not group 2.
        $this->assertSame(3, $resolver->resolveRepresentativeShopId(ShopConstraint::shopGroup(2)));
        $this->assertSame(2, $resolver->resolveRepresentativeShopId(ShopCollection::shops([4, 2])));
    }

    public function testEmptyScopeYieldsZeroRepresentative(): void
    {
        $resolver = $this->buildResolver();

        $this->assertSame([], $resolver->resolveShopIds(ShopConstraint::shopGroup(99)));
        $this->assertSame(0, $resolver->resolveRepresentativeShopId(ShopConstraint::shopGroup(99)));
    }

    private function buildResolver(): ShopListResolver
    {
        $this->repositoryCallCount = 0;

        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn (): QueryBuilder => new QueryBuilder($connection)
        );
        $connection->method('fetchOne')->willReturn((string) self::DEFAULT_SHOP_ID);

        $shopRepository = $this->createMock(ShopRepository::class);
        $shopRepository->method('getAssociatedShopIds')->willReturnCallback(
            function (ShopConstraint $shopConstraint): array {
                ++$this->repositoryCallCount;
                $groupId = $shopConstraint->getShopGroupId()?->getValue();

                return null !== $groupId
                    ? (self::SHOPS_BY_GROUP[$groupId] ?? [])
                    : array_merge(...array_values(self::SHOPS_BY_GROUP));
            }
        );

        return new ShopListResolver($connection, 'ps_', $shopRepository);
    }
}
