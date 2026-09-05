<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;

class ExtraPropertyDefinitionNamingTest extends TestCase
{
    /**
     * @dataProvider extraTableNameProvider
     */
    public function testBuildExtraTableName(string $entityName, ExtraPropertyScope $scope, string $expected): void
    {
        $this->assertSame($expected, ExtraPropertyDefinition::buildExtraTableName($entityName, $scope));
    }

    public static function extraTableNameProvider(): array
    {
        return [
            'common scope' => ['product', ExtraPropertyScope::COMMON, 'product_extra'],
            'lang scope' => ['product', ExtraPropertyScope::LANG, 'product_extra_lang'],
            'shop scope' => ['product', ExtraPropertyScope::SHOP, 'product_extra_shop'],
            'different entity common' => ['customer', ExtraPropertyScope::COMMON, 'customer_extra'],
            'different entity lang' => ['customer', ExtraPropertyScope::LANG, 'customer_extra_lang'],
            'different entity shop' => ['customer', ExtraPropertyScope::SHOP, 'customer_extra_shop'],
        ];
    }

    /**
     * @dataProvider storageColumnNameProvider
     */
    public function testBuildStorageColumnName(?string $moduleName, string $propertyName, string $expected): void
    {
        $this->assertSame($expected, ExtraPropertyDefinition::buildStorageColumnName($moduleName, $propertyName));
    }

    public static function storageColumnNameProvider(): array
    {
        return [
            'null module (core field)' => [null, 'video_link', 'video_link'],
            'empty string module (core field)' => ['', 'video_link', 'video_link'],
            'core sentinel module' => [ExtraPropertyDefinition::CORE_MODULE_KEY, 'video_link', 'video_link'],
            'module prefixes property' => ['ps_mymodule', 'video_link', 'ps_mymodule_video_link'],
            'another module' => ['demomodule', 'color', 'demomodule_color'],
        ];
    }

    /**
     * @dataProvider fieldNameProvider
     */
    public function testGetFieldName(ExtraPropertyDefinition $definition, string $expected): void
    {
        $this->assertSame($expected, $definition->getFieldName());
    }

    public static function fieldNameProvider(): array
    {
        // The scope is intentionally NOT part of the field name: the same module + property name yields the same
        // identifier whatever the scope (a property is unique per module + name).
        return [
            'module field, common scope' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'video_link', scope: ExtraPropertyScope::COMMON, moduleName: 'ps_mymodule'),
                'extra_ps_mymodule_video_link',
            ],
            'module field, lang scope (same name as common)' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'video_link', scope: ExtraPropertyScope::LANG, moduleName: 'ps_mymodule'),
                'extra_ps_mymodule_video_link',
            ],
            'module field, shop scope (same name as common)' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'video_link', scope: ExtraPropertyScope::SHOP, moduleName: 'ps_mymodule'),
                'extra_ps_mymodule_video_link',
            ],
            'core sentinel module' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'my_field', scope: ExtraPropertyScope::COMMON, moduleName: ExtraPropertyDefinition::CORE_MODULE_KEY),
                'extra__core_my_field',
            ],
            'null module treated as _core' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'my_field', scope: ExtraPropertyScope::COMMON, moduleName: null),
                'extra__core_my_field',
            ],
        ];
    }

    /**
     * Rule: getNormalizedModuleKey() always returns '_core' for core fields and the
     * module technical name otherwise — computed once at construction.
     *
     * @dataProvider normalizedModuleKeyProvider
     */
    public function testGetNormalizedModuleKey(ExtraPropertyDefinition $definition, string $expected): void
    {
        $this->assertSame($expected, $definition->getNormalizedModuleKey());
    }

    public static function normalizedModuleKeyProvider(): array
    {
        return [
            'null maps to _core' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: null),
                ExtraPropertyDefinition::CORE_MODULE_KEY,
            ],
            'empty string maps to _core' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: ''),
                ExtraPropertyDefinition::CORE_MODULE_KEY,
            ],
            '_core stays _core' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: ExtraPropertyDefinition::CORE_MODULE_KEY),
                ExtraPropertyDefinition::CORE_MODULE_KEY,
            ],
            'actual module name is returned as-is' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: 'ps_mymodule'),
                'ps_mymodule',
            ],
            'another module' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: 'demomodule'),
                'demomodule',
            ],
        ];
    }

    /**
     * Rule: getModuleName() always returns null for core fields — '' and the '_core'
     * sentinel are normalized to null at construction, so callers never need to
     * re-normalize (getNormalizedModuleKey() is the '_core'-keyed counterpart).
     *
     * @dataProvider moduleNameNormalizationProvider
     */
    public function testGetModuleNameIsNormalizedAtConstruction(?string $inputModuleName, ?string $expected): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: $inputModuleName);

        $this->assertSame($expected, $definition->getModuleName());
    }

    public static function moduleNameNormalizationProvider(): array
    {
        return [
            'null stays null' => [null, null],
            'empty string normalized to null' => ['', null],
            '_core sentinel normalized to null' => [ExtraPropertyDefinition::CORE_MODULE_KEY, null],
            'module name kept as-is' => ['ps_mymodule', 'ps_mymodule'],
        ];
    }

    /**
     * Rule: getPrimaryKeyName() prefers the explicit/introspected value, then the entity's
     * own ObjectModel $definition['primary'], then falls back to 'id_' + the normalized
     * entity name — the PrestaShop primary key convention, centralized so callers never
     * build it manually.
     *
     * @dataProvider primaryKeyNameProvider
     */
    public function testGetPrimaryKeyName(string $entityName, string $expected): void
    {
        $definition = new ExtraPropertyDefinition(entityName: $entityName, propertyName: 'field');

        $this->assertSame($expected, $definition->getPrimaryKeyName());
    }

    public static function primaryKeyNameProvider(): array
    {
        return [
            'simple entity' => ['product', 'id_product'],
            // ManufacturerAddress only INHERITS Address::$definition — resolution must
            // ignore inherited definitions and fall back to the naming convention.
            'compound entity with an inheriting class' => ['manufacturer_address', 'id_manufacturer_address'],
            'uppercase entity is normalized first' => ['Product', 'id_product'],
            // Canonicalized to 'combination'; the Combination class declares id_product_attribute.
            'CamelCase irregular entity resolves through its class' => ['ProductAttribute', 'id_product_attribute'],
            'irregular entity resolves through its class' => ['order', 'id_order'],
            'irregular table spelling is canonicalized first' => ['orders', 'id_order'],
            'hyphenated entity is normalized first' => ['my-entity', 'id_my_entity'],
        ];
    }

    /**
     * The full entity resolution matrix: every accepted spelling of an entity converges
     * onto ONE canonical (entityName, tableName) pair — the canonical map handles the two
     * known irregular families before class resolution ever runs (which is what keeps the
     * modern ProductAttribute class, the renamed Attribute entity with table `attribute`,
     * from hijacking the combination spellings), the ObjectModel class resolves the rest,
     * and a bare table name stays itself.
     *
     * @dataProvider entityResolutionProvider
     */
    public function testEntityNameAndTableNameResolution(string $input, string $expectedEntityName, string $expectedTableName): void
    {
        $definition = new ExtraPropertyDefinition(entityName: $input, propertyName: 'field');

        $this->assertSame($expectedEntityName, $definition->getEntityName());
        $this->assertSame($expectedTableName, $definition->getTableName());
    }

    public static function entityResolutionProvider(): iterable
    {
        // The combination family — four spellings, one definition.
        yield 'combination' => ['combination', 'combination', 'product_attribute'];
        yield 'Combination class spelling' => ['Combination', 'combination', 'product_attribute'];
        yield 'product_attribute table spelling' => ['product_attribute', 'combination', 'product_attribute'];
        yield 'ProductAttribute class spelling (never class-resolved to the attribute table)' => ['ProductAttribute', 'combination', 'product_attribute'];

        // The order family — three spellings, one definition.
        yield 'order' => ['order', 'order', 'orders'];
        yield 'Order class spelling' => ['Order', 'order', 'orders'];
        yield 'orders table spelling' => ['orders', 'order', 'orders'];

        // Conventional entities: resolution is a no-op.
        yield 'cart' => ['cart', 'cart', 'cart'];
        yield 'product' => ['product', 'product', 'product'];

        // The genuine attribute entity stays reachable under its own table name.
        yield 'attribute' => ['attribute', 'attribute', 'attribute'];

        // Modern CQRS domain names are canonical (like combination — those entities are
        // headed for that rename), while the physical table stays the legacy one, mapped
        // explicitly since no ObjectModel class carries the modern name yet.
        yield 'discount' => ['discount', 'discount', 'cart_rule'];
        yield 'Discount domain spelling' => ['Discount', 'discount', 'cart_rule'];
        yield 'cart_rule legacy spelling' => ['cart_rule', 'discount', 'cart_rule'];
        yield 'cms_page' => ['cms_page', 'cms_page', 'cms'];
        yield 'cms legacy spelling' => ['cms', 'cms_page', 'cms'];
        yield 'cms_page_category' => ['cms_page_category', 'cms_page_category', 'cms_category'];
        yield 'cms_category legacy spelling' => ['cms_category', 'cms_page_category', 'cms_category'];
        yield 'credit_slip' => ['credit_slip', 'credit_slip', 'order_slip'];
        yield 'order_slip legacy spelling' => ['order_slip', 'credit_slip', 'order_slip'];
        yield 'catalog_price_rule' => ['catalog_price_rule', 'catalog_price_rule', 'specific_price_rule'];
        yield 'specific_price_rule legacy spelling' => ['specific_price_rule', 'catalog_price_rule', 'specific_price_rule'];
        yield 'sql_request' => ['sql_request', 'sql_request', 'request_sql'];
        yield 'request_sql legacy spelling' => ['request_sql', 'sql_request', 'request_sql'];
        yield 'title' => ['title', 'title', 'gender'];
        yield 'gender legacy spelling' => ['gender', 'title', 'gender'];

        // Table spellings of class↔table disparities converge like 'orders' does —
        // otherwise each would register a second entity name on the same storage table.
        yield 'lang table spelling' => ['lang', 'language', 'lang'];
        yield 'language' => ['language', 'language', 'lang'];
        yield 'connections table spelling' => ['connections', 'connection', 'connections'];
        yield 'webservice_account table spelling' => ['webservice_account', 'webservice_key', 'webservice_account'];

        // BO grid/menu spelling converges on the entity behind the grid.
        yield 'merchandise_return grid spelling' => ['merchandise_return', 'order_return', 'order_return'];

        // No matching ObjectModel class: bare-table registration, the name is the table.
        yield 'unknown table' => ['my_custom_table', 'my_custom_table', 'my_custom_table'];

        // Class-name collision guards: Link exists but is not an ObjectModel; the
        // ManufacturerAddress class only INHERITS Address::$definition — neither may
        // repoint the table.
        yield 'non-ObjectModel class name' => ['link', 'link', 'link'];
        yield 'class with inherited definition' => ['manufacturer_address', 'manufacturer_address', 'manufacturer_address'];
    }

    /**
     * Explicit tableName / primaryKeyName constructor values (the third-party escape
     * hatch for ObjectModels whose entity name differs from their table) win over any
     * resolution, and both survive the copy-with methods.
     */
    public function testExplicitTableNameAndPrimaryKeyNameAreRespectedAndPreserved(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'my_entity',
            propertyName: 'field',
            tableName: 'my_actual_table',
            primaryKeyName: 'id_my_actual',
        );

        $this->assertSame('my_entity', $definition->getEntityName());
        $this->assertSame('my_actual_table', $definition->getTableName());
        $this->assertSame('id_my_actual', $definition->getPrimaryKeyName());

        $withModule = $definition->withModuleName('ps_mymodule');
        $this->assertSame('my_actual_table', $withModule->getTableName());
        $this->assertSame('id_my_actual', $withModule->getPrimaryKeyName());

        $withOverrides = $definition->withOverrides(['displayFront' => true]);
        $this->assertSame('my_actual_table', $withOverrides->getTableName());
        $this->assertSame('id_my_actual', $withOverrides->getPrimaryKeyName());
    }

    /**
     * @dataProvider invalidExplicitIdentifierProvider
     */
    public function testInvalidExplicitTableNameOrPrimaryKeyNameIsRejected(?string $tableName, ?string $primaryKeyName): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);

        new ExtraPropertyDefinition(
            entityName: 'my_entity',
            propertyName: 'field',
            tableName: $tableName,
            primaryKeyName: $primaryKeyName,
        );
    }

    public static function invalidExplicitIdentifierProvider(): iterable
    {
        yield 'sql injection in tableName' => ["orders'; DROP TABLE x; --", null];
        yield 'space in tableName' => ['my table', null];
        yield 'sql injection in primaryKeyName' => [null, "id'; --"];
        yield 'overlong primaryKeyName' => [null, str_repeat('a', 65)];
    }

    /**
     * getControllerName() is the BO permission subject used by any employee-permission
     * check tied to a definition (e.g. the grid toggle). The mapping is security-relevant:
     * the irregular-tab map covers entities whose real tab breaks the 'Admin' + pluralized
     * entity convention, the inflector convention covers the rest, and a resolved name
     * matching no existing tab is deny-safe (Access::isGranted() grants nothing for
     * unknown subjects).
     *
     * @dataProvider controllerNameProvider
     */
    public function testControllerNameResolution(string $entityName, string $expected): void
    {
        $definition = new ExtraPropertyDefinition(entityName: $entityName, propertyName: 'field');

        $this->assertSame($expected, $definition->getControllerName());
        // No override was given: nothing must be persisted, the deduction stays live.
        $this->assertNull($definition->getControllerNameOverride());
    }

    public static function controllerNameProvider(): iterable
    {
        // The convention: 'Admin' + pluralize(classify(entity)).
        yield 'product' => ['product', 'AdminProducts'];
        yield 'order' => ['order', 'AdminOrders'];
        yield 'category (ies pluralization)' => ['category', 'AdminCategories'];
        yield 'manufacturer_address (snake_case classified)' => ['manufacturer_address', 'AdminManufacturerAddresses'];

        // The irregular-tab map.
        yield 'attribute' => ['attribute', 'AdminAttributesGroups'];
        yield 'attribute_group' => ['attribute_group', 'AdminAttributesGroups'];
        yield 'shipment (grid embedded in the order page)' => ['shipment', 'AdminOrders'];

        // Canonicalized entity names resolve through their canonical spelling: the map or
        // the convention applies AFTER the entity alias — whatever spelling the
        // definition was registered with.
        yield 'cms_page' => ['cms_page', 'AdminCmsContent'];
        yield 'cms legacy spelling' => ['cms', 'AdminCmsContent'];
        yield 'credit_slip' => ['credit_slip', 'AdminSlip'];
        yield 'discount' => ['discount', 'AdminCartRules'];
        yield 'cart_rule legacy spelling' => ['cart_rule', 'AdminCartRules'];
        yield 'title' => ['title', 'AdminGenders'];
        yield 'gender legacy spelling' => ['gender', 'AdminGenders'];
        yield 'merchandise_return grid spelling' => ['merchandise_return', 'AdminReturn'];
        yield 'mail (email logs entity)' => ['mail', 'AdminEmails'];

        // No AdminCombinations tab exists: deny-safe unknown subject (and moot — the
        // combination list is Vue-based, no grid toggle can target it).
        yield 'combination (deny-safe unknown tab)' => ['combination', 'AdminCombinations'];
    }

    public function testExplicitControllerNameOverrideWinsAndIsExposedForPersistence(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'my_module_entity',
            propertyName: 'field',
            controllerName: 'AdminMyModuleThings',
        );

        $this->assertSame('AdminMyModuleThings', $definition->getControllerName());
        $this->assertSame('AdminMyModuleThings', $definition->getControllerNameOverride());
    }

    /**
     * An explicit value equal to the deduction is NOT an override: it collapses to null at
     * construction, so re-registering with the conventional name also cleans a previously
     * stored override, and the deduction keeps following core code as BO tabs evolve.
     */
    public function testControllerNameOverrideMatchingTheDeductionCollapsesToNull(): void
    {
        $conventional = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'field',
            controllerName: 'AdminProducts',
        );
        $mapped = new ExtraPropertyDefinition(
            entityName: 'attribute',
            propertyName: 'field',
            controllerName: 'AdminAttributesGroups',
        );

        $this->assertNull($conventional->getControllerNameOverride());
        $this->assertSame('AdminProducts', $conventional->getControllerName());
        $this->assertNull($mapped->getControllerNameOverride());
        $this->assertSame('AdminAttributesGroups', $mapped->getControllerName());
    }

    public function testControllerNameOverrideSurvivesHydrationAndCopyWithMethods(): void
    {
        $hydrated = ExtraPropertyDefinition::fromRow([
            'entity_name' => 'my_module_entity',
            'property_name' => 'field',
            'controller_name' => 'AdminMyModuleThings',
        ]);

        $this->assertSame('AdminMyModuleThings', $hydrated->getControllerName());
        $this->assertSame('AdminMyModuleThings', $hydrated->withModuleName('mymodule')->getControllerNameOverride());
        $this->assertSame('AdminMyModuleThings', $hydrated->withOverrides([])->getControllerNameOverride());
        // A row without the column (or predating it) simply resolves the deduction.
        $this->assertNull(ExtraPropertyDefinition::fromRow([
            'entity_name' => 'product',
            'property_name' => 'field',
        ])->getControllerNameOverride());
    }

    public function testInvalidControllerNameIsRejected(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'field',
            controllerName: 'Admin Products; DROP',
        );
    }

    public function testCoreModuleKeyConstant(): void
    {
        $this->assertSame('_core', ExtraPropertyDefinition::CORE_MODULE_KEY);
    }

    /**
     * Tests parseGridEntry indirectly via getGridEntry() on a definition instance.
     *
     * parseGridEntry() is protected; testing it via getGridEntry() covers the same behavior
     * through the public API.
     *
     * @dataProvider parseGridEntryProvider
     *
     * @param array{gridId: string, columnId: string|null, mode: 'before'|'after'|null} $expected
     */
    public function testGetGridEntry(string $entry, array $expected): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: $expected['gridId'],
            propertyName: 'test_field',
            associatedGrids: [$entry],
            labelWording: 'Test',
        );

        $this->assertSame($expected, $definition->getGridEntry($expected['gridId']));
    }

    public static function parseGridEntryProvider(): array
    {
        return [
            'bare grid id — no column, no mode' => [
                'product',
                ['gridId' => 'product', 'columnId' => null, 'mode' => null],
            ],
            'grid with column — default mode is after' => [
                'product:reference',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'after'],
            ],
            'grid with column explicit after' => [
                'product:reference:after',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'after'],
            ],
            'grid with column explicit before' => [
                'product:reference:before',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'before'],
            ],
            'compound grid id with column' => [
                'manufacturer_address:city',
                ['gridId' => 'manufacturer_address', 'columnId' => 'city', 'mode' => 'after'],
            ],
            'compound grid id with column and before' => [
                'manufacturer_address:city:before',
                ['gridId' => 'manufacturer_address', 'columnId' => 'city', 'mode' => 'before'],
            ],
            'colon with empty rest treated as no column' => [
                'product:',
                ['gridId' => 'product', 'columnId' => null, 'mode' => null],
            ],
        ];
    }

    /**
     * Tests parseFormEntry indirectly via getFormEntry() on a definition instance.
     *
     * parseFormEntry() is protected; testing it via getFormEntry() covers the same behavior
     * through the public API. getFormEntry() resolves placement completely: no mode → container
     * (path is the full path, anchor null), mode set → anchor (path is the parent,
     * anchor is the last segment).
     *
     * @dataProvider parseFormEntryProvider
     *
     * @param array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null} $expected
     */
    public function testGetFormEntry(string $entry, array $expected): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: $expected['formId'],
            propertyName: 'test_field',
            associatedForms: [$entry],
            labelWording: 'Test',
        );

        $this->assertSame($expected, $definition->getFormEntry($expected['formId']));
    }

    public static function parseFormEntryProvider(): array
    {
        return [
            'bare form id — no path, no mode' => [
                'product',
                ['formId' => 'product', 'mode' => null, 'path' => null, 'anchor' => null],
            ],
            'container path, no mode' => [
                'product:options',
                ['formId' => 'product', 'mode' => null, 'path' => 'options', 'anchor' => null],
            ],
            'nested container path, no mode' => [
                'product:options.suppliers',
                ['formId' => 'product', 'mode' => null, 'path' => 'options.suppliers', 'anchor' => null],
            ],
            'anchor path before — parent is everything before last segment' => [
                'product:options.suppliers:before',
                ['formId' => 'product', 'mode' => 'before', 'path' => 'options', 'anchor' => 'suppliers'],
            ],
            'anchor path after' => [
                'product:options.suppliers:after',
                ['formId' => 'product', 'mode' => 'after', 'path' => 'options', 'anchor' => 'suppliers'],
            ],
            'anchor at root — single segment means parent is root' => [
                'product:options:before',
                ['formId' => 'product', 'mode' => 'before', 'path' => '', 'anchor' => 'options'],
            ],
            'compound form id with container path' => [
                'manufacturer_address:city',
                ['formId' => 'manufacturer_address', 'mode' => null, 'path' => 'city', 'anchor' => null],
            ],
            'colon with empty rest treated as no path' => [
                'product:',
                ['formId' => 'product', 'mode' => null, 'path' => null, 'anchor' => null],
            ],
        ];
    }
}
