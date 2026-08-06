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
     * Rule: getPrimaryKeyName() is always 'id_' + the normalized entity name — the
     * PrestaShop primary key convention, centralized so callers never build it manually.
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
            'compound entity' => ['manufacturer_address', 'id_manufacturer_address'],
            'uppercase entity is normalized first' => ['Product', 'id_product'],
            'CamelCase entity is tableized first' => ['ProductAttribute', 'id_product_attribute'],
            'hyphenated entity is normalized first' => ['my-entity', 'id_my_entity'],
        ];
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
