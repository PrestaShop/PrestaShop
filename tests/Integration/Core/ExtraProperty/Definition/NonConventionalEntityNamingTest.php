<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Definition;

use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Entities with non-conventional naming (#41779), end to end against the live DB:
 *
 *  - Order: registered by its natural entity name ('order' / 'Order' / 'orders' all
 *    converge), stored with the physical table 'orders' and the introspected primary key
 *    'id_order' — the deduced-id_orders breakage that used to hit every BO/API surface
 *    (unknown column: silent defaults on read, exception on write, toggle 403);
 *  - Combination: 'combination' resolves the product_attribute table; LANG and SHOP
 *    scopes work against product_attribute_lang / product_attribute_shop;
 *  - the genuine attribute entity stays reachable: its registration is NOT hijacked by
 *    the product_attribute → combination canonicalization;
 *  - the discount entity is canonical over its legacy cart_rule storage (a cart_rule
 *    registration converges on 'discount', stored in cart_rule_extra);
 *  - LANG/SHOP on order are cleanly rejected (no orders_lang / orders_shop base table);
 *  - the cross-entity storage guard refuses a second definition writing the same
 *    physical column under another entity name.
 */
class NonConventionalEntityNamingTest extends KernelTestCase
{
    private const MODULE = 'extrapropnamingtest';

    /** Standard fixtures: order 1 and product 1 (with combinations) always exist. */
    private const ORDER_ID = 1;
    private const DEFAULT_LANG_ID = 1;

    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $definitionRepository;
    private static ExtraPropertyReaderInterface $reader;
    private static ExtraPropertyWriterInterface $writer;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::definitions() as $definition) {
            self::$registry->unregister($definition, true);
        }
        DatabaseDump::restoreTables(['extra_property_definition']);
        DatabaseDump::removeExtraTables();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();

        foreach (self::definitions() as $definition) {
            self::$registry->register($definition);
        }
        // The cached repository must serve freshly hydrated definitions (register()
        // invalidates, but earlier test classes may have warmed unrelated entries).
        self::getContainer()->get('prestashop.extra_property.definition.filesystem_cache')->clear();
    }

    protected function tearDown(): void
    {
        foreach (['orders_extra', 'product_attribute_extra_lang', 'product_attribute_extra_shop'] as $table) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '`');
        }

        parent::tearDown();
    }

    public function testOrderDefinitionResolvesThePhysicalTableAndIntrospectedPrimaryKey(): void
    {
        $definition = self::$definitionRepository->findDefinitionByModuleAndField('order', self::MODULE, 'naming_note');

        $this->assertNotNull($definition);
        $this->assertSame('order', $definition->getEntityName());
        $this->assertSame('orders', $definition->getTableName());
        $this->assertSame('orders_extra', $definition->getExtraTableName());
        // The physical extra table mirrors the base table's PK (id_order, NOT id_orders) —
        // and hydration introspects exactly that column.
        $this->assertSame('id_order', $definition->getPrimaryKeyName());
        $primaryColumns = Db::getInstance()->executeS(
            'SHOW COLUMNS FROM `' . _DB_PREFIX_ . "orders_extra` WHERE `Key` = 'PRI'"
        );
        $this->assertSame(['id_order'], array_column($primaryColumns, 'Field'));
    }

    public function testEverySpellingConvergesOnTheSameDefinition(): void
    {
        // 'Order' and 'orders' re-register the same definition (canonical entity 'order'):
        // the unique key sees one row, not three.
        self::$registry->register(new ExtraPropertyDefinition(
            entityName: 'Order',
            propertyName: 'naming_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
        ));
        self::$registry->register(new ExtraPropertyDefinition(
            entityName: 'orders',
            propertyName: 'naming_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
        ));

        $count = (int) Db::getInstance()->getValue(sprintf(
            "SELECT COUNT(*) FROM `%sextra_property_definition` WHERE `property_name` = 'naming_note' AND `module_name` = '%s'",
            _DB_PREFIX_,
            self::MODULE
        ));
        $this->assertSame(1, $count);
    }

    public function testWriteAndReadRoundtripOnAnOrder(): void
    {
        // The ObjectModel path presents the PHYSICAL table + PK ($definition['table'/'primary']).
        self::$writer->writeAll('orders', 'id_order', self::ORDER_ID, [self::MODULE => [
            'naming_note' => 'leave at the door',
        ]], ShopConstraint::allShops());

        $values = self::$reader->getExtraProperties('orders', 'id_order', self::ORDER_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertSame('leave at the door', $values[self::MODULE]['naming_note']);
    }

    public function testToggleOnAnOrderUsesTheIntrospectedPrimaryKey(): void
    {
        // toggleExtraProperty() has no caller-provided PK: it relies entirely on the
        // hydrated definition — the path that used to build the unknown id_orders column.
        $definition = self::$definitionRepository->findDefinitionByModuleAndField('order', self::MODULE, 'naming_flag');
        $this->assertNotNull($definition);

        self::$writer->toggleExtraProperty($definition, self::ORDER_ID, ShopConstraint::allShops(), self::DEFAULT_LANG_ID);
        $values = self::$reader->getExtraProperties('orders', 'id_order', self::ORDER_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertTrue($values[self::MODULE]['naming_flag']);

        self::$writer->toggleExtraProperty($definition, self::ORDER_ID, ShopConstraint::allShops(), self::DEFAULT_LANG_ID);
        $values = self::$reader->getExtraProperties('orders', 'id_order', self::ORDER_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertFalse($values[self::MODULE]['naming_flag']);
    }

    public function testLangAndShopScopesAreCleanlyRejectedOnOrder(): void
    {
        // No orders_lang base table — the registration fails with the guidance message
        // and persists nothing (DDL runs before the row write).
        try {
            self::$registry->register(new ExtraPropertyDefinition(
                entityName: 'order',
                propertyName: 'naming_lang_rejected',
                type: ExtraPropertyType::STRING,
                scope: ExtraPropertyScope::LANG,
                moduleName: self::MODULE,
            ));
            $this->fail('LANG scope on order should have been rejected.');
        } catch (ExtraPropertyRegistryException $exception) {
            $this->assertSame(ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND, $exception->getCode());
            $this->assertStringContainsString('orders_lang', $exception->getMessage());
        }
    }

    public function testCombinationLangAndShopScopesRoundtrip(): void
    {
        $combinationId = (int) Db::getInstance()->getValue(
            'SELECT MIN(`id_product_attribute`) FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE `id_product` = 1'
        );
        $this->assertGreaterThan(0, $combinationId, 'Standard fixtures should provide combinations on product 1.');

        self::$writer->writeAll('product_attribute', 'id_product_attribute', $combinationId, [self::MODULE => [
            'naming_lang_note' => [self::DEFAULT_LANG_ID => 'combinaison'],
            'naming_shop_note' => 'restock soon',
        ]], ShopConstraint::shop(1));

        $values = self::$reader->getExtraProperties('product_attribute', 'id_product_attribute', $combinationId, self::DEFAULT_LANG_ID, ShopConstraint::shop(1));
        $this->assertSame('combinaison', $values[self::MODULE]['naming_lang_note']);
        $this->assertSame('restock soon', $values[self::MODULE]['naming_shop_note']);

        // The definitions registered as 'combination' really live on product_attribute_* tables.
        $definition = self::$definitionRepository->findDefinitionByModuleAndField('combination', self::MODULE, 'naming_lang_note');
        $this->assertSame('product_attribute_extra_lang', $definition->getExtraTableName());
        $this->assertSame('id_product_attribute', $definition->getPrimaryKeyName());
    }

    public function testAttributeEntityIsNotHijackedByTheCombinationCanonicalization(): void
    {
        // 'product_attribute' canonicalizes to 'combination' — but the genuine attribute
        // entity (whose modern class is the confusingly named ProductAttribute, table
        // `attribute`) must keep its own name and storage tables.
        $definition = new ExtraPropertyDefinition(
            entityName: 'attribute',
            propertyName: 'naming_attribute_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
        );

        try {
            self::$registry->register($definition);

            $hydrated = self::$definitionRepository->findDefinitionByModuleAndField('attribute', self::MODULE, 'naming_attribute_note');
            $this->assertNotNull($hydrated);
            $this->assertSame('attribute', $hydrated->getEntityName());
            $this->assertSame('attribute', $hydrated->getTableName());
            $this->assertSame('attribute_extra', $hydrated->getExtraTableName());
            $this->assertSame('id_attribute', $hydrated->getPrimaryKeyName());

            // The column landed on attribute_extra, never on the combination COMMON storage
            // table (which this class's fixtures don't even create — a hijack would have).
            $columnName = self::MODULE . '_naming_attribute_note';
            $attributeColumns = array_column(Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'attribute_extra`'), 'Field');
            $this->assertContains($columnName, $attributeColumns);
            if (Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . "product_attribute_extra'")) {
                $combinationColumns = array_column(Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'product_attribute_extra`'), 'Field');
                $this->assertNotContains($columnName, $combinationColumns);
            }
        } finally {
            self::$registry->unregister($definition, true);
        }
    }

    public function testDiscountIsTheCanonicalEntityOverTheCartRuleStorage(): void
    {
        // The whole Discount CQRS domain already carries the new name, so 'discount' is
        // the canonical entity (a cart_rule registration converges on it) while the
        // physical storage stays next to the cart_rule table it still lives in.
        $definition = new ExtraPropertyDefinition(
            entityName: 'cart_rule',
            propertyName: 'naming_discount_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
        );

        try {
            self::$registry->register($definition);

            $hydrated = self::$definitionRepository->findDefinitionByModuleAndField('discount', self::MODULE, 'naming_discount_note');
            $this->assertNotNull($hydrated);
            $this->assertSame('discount', $hydrated->getEntityName());
            $this->assertSame('cart_rule', $hydrated->getTableName());
            $this->assertSame('cart_rule_extra', $hydrated->getExtraTableName());
            $this->assertSame('id_cart_rule', $hydrated->getPrimaryKeyName());
            // The toggle permission subject matches the _legacy_controller the discount
            // page itself declares.
            $this->assertSame('AdminCartRules', $hydrated->getControllerName());
        } finally {
            self::$registry->unregister($definition, true);
        }
    }

    public function testAnotherEntityNameCannotClaimTheSameStorageColumn(): void
    {
        // Same module + property + physical table under a DIFFERENT entity name: the DB
        // unique key cannot see it, the registry storage guard must.
        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::STORAGE_CONFLICT);

        self::$registry->register(new ExtraPropertyDefinition(
            entityName: 'order_alias',
            propertyName: 'naming_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
            tableName: 'orders',
        ));
    }

    private static function initServices(): void
    {
        $container = self::getContainer();
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$definitionRepository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
        self::$reader = $container->get(ExtraPropertyReaderInterface::class);
        self::$writer = $container->get(ExtraPropertyWriterInterface::class);
    }

    /**
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'order', propertyName: 'naming_note', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE),
            // NOT NULL needs a DDL default: another property's write may create the row.
            new ExtraPropertyDefinition(entityName: 'order', propertyName: 'naming_flag', type: ExtraPropertyType::BOOL, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: false, nullable: false),
            new ExtraPropertyDefinition(entityName: 'combination', propertyName: 'naming_lang_note', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'combination', propertyName: 'naming_shop_note', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::SHOP, moduleName: self::MODULE),
        ];
    }
}
