<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Definition;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Grid\Query\ExtraPropertyDefinitionQueryBuilder;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use PrestaShop\PrestaShop\Core\Search\Filters\ExtraPropertyDefinitionFilters;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Shop;
use ShopGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;

/**
 * Lockstep guard for the definition availability rule, which deliberately has several
 * implementations (a per-row query would be the alternative cost):
 *
 *  1. ExtraPropertyDefinition::isAvailableForShops() — the pure PHP predicate;
 *  2. ExtraPropertyDefinitionShopFilter — filterByShopConstraint() (forms, grids, API,
 *     reader/writer all filter through it) and getAvailableShopIds() (write fan-out and
 *     the grid data decorator's fallback resolution follow the same explicit rows →
 *     module rows → unrestricted resolution);
 *  3. ExtraPropertyDefinitionQueryBuilder::applyShopContextRestriction() — the SQL mirror
 *     feeding the registry grid.
 *
 * A single fixture matrix (core / explicitly restricted / module fallback / degenerate
 * module without any ps_module_shop row, crossed with every single-shop and group scope)
 * runs against all of them and asserts identical availability. When one implementation
 * evolves, this test fails until the others are synced — or shows the change should not
 * be made at all.
 *
 * The all-shops scope is asserted separately: the SQL restriction deliberately
 * short-circuits there (the registry is managed from the all-shops view), which happens
 * to agree with the predicate since any restriction intersects the full shop list.
 */
class ExtraPropertyDefinitionAvailabilityConsistencyTest extends KernelTestCase
{
    private const MODULE = 'extrapropconsistencymod';
    private const ORPHAN_MODULE = 'extrapropconsistencyorphan';
    private const PROPERTY_PREFIX = 'cons_';
    private const DEFAULT_SHOP_ID = 1;

    private static int $secondShopId;
    private static int $thirdShopId;
    private static int $defaultGroupId;
    private static int $secondGroupId;

    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $definitionRepository;
    private static ExtraPropertyDefinitionShopFilterInterface $shopFilter;
    private static ShopListResolverInterface $shopListResolver;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        ShopResetter::resetShops();
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);

        self::$defaultGroupId = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);

        $secondShop = new Shop();
        $secondShop->name = 'Consistency Shop 2';
        $secondShop->id_shop_group = self::$defaultGroupId;
        $secondShop->id_category = 2;
        $secondShop->active = true;
        $secondShop->save();
        self::$secondShopId = (int) $secondShop->id;

        $secondGroup = new ShopGroup();
        $secondGroup->name = 'Consistency Group 2';
        $secondGroup->save();
        self::$secondGroupId = (int) $secondGroup->id;

        $thirdShop = new Shop();
        $thirdShop->name = 'Consistency Shop 3';
        $thirdShop->id_shop_group = self::$secondGroupId;
        $thirdShop->id_category = 2;
        $thirdShop->active = true;
        $thirdShop->save();
        self::$thirdShopId = (int) $thirdShop->id;

        Shop::resetStaticCache();
        Shop::resetContext();
        Shop::setContext(Shop::CONTEXT_ALL);

        // Fake module rows are enough: the fallback only reads ps_module + ps_module_shop.
        // MODULE is enabled on shop 1 and the third shop; ORPHAN_MODULE has no row at all.
        Db::getInstance()->execute(sprintf(
            "INSERT INTO `%smodule` (`name`, `active`, `version`) VALUES ('%s', 1, '1.0.0')",
            _DB_PREFIX_,
            self::MODULE
        ));
        $moduleId = (int) Db::getInstance()->Insert_ID();
        foreach ([self::DEFAULT_SHOP_ID, self::$thirdShopId] as $shopId) {
            Db::getInstance()->execute(sprintf(
                'INSERT IGNORE INTO `%smodule_shop` (`id_module`, `id_shop`) VALUES (%d, %d)',
                _DB_PREFIX_,
                $moduleId,
                $shopId
            ));
        }
        Db::getInstance()->execute(sprintf(
            "INSERT INTO `%smodule` (`name`, `active`, `version`) VALUES ('%s', 0, '1.0.0')",
            _DB_PREFIX_,
            self::ORPHAN_MODULE
        ));

        self::initServices();

        foreach (self::definitions() as $definition) {
            self::$registry->register($definition);
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::definitions() as $definition) {
            self::$registry->unregister($definition, true);
        }
        Db::getInstance()->execute(sprintf(
            "DELETE FROM `%smodule` WHERE `name` IN ('%s', '%s')",
            _DB_PREFIX_,
            self::MODULE,
            self::ORPHAN_MODULE
        ));
        ShopResetter::resetShops();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();

        // Rebuild the per-shop module cache entries from the current DB state.
        self::getContainer()->get('prestashop.extra_property.definition.filesystem_cache')->clear();
    }

    private static function initServices(): void
    {
        $container = self::getContainer();
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$definitionRepository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
        self::$shopFilter = $container->get(ExtraPropertyDefinitionShopFilterInterface::class);
        self::$shopListResolver = $container->get(ShopListResolverInterface::class);
    }

    /**
     * The matrix: every single-shop and group scope, checked against the three
     * implementations at once.
     */
    public function testAllImplementationsAgreeOnEveryScope(): void
    {
        $scopes = [
            'shop 1' => ShopConstraint::shop(self::DEFAULT_SHOP_ID),
            'shop 2' => ShopConstraint::shop(self::$secondShopId),
            'shop 3' => ShopConstraint::shop(self::$thirdShopId),
            'default group' => ShopConstraint::shopGroup(self::$defaultGroupId),
            'second group' => ShopConstraint::shopGroup(self::$secondGroupId),
        ];

        foreach ($scopes as $label => $shopConstraint) {
            $fromPredicate = $this->availableNamesPerPredicate($shopConstraint);
            $fromFilter = $this->availableNamesPerFilterService($shopConstraint);
            $fromSql = $this->availableNamesPerGridQuery($shopConstraint);

            $this->assertSame($fromPredicate, $fromFilter, sprintf(
                '[%s] ExtraPropertyDefinitionShopFilter::filterByShopConstraint() drifted from the isAvailableForShops() predicate.',
                $label
            ));
            $this->assertSame($fromPredicate, $fromSql, sprintf(
                '[%s] ExtraPropertyDefinitionQueryBuilder::applyShopContextRestriction() drifted from the isAvailableForShops() predicate.',
                $label
            ));
        }

        // Sanity check on the fixture power: every availability case must actually
        // discriminate somewhere, otherwise the matrix proves nothing.
        $this->assertSame(
            ['cons_core_open', 'cons_module_follow', 'cons_orphan'],
            $this->availableNamesPerPredicate(ShopConstraint::shop(self::DEFAULT_SHOP_ID))
        );
        $this->assertSame(
            ['cons_core_open', 'cons_core_restricted', 'cons_orphan'],
            $this->availableNamesPerPredicate(ShopConstraint::shop(self::$secondShopId))
        );
    }

    /**
     * getAvailableShopIds() (write fan-out and the display fallback resolution) must
     * resolve, per definition, exactly the shops the predicate accepts one by one.
     */
    public function testGetAvailableShopIdsMatchesThePerShopPredicate(): void
    {
        $allShopIds = self::$shopListResolver->resolveShopIds(ShopConstraint::allShops());

        foreach ($this->testDefinitions() as $definition) {
            $expected = array_values(array_filter(
                $allShopIds,
                fn (int $shopId): bool => $definition->isAvailableForShops(
                    [$shopId],
                    $this->moduleShopIdsFor($definition)
                )
            ));

            $this->assertSame(
                $expected,
                self::$shopFilter->getAvailableShopIds($definition, $allShopIds),
                sprintf('getAvailableShopIds() drifted from the predicate for %s.', $definition->getPropertyName())
            );
        }
    }

    public function testAllShopsScopeListsEverythingOnEveryImplementation(): void
    {
        $allNames = array_map(
            static fn (ExtraPropertyDefinition $definition): string => $definition->getPropertyName(),
            $this->testDefinitions()
        );
        sort($allNames);

        // The SQL restriction short-circuits on all-shops (registry management view); the
        // predicate agrees because any restriction intersects the full shop list.
        $this->assertSame($allNames, $this->availableNamesPerPredicate(ShopConstraint::allShops()));
        $this->assertSame($allNames, $this->availableNamesPerFilterService(ShopConstraint::allShops()));
        $this->assertSame($allNames, $this->availableNamesPerGridQuery(ShopConstraint::allShops()));
    }

    /**
     * The fixture matrix. Every availability rule is represented:
     *  - cons_core_open: core-owned, unrestricted → everywhere;
     *  - cons_core_restricted: explicit restriction to shop 2;
     *  - cons_module_follow: module-owned, unrestricted → follows MODULE (shops 1 and 3);
     *  - cons_module_restricted: module-owned with an explicit restriction to shop 3 —
     *    the restriction wins over the module fallback;
     *  - cons_orphan: owned by a module with no ps_module_shop row at all → unrestricted.
     *
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'cons_core_open', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'cons_core_restricted', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, associatedShopIds: [self::$secondShopId]),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'cons_module_follow', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'cons_module_restricted', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, associatedShopIds: [self::$thirdShopId]),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'cons_orphan', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::ORPHAN_MODULE),
        ];
    }

    /**
     * @return ExtraPropertyDefinition[] the stored (id-hydrated) fixture definitions
     */
    private function testDefinitions(): array
    {
        return array_values(array_filter(
            iterator_to_array(self::$definitionRepository->getAllDefinitions()),
            static fn (ExtraPropertyDefinition $definition): bool => str_starts_with($definition->getPropertyName(), self::PROPERTY_PREFIX)
        ));
    }

    /**
     * The module fallback input of the pure predicate, derived from the FIXTURES (not from
     * the service under test): MODULE's enabled shops, null for core-owned definitions and
     * for ORPHAN_MODULE (no ps_module_shop row at all → unknown → unrestricted).
     *
     * @return list<int>|null
     */
    private function moduleShopIdsFor(ExtraPropertyDefinition $definition): ?array
    {
        return self::MODULE === $definition->getModuleName()
            ? [self::DEFAULT_SHOP_ID, self::$thirdShopId]
            : null;
    }

    /**
     * @return list<string> sorted property names accepted by the pure predicate
     */
    private function availableNamesPerPredicate(ShopConstraint $shopConstraint): array
    {
        $scopeShopIds = self::$shopListResolver->resolveShopIds($shopConstraint);
        $names = [];
        foreach ($this->testDefinitions() as $definition) {
            if ($definition->isAvailableForShops($scopeShopIds, $this->moduleShopIdsFor($definition))) {
                $names[] = $definition->getPropertyName();
            }
        }
        sort($names);

        return $names;
    }

    /**
     * @return list<string> sorted property names kept by the shop filter service
     */
    private function availableNamesPerFilterService(ShopConstraint $shopConstraint): array
    {
        $filtered = self::$shopFilter->filterByShopConstraint(
            new ExtraPropertyDefinitionCollection($this->testDefinitions()),
            $shopConstraint
        );
        $names = array_map(
            static fn (ExtraPropertyDefinition $definition): string => $definition->getPropertyName(),
            iterator_to_array($filtered)
        );
        sort($names);

        return $names;
    }

    /**
     * @return list<string> sorted property names returned by the registry grid search query
     */
    private function availableNamesPerGridQuery(ShopConstraint $shopConstraint): array
    {
        /** @var ExtraPropertyDefinitionQueryBuilder $queryBuilder */
        $queryBuilder = self::getContainer()->get('prestashop.core.grid.query_builder.extra_property_definition');
        $filters = new ExtraPropertyDefinitionFilters(
            $shopConstraint,
            array_replace(ExtraPropertyDefinitionFilters::getDefaults(), ['filters' => ['property_name' => self::PROPERTY_PREFIX]]),
            'extra_property_definition'
        );

        $rows = $queryBuilder->getSearchQueryBuilder($filters)->executeQuery()->fetchAllAssociative();
        $names = array_map(static fn (array $row): string => (string) $row['property_name'], $rows);
        sort($names);

        return $names;
    }
}
