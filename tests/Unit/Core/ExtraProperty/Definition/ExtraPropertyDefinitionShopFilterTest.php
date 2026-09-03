<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilter;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Covers the definition-level shop availability rules on all three layers against a stubbed
 * 4-shop installation (shops 1+2 in group 1, shops 3+4 in group 2):
 *  - the pure VO predicate ExtraPropertyDefinition::isAvailableForShops(),
 *  - the pure collection filter ExtraPropertyDefinitionCollection::filterByShops(),
 *  - the ExtraPropertyDefinitionShopFilter service: multistore-not-used short-circuit
 *    (feature flag off, or a single shop), constraint resolution, module fallback
 *    (including the degenerate "module with no shop rows at all" rule) and the per-shop
 *    caching of the module→shops association.
 */
class ExtraPropertyDefinitionShopFilterTest extends TestCase
{
    private const SHOPS_BY_GROUP = [1 => [1, 2], 2 => [3, 4]];

    /**
     * Module enablement of the stubbed installation: demo_a on shops 1+3, demo_b on shop 2.
     * demo_orphan has no ps_module_shop row at all (degenerate case).
     */
    private const MODULES_BY_SHOP = [1 => ['demo_a'], 2 => ['demo_b'], 3 => ['demo_a'], 4 => []];

    private bool $multiShopActive = true;

    private int $shopCount = 4;

    private int $moduleQueryCount = 0;

    protected function setUp(): void
    {
        $this->multiShopActive = true;
        $this->shopCount = 4;
        $this->moduleQueryCount = 0;
    }

    // -------------------------------------------------------------------------
    // VO predicate
    // -------------------------------------------------------------------------

    public function testExplicitRestrictionIntersectsTheScope(): void
    {
        $definition = $this->definition('restricted', shopIds: [2, 3]);

        $this->assertTrue($definition->isAvailableForShops([1, 2]));
        $this->assertTrue($definition->isAvailableForShops([3]));
        $this->assertFalse($definition->isAvailableForShops([1, 4]));
        // Explicit restriction wins over any module fallback input.
        $this->assertFalse($definition->isAvailableForShops([1], [1]));
    }

    public function testCoreOwnedWithoutRestrictionIsAvailableEverywhere(): void
    {
        $definition = $this->definition('open');

        $this->assertTrue($definition->isAvailableForShops([4]));
        $this->assertTrue($definition->isAvailableForShops([]));
    }

    public function testModuleOwnedWithoutRestrictionFollowsTheModuleShops(): void
    {
        $definition = $this->definition('module_prop', moduleName: 'demo_a');

        // null module shops = unknown / module has no shop rows at all → unrestricted.
        $this->assertTrue($definition->isAvailableForShops([4], null));
        // Empty list = the module is enabled on other shops only → excluded.
        $this->assertFalse($definition->isAvailableForShops([4], []));
        $this->assertTrue($definition->isAvailableForShops([1, 4], [1]));
    }

    public function testEmptyListIsTheWriteTimeClearMarkerAndDoesNotRestrict(): void
    {
        // [] is preserved (save() reads it as "clear the stored association") but is
        // equivalent to the no-restriction state it reverts to for availability.
        $definition = $this->definition('cleared', shopIds: []);

        $this->assertSame([], $definition->getAssociatedShopIds());
        $this->assertTrue($definition->isAvailableForShops([4]));
    }

    // -------------------------------------------------------------------------
    // Collection filter
    // -------------------------------------------------------------------------

    public function testFilterByShopsKeepsOnlyAvailableDefinitions(): void
    {
        $collection = new ExtraPropertyDefinitionCollection([
            $this->definition('open'),
            $this->definition('restricted', shopIds: [3]),
            $this->definition('module_prop', moduleName: 'demo_a'),
            $this->definition('orphan_prop', moduleName: 'demo_orphan'),
        ]);

        // demo_a is mapped to shop 2's scope subset ([]), demo_orphan has no map entry (unrestricted).
        $filtered = $collection->filterByShops([2], ['demo_a' => []]);

        $this->assertSame(
            ['open', 'orphan_prop'],
            array_map(static fn (ExtraPropertyDefinition $d): string => $d->getPropertyName(), iterator_to_array($filtered))
        );
    }

    // -------------------------------------------------------------------------
    // Service
    // -------------------------------------------------------------------------

    public function testMultistoreOffShortCircuits(): void
    {
        $this->multiShopActive = false;
        $filter = $this->buildFilter();
        $collection = new ExtraPropertyDefinitionCollection([
            $this->definition('restricted', shopIds: [3]),
        ]);

        // Stale association rows are ignored entirely when the feature is off.
        $this->assertSame($collection, $filter->filterByShopConstraint($collection, ShopConstraint::shop(1)));
        $this->assertSame([1], $filter->getAvailableShopIds($this->definition('restricted', shopIds: [3]), [1]));
        $this->assertSame(0, $this->moduleQueryCount);
    }

    public function testActiveButSingleShopShortCircuitsToo(): void
    {
        // Multistore is not USED with a single shop (MultistoreFeature::isUsed() semantics,
        // the criterion shared by every extra-property multistore gate): a restriction can
        // never usefully exclude anything, and the UI to manage it is not rendered.
        $this->shopCount = 1;
        $filter = $this->buildFilter();
        $collection = new ExtraPropertyDefinitionCollection([
            $this->definition('restricted', shopIds: [3]),
        ]);

        $this->assertSame($collection, $filter->filterByShopConstraint($collection, ShopConstraint::shop(1)));
        $this->assertSame([1], $filter->getAvailableShopIds($this->definition('restricted', shopIds: [3]), [1]));
        $this->assertSame(0, $this->moduleQueryCount);
    }

    public function testFilterByShopConstraintAppliesTheAvailabilityRules(): void
    {
        $filter = $this->buildFilter();
        $collection = new ExtraPropertyDefinitionCollection([
            $this->definition('open'),
            $this->definition('restricted', shopIds: [3]),
            $this->definition('module_a_prop', moduleName: 'demo_a'),
            $this->definition('module_b_prop', moduleName: 'demo_b'),
            $this->definition('orphan_prop', moduleName: 'demo_orphan'),
        ]);

        $names = fn (ExtraPropertyDefinitionCollection $c): array => array_map(
            static fn (ExtraPropertyDefinition $d): string => $d->getPropertyName(),
            iterator_to_array($c)
        );

        // Shop 1: demo_a enabled there, demo_b is not, restriction targets shop 3 only.
        $this->assertSame(
            ['open', 'module_a_prop', 'orphan_prop'],
            $names($filter->filterByShopConstraint($collection, ShopConstraint::shop(1)))
        );
        // Group 2 (shops 3+4): the restriction matches shop 3, demo_a is enabled on shop 3.
        $this->assertSame(
            ['open', 'restricted', 'module_a_prop', 'orphan_prop'],
            $names($filter->filterByShopConstraint($collection, ShopConstraint::shopGroup(2)))
        );
        // Shop 4: nothing module-specific is enabled there.
        $this->assertSame(
            ['open', 'orphan_prop'],
            $names($filter->filterByShopConstraint($collection, ShopConstraint::shop(4)))
        );
    }

    public function testGetAvailableShopIdsIntersectsPerDefinition(): void
    {
        $filter = $this->buildFilter();

        $this->assertSame([3], $filter->getAvailableShopIds($this->definition('restricted', shopIds: [3]), [3, 4]));
        $this->assertSame([3, 4], $filter->getAvailableShopIds($this->definition('open'), [3, 4]));
        $this->assertSame([3], $filter->getAvailableShopIds($this->definition('p', moduleName: 'demo_a'), [3, 4]));
        $this->assertSame([], $filter->getAvailableShopIds($this->definition('p', moduleName: 'demo_b'), [3, 4]));
        // Degenerate rule: no ps_module_shop row at all → unrestricted.
        $this->assertSame([3, 4], $filter->getAvailableShopIds($this->definition('p', moduleName: 'demo_orphan'), [3, 4]));
    }

    public function testModuleAssociationLookupsAreCachedPerShop(): void
    {
        $filter = $this->buildFilter();
        $collection = new ExtraPropertyDefinitionCollection([
            $this->definition('module_a_prop', moduleName: 'demo_a'),
        ]);

        $filter->filterByShopConstraint($collection, ShopConstraint::shopGroup(1));
        $filter->filterByShopConstraint($collection, ShopConstraint::shopGroup(1));
        $filter->filterByShopConstraint($collection, ShopConstraint::shop(1));

        // 1 "modules with any association" query + one per distinct shop of the scopes (1 and 2).
        $this->assertSame(3, $this->moduleQueryCount);
    }

    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    /**
     * @param list<int>|null $shopIds
     */
    private function definition(string $propertyName, ?string $moduleName = null, ?array $shopIds = null): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            moduleName: $moduleName,
            associatedShopIds: $shopIds,
        );
    }

    private function buildFilter(): ExtraPropertyDefinitionShopFilter
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn (): QueryBuilder => new QueryBuilder($connection)
        );
        // Module association and shop count lookups (the QueryBuilder fetch helpers execute
        // through Connection::executeQuery()).
        $connection->method('executeQuery')->willReturnCallback(
            function (string $sql, array $params = []): Result {
                $result = $this->createMock(Result::class);

                // isMultiShopUsed() shop count — not a module association lookup.
                if (str_contains($sql, 'COUNT(')) {
                    $result->method('fetchOne')->willReturn($this->shopCount);

                    return $result;
                }

                ++$this->moduleQueryCount;
                $rows = str_contains($sql, 'DISTINCT')
                    ? array_values(array_unique(array_merge(...array_values(self::MODULES_BY_SHOP))))
                    : self::MODULES_BY_SHOP[(int) ($params['shopId'] ?? 0)] ?? [];
                $result->method('fetchFirstColumn')->willReturn($rows);

                return $result;
            }
        );

        $shopListResolver = $this->createMock(ShopListResolverInterface::class);
        $shopListResolver->method('resolveShopIds')->willReturnCallback(
            static function (ShopConstraint $shopConstraint): array {
                if (null !== $shopConstraint->getShopId()) {
                    return [$shopConstraint->getShopId()->getValue()];
                }
                $groupId = $shopConstraint->getShopGroupId()?->getValue();

                return null !== $groupId
                    ? (self::SHOPS_BY_GROUP[$groupId] ?? [])
                    : array_merge(...array_values(self::SHOPS_BY_GROUP));
            }
        );

        // PS_MULTISHOP_FEATURE_ACTIVE global read (the only configuration value the filter needs).
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (): bool => $this->multiShopActive);

        return new ExtraPropertyDefinitionShopFilter($connection, 'ps_', $shopListResolver, new ArrayAdapter(), $configuration);
    }
}
