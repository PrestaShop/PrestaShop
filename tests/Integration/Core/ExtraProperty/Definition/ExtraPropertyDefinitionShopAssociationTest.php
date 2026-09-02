<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Definition;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\ExtraPropertyDefinitionQueryBuilder;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use PrestaShop\PrestaShop\Core\Search\Filters\ExtraPropertyDefinitionFilters;
use Shop;
use ShopGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;

/**
 * Definition-level shop association, end to end, on the same 3-shop installation as
 * ExtraPropertyMultiShopTest (default shop 1 + a sibling in the default group, a third
 * shop alone in a second group):
 *
 *  - explicit restriction (extra_property_definition_shop) filtering reads and writes,
 *    LANG scope included;
 *  - the module fallback: an unrestricted module-owned definition follows its module's
 *    ps_module_shop rows, live (per-shop cache entries), while a module with no row at
 *    all stays unrestricted (registration precedes enablement);
 *  - the representative-shop read edge on broad constraints;
 *  - the BO commands: Add with an association, Update reverting via [], and the
 *    module-owned carve-out (shops-only edits allowed, anything else rejected);
 *  - the registry grid query builder's shop-context restriction (all shops / single
 *    shop / group, explicit and fallback rows);
 *  - shop deletion purging the association rows (extra_property_definition is declared
 *    in Shop::$asso_tables).
 */
class ExtraPropertyDefinitionShopAssociationTest extends KernelTestCase
{
    private const MODULE = 'extrapropshopassoctest';
    private const ORPHAN_MODULE = 'extrapropshopassocorphan';
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_LANG_ID = 1;

    /** Arbitrary storage-only entity ids, far above installed fixtures. */
    private const RESTRICTED_PRODUCT_ID = 401;
    private const LANG_PRODUCT_ID = 402;
    private const REPRESENTATIVE_PRODUCT_ID = 403;

    private static int $secondShopId;
    private static int $thirdShopId;
    private static int $defaultGroupId;
    private static int $secondGroupId;
    private static int $moduleId;

    private static ExtraPropertyReaderInterface $reader;
    private static ExtraPropertyWriterInterface $writer;
    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $definitionRepository;

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
        $secondShop->name = 'Definition Assoc Shop 2';
        $secondShop->id_shop_group = self::$defaultGroupId;
        $secondShop->id_category = 2;
        $secondShop->active = true;
        $secondShop->save();
        self::$secondShopId = (int) $secondShop->id;

        $secondGroup = new ShopGroup();
        $secondGroup->name = 'Definition Assoc Group 2';
        $secondGroup->save();
        self::$secondGroupId = (int) $secondGroup->id;

        $thirdShop = new Shop();
        $thirdShop->name = 'Definition Assoc Shop 3';
        $thirdShop->id_shop_group = self::$secondGroupId;
        $thirdShop->id_category = 2;
        $thirdShop->active = true;
        $thirdShop->save();
        self::$thirdShopId = (int) $thirdShop->id;

        Shop::resetStaticCache();
        Shop::resetContext();
        Shop::setContext(Shop::CONTEXT_ALL);

        // A fake module row is enough: the fallback only reads ps_module + ps_module_shop.
        Db::getInstance()->execute(sprintf(
            "INSERT INTO `%smodule` (`name`, `active`, `version`) VALUES ('%s', 1, '1.0.0')",
            _DB_PREFIX_,
            self::MODULE
        ));
        self::$moduleId = (int) Db::getInstance()->Insert_ID();
        // Enabled on shop 1 and the third shop only. ORPHAN_MODULE gets no row at all.
        foreach ([self::DEFAULT_SHOP_ID, self::$thirdShopId] as $shopId) {
            Db::getInstance()->execute(sprintf(
                'INSERT IGNORE INTO `%smodule_shop` (`id_module`, `id_shop`) VALUES (%d, %d)',
                _DB_PREFIX_,
                self::$moduleId,
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

        // Broad SHOP-scope writes only touch shops the ENTITY is associated with (native
        // {entity}_shop parity, orthogonal to the definition axis under test): associate
        // every storage id with every shop so only the definition restriction filters.
        foreach ([self::RESTRICTED_PRODUCT_ID, self::LANG_PRODUCT_ID, self::REPRESENTATIVE_PRODUCT_ID] as $productId) {
            foreach ([self::DEFAULT_SHOP_ID, self::$secondShopId, self::$thirdShopId] as $shopId) {
                Db::getInstance()->execute(sprintf(
                    'INSERT IGNORE INTO `%sproduct_shop` (`id_product`, `id_shop`, `id_tax_rules_group`, `date_add`, `date_upd`) VALUES (%d, %d, 0, NOW(), NOW())',
                    _DB_PREFIX_,
                    $productId,
                    $shopId
                ));
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::definitions() as $definition) {
            self::$registry->unregister($definition, true);
        }
        // Commands of this test may have created core-owned definitions.
        Db::getInstance()->execute(sprintf(
            "DELETE FROM `%sextra_property_definition` WHERE `property_name` LIKE 'sa_%%' OR `property_name` LIKE 'zzcmd_%%'",
            _DB_PREFIX_
        ));
        Db::getInstance()->execute(sprintf(
            'DELETE FROM `%sextra_property_definition_shop`',
            _DB_PREFIX_
        ));
        Db::getInstance()->execute(sprintf(
            "DELETE FROM `%smodule` WHERE `name` IN ('%s', '%s')",
            _DB_PREFIX_,
            self::MODULE,
            self::ORPHAN_MODULE
        ));
        // ShopResetter restores every *_shop table (module_shop and extra_property_definition_shop included).
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

        foreach (['product_extra_lang', 'product_extra_shop'] as $table) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '`');
        }
        // Reset every explicit association between tests (fallback state), and drop the
        // shared filesystem cache so per-shop module entries and cached definitions are
        // rebuilt from the current DB state.
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'extra_property_definition_shop`');
        $this->clearExtraPropertyCache();
    }

    private static function initServices(): void
    {
        $container = self::getContainer();
        self::$reader = $container->get(ExtraPropertyReaderInterface::class);
        self::$writer = $container->get(ExtraPropertyWriterInterface::class);
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$definitionRepository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
    }

    public function testExplicitRestrictionFiltersReadsPerShop(): void
    {
        // Unrestricted: a single-shop write lands and reads back.
        self::$writer->writeAll('product', 'id_product', self::RESTRICTED_PRODUCT_ID, [self::MODULE => [
            'sa_shop' => 'everywhere',
        ]], ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        $this->assertSame('everywhere', $this->readValue('sa_shop', self::RESTRICTED_PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID)));

        // Restrict the definition to the third shop: it disappears from shop 1 reads —
        // the whole property key is gone, not just its value.
        $this->restrictDefinition('sa_shop', [self::$thirdShopId]);
        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        $this->assertArrayNotHasKey('sa_shop', $values[self::MODULE] ?? []);

        // On the associated shop the definition is still there (default value: no row yet).
        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$thirdShopId));
        $this->assertArrayHasKey('sa_shop', $values[self::MODULE]);
    }

    public function testExplicitRestrictionSkipsAndIntersectsWrites(): void
    {
        $this->restrictDefinition('sa_shop', [self::$thirdShopId]);

        // Out-of-scope write: skipped entirely.
        self::$writer->writeAll('product', 'id_product', self::RESTRICTED_PRODUCT_ID, [self::MODULE => [
            'sa_shop' => 'must-not-land',
        ]], ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        $this->assertSame(0, $this->countRows('product_extra_shop', self::RESTRICTED_PRODUCT_ID));

        // Broad write: the fan-out intersects with the definition's shops (third shop only).
        self::$writer->writeAll('product', 'id_product', self::RESTRICTED_PRODUCT_ID, [self::MODULE => [
            'sa_shop' => 'third-only',
        ]], ShopConstraint::allShops());
        $this->assertSame(1, $this->countRows('product_extra_shop', self::RESTRICTED_PRODUCT_ID));
        $this->assertSame('third-only', $this->readValue('sa_shop', self::RESTRICTED_PRODUCT_ID, ShopConstraint::shop(self::$thirdShopId)));
    }

    public function testLangScopeFollowsTheDefinitionRestrictionUnlikeEntityAssociations(): void
    {
        $this->restrictDefinition('sa_lang', [self::$thirdShopId]);

        self::$writer->writeAll('product', 'id_product', self::LANG_PRODUCT_ID, [self::MODULE => [
            'sa_lang' => [self::DEFAULT_LANG_ID => 'restricted-lang'],
        ]], ShopConstraint::allShops());

        // product_extra_lang is shop-aware: one row for the third shop only.
        $this->assertSame(1, $this->countRows('product_extra_lang', self::LANG_PRODUCT_ID));
        $values = self::$reader->getExtraProperties('product', 'id_product', self::LANG_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$thirdShopId));
        $this->assertSame('restricted-lang', $values[self::MODULE]['sa_lang']);
    }

    public function testModuleFallbackFollowsTheModuleShopAssociationLive(): void
    {
        // No explicit restriction: sa_shop follows the module (enabled on shops 1 and 3).
        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$secondShopId));
        $this->assertArrayNotHasKey('sa_shop', $values[self::MODULE] ?? []);

        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        $this->assertArrayHasKey('sa_shop', $values[self::MODULE]);

        // Enable the module on the second shop (module actions wipe var/cache in production —
        // simulated here by clearing the shared pool) → the definition follows.
        Db::getInstance()->execute(sprintf(
            'INSERT IGNORE INTO `%smodule_shop` (`id_module`, `id_shop`) VALUES (%d, %d)',
            _DB_PREFIX_,
            self::$moduleId,
            self::$secondShopId
        ));
        $this->clearExtraPropertyCache();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();

        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$secondShopId));
        $this->assertArrayHasKey('sa_shop', $values[self::MODULE]);

        // An explicit BO override takes precedence over the module association.
        $this->restrictDefinition('sa_shop', [self::DEFAULT_SHOP_ID]);
        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$secondShopId));
        $this->assertArrayNotHasKey('sa_shop', $values[self::MODULE] ?? []);

        Db::getInstance()->execute(sprintf(
            'DELETE FROM `%smodule_shop` WHERE `id_module` = %d AND `id_shop` = %d',
            _DB_PREFIX_,
            self::$moduleId,
            self::$secondShopId
        ));
    }

    public function testModuleWithoutAnyShopRowStaysUnrestricted(): void
    {
        // Registration precedes enablement: a module with no ps_module_shop row at all
        // must not hide its definitions anywhere.
        $values = self::$reader->getExtraProperties('product', 'id_product', self::RESTRICTED_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$secondShopId));
        $this->assertArrayHasKey('sa_orphan', $values[self::ORPHAN_MODULE]);
    }

    public function testBroadReadPinsTheRepresentativeShopEvenOutsideTheRestriction(): void
    {
        // Definition restricted to the third shop, value stored there.
        $this->restrictDefinition('sa_shop', [self::$thirdShopId]);
        self::$writer->writeAll('product', 'id_product', self::REPRESENTATIVE_PRODUCT_ID, [self::MODULE => [
            'sa_shop' => 'third-value',
        ]], ShopConstraint::shop(self::$thirdShopId));

        // An all-shops read passes the availability filter (the restriction intersects the
        // scope) but pins the representative shop (default shop 1), which holds no row:
        // the property is present with its default value. Documented edge.
        $values = self::$reader->getExtraProperties('product', 'id_product', self::REPRESENTATIVE_PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertArrayHasKey('sa_shop', $values[self::MODULE]);
        $this->assertNull($values[self::MODULE]['sa_shop']);
    }

    public function testAddCommandPersistsTheAssociation(): void
    {
        // The property name deliberately avoids the 'sa' prefix so the grid scenario's
        // LIKE filter can never match this command-created definition.
        /** @var ExtraPropertyDefinitionId $id */
        $id = $this->getCommandBus()->handle(new AddExtraPropertyDefinitionCommand(
            entityName: 'product',
            propertyName: 'zzcmd_core_created',
            fieldType: ExtraPropertyType::STRING,
            fieldScope: ExtraPropertyScope::COMMON,
            associatedShopIds: [self::$secondShopId, self::$thirdShopId],
        ));

        $definition = self::$definitionRepository->getDefinitionById($id->getValue());
        $this->assertSame([self::$secondShopId, self::$thirdShopId], $definition->getAssociatedShopIds());

        // Update with [] reverts to the fallback (no restriction).
        $this->getCommandBus()->handle(
            (new UpdateExtraPropertyDefinitionCommand($id->getValue()))->setAssociatedShopIds([])
        );
        $this->assertNull(self::$definitionRepository->getDefinitionById($id->getValue())->getAssociatedShopIds());

        // Clean up through the delete command (also drops the storage column).
        $this->getCommandBus()->handle(new DeleteExtraPropertyDefinitionCommand($id->getValue(), true));
        $this->assertNull(self::$definitionRepository->getDefinitionById($id->getValue()));
    }

    public function testModuleOwnedDefinitionsAcceptOnlyShopAssociationEdits(): void
    {
        $moduleDefinitionId = (int) Db::getInstance()->getValue(sprintf(
            "SELECT id_extra_property_definition FROM `%sextra_property_definition` WHERE module_name = '%s' AND property_name = 'sa_shop'",
            _DB_PREFIX_,
            self::MODULE
        ));
        $this->assertGreaterThan(0, $moduleDefinitionId);

        // The carve-out: a shops-only update succeeds on a module-owned definition…
        $this->getCommandBus()->handle(
            (new UpdateExtraPropertyDefinitionCommand($moduleDefinitionId))->setAssociatedShopIds([self::$thirdShopId])
        );
        $this->assertSame([self::$thirdShopId], self::$definitionRepository->getDefinitionById($moduleDefinitionId)->getAssociatedShopIds());

        // …while any other modification is still rejected, association or not.
        $this->expectException(ProtectedModuleExtraPropertyDefinitionException::class);
        $this->getCommandBus()->handle(
            (new UpdateExtraPropertyDefinitionCommand($moduleDefinitionId))
                ->setAssociatedShopIds([self::$thirdShopId])
                ->setDisplayFront(true)
        );
    }

    public function testDeletingAShopPurgesItsAssociationRows(): void
    {
        // extra_property_definition is declared in Shop::$asso_tables, so the generic
        // Shop::delete() loop wipes its *_shop rows — no orphan restriction can survive
        // a shop deletion and silently hide the definition everywhere.
        $temporaryShop = new Shop();
        $temporaryShop->name = 'Definition Assoc Temp Shop';
        $temporaryShop->id_shop_group = self::$defaultGroupId;
        $temporaryShop->id_category = 2;
        $temporaryShop->active = true;
        $temporaryShop->save();
        $temporaryShopId = (int) $temporaryShop->id;
        Shop::resetStaticCache();

        // Shop::delete() refuses shops carrying customers or orders (hasDependency()), and
        // this id is recycled: ShopResetter restores ps_shop (AUTO_INCREMENT included) but
        // never touches customer/orders, so other tests of a full-suite run may have left
        // rows on it. Clear them so the assertion below only guards the purge under test.
        Db::getInstance()->delete('customer', 'id_shop = ' . $temporaryShopId);
        Db::getInstance()->delete('orders', 'id_shop = ' . $temporaryShopId);

        $this->restrictDefinition('sa_shop', [self::$thirdShopId, $temporaryShopId]);

        // Cast: Shop::delete() accumulates through `&=`, so it returns int(1) on success.
        $this->assertTrue((bool) $temporaryShop->delete());
        Shop::resetStaticCache();
        Shop::resetContext();
        Shop::setContext(Shop::CONTEXT_ALL);

        $remainingShopIds = Db::getInstance()->executeS(sprintf(
            'SELECT epds.id_shop FROM `%sextra_property_definition_shop` epds
             INNER JOIN `%sextra_property_definition` epd
                ON epd.id_extra_property_definition = epds.id_extra_property_definition
             WHERE epd.property_name = "sa_shop"',
            _DB_PREFIX_,
            _DB_PREFIX_
        ));
        // The deleted shop's row is gone, the third shop's row survives: the definition
        // keeps its (still meaningful) restriction instead of vanishing everywhere.
        $this->assertSame(
            [self::$thirdShopId],
            array_map(static fn (array $row): int => (int) $row['id_shop'], $remainingShopIds)
        );
    }

    public function testRegistryGridQueryBuilderRestrictsRowsToTheShopContext(): void
    {
        $this->restrictDefinition('sa_shop', [self::$thirdShopId]);

        // All shops: everything is listed (registry management view).
        $this->assertSame(
            ['sa_lang', 'sa_orphan', 'sa_shop'],
            $this->fetchGridPropertyNames(ShopConstraint::allShops())
        );

        // Second shop: sa_shop is restricted elsewhere, and the module (fallback of sa_lang)
        // is not enabled there; the orphan module stays unrestricted.
        $this->assertSame(
            ['sa_orphan'],
            $this->fetchGridPropertyNames(ShopConstraint::shop(self::$secondShopId))
        );

        // Second group (third shop): explicit restriction matches, module enabled there.
        $this->assertSame(
            ['sa_lang', 'sa_orphan', 'sa_shop'],
            $this->fetchGridPropertyNames(ShopConstraint::shopGroup(self::$secondGroupId))
        );
    }

    /**
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'sa_shop', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::SHOP, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'sa_lang', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'sa_orphan', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::ORPHAN_MODULE),
        ];
    }

    private function restrictDefinition(string $propertyName, array $shopIds): void
    {
        $definitionId = (int) Db::getInstance()->getValue(sprintf(
            "SELECT id_extra_property_definition FROM `%sextra_property_definition` WHERE property_name = '%s'",
            _DB_PREFIX_,
            $propertyName
        ));
        // Through the single registration endpoint (register() -> save() persists the
        // association) — also exercises the module-owned shops-only carve-out.
        $this->getCommandBus()->handle(
            (new UpdateExtraPropertyDefinitionCommand($definitionId))->setAssociatedShopIds($shopIds)
        );
    }

    /**
     * @return list<string> property names returned by the registry grid search query,
     *                      restricted to this test's definitions, sorted
     */
    private function fetchGridPropertyNames(ShopConstraint $shopConstraint): array
    {
        /** @var ExtraPropertyDefinitionQueryBuilder $queryBuilder */
        $queryBuilder = self::getContainer()->get('prestashop.core.grid.query_builder.extra_property_definition');
        $filters = new ExtraPropertyDefinitionFilters(
            $shopConstraint,
            array_replace(ExtraPropertyDefinitionFilters::getDefaults(), ['filters' => ['property_name' => 'sa_']]),
            'extra_property_definition'
        );

        $rows = $queryBuilder->getSearchQueryBuilder($filters)->executeQuery()->fetchAllAssociative();
        // The grid filter only narrows (LIKE '%sa_%', where _ is a single-char SQL
        // wildcard, so e.g. 'usage' would match): scope strictly in PHP so definitions
        // left in the shared test DB by other suites can never leak into the expected sets.
        $names = array_values(array_filter(
            array_map(static fn (array $row): string => (string) $row['property_name'], $rows),
            static fn (string $name): bool => str_starts_with($name, 'sa_')
        ));
        sort($names);

        return $names;
    }

    private function getCommandBus(): CommandBusInterface
    {
        return self::getContainer()->get('prestashop.core.command_bus');
    }

    private function readValue(string $propertyName, int $productId, ShopConstraint $shopConstraint): mixed
    {
        $values = self::$reader->getExtraProperties('product', 'id_product', $productId, self::DEFAULT_LANG_ID, $shopConstraint);

        return $values[self::MODULE][$propertyName] ?? null;
    }

    private function countRows(string $table, int $productId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $table . '` WHERE `id_product` = ' . $productId
        );
    }

    private function clearExtraPropertyCache(): void
    {
        self::getContainer()->get('prestashop.extra_property.definition.filesystem_cache')->clear();
    }
}
