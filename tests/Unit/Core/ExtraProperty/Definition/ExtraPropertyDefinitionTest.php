<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Definition;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

final class ExtraPropertyDefinitionTest extends TestCase
{
    private const BASE_ROW = [
        'entity_name' => 'product',
        'property_name' => 'packaging_type',
        'type' => 'choice',
        'scope' => 'common',
        'module_name' => 'demoextrafield',
    ];

    // -------------------------------------------------------------------------
    // Construction / invariants
    // -------------------------------------------------------------------------

    /**
     * @dataProvider invalidSqlIdentifierProvider
     */
    public function testInvalidEntityNameThrows(string $invalidValue): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/entityName/');

        new ExtraPropertyDefinition(entityName: $invalidValue, propertyName: 'video_link');
    }

    /**
     * @dataProvider invalidSqlIdentifierProvider
     */
    public function testInvalidPropertyNameThrows(string $invalidValue): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/propertyName/');

        new ExtraPropertyDefinition(entityName: 'product', propertyName: $invalidValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidSqlIdentifierProvider(): array
    {
        return [
            'empty string' => [''],
            'space' => ['invalid name'],
            'dot' => ['entity.name'],
            'at sign' => ['entity@name'],
            'slash' => ['entity/name'],
            'parenthesis' => ['entity(name)'],
            'SQL injection attempt' => ["'; DROP TABLE product; --"],
            'longer than the 64-char MySQL identifier limit' => [str_repeat('a', 65)],
        ];
    }

    public function testPropertyNameIsKeptAsIs(): void
    {
        $this->assertSame('VideoLink', (new ExtraPropertyDefinition(entityName: 'product', propertyName: 'VideoLink'))->getPropertyName());
        $this->assertSame('field456', (new ExtraPropertyDefinition(entityName: 'product', propertyName: 'field456'))->getPropertyName());
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

    public function testInvalidControllerNameIsRejected(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'field',
            controllerName: 'Admin Products; DROP',
        );
    }

    public function testNonConstraintEntryInConstraintsThrows(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/constraint/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            constraints: [new stdClass()], // @phpstan-ignore-line intentionally invalid: must be a Symfony Constraint
        );
    }

    public function testValidConstraintsAreAccepted(): void
    {
        $url = new Assert\Url();
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            constraints: [$url],
        );

        $this->assertSame([$url], $definition->getConstraints());
    }

    /**
     * DDL consumers (ExtraPropertySchemaManager) embed the storage column without re-validating it.
     */
    public function testStorageColumnNameLongerThan64CharsThrows(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/storage column name/');

        // moduleName (30) + '_' + propertyName (40) = 71 chars > 64.
        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: str_repeat('p', 40),
            moduleName: str_repeat('m', 30),
        );
    }

    public function testHyphensAreNormalizedToSqlSafeStorageColumn(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video-link',
            moduleName: 'my-module',
        );

        $this->assertSame('video-link', $definition->getPropertyName());
        $this->assertSame('my_module_video_link', $definition->getStorageColumnName());
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_]{1,64}$/', $definition->getStorageColumnName());
    }

    public function testAssociatedFormsRequiresLabelWording(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelWording is required/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedForms: ['product'],
        );
    }

    public function testAssociatedGridsRequiresLabelWording(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelWording is required/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedGrids: ['product'],
        );
    }

    public function testDuplicateFormIdThrows(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/duplicate formId/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedForms: ['product', 'product'],
            labelWording: 'Video link',
        );
    }

    public function testDuplicateGridIdThrows(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/duplicate gridId/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedGrids: ['product', 'product'],
            labelWording: 'Video link',
        );
    }

    /**
     * @dataProvider unsafeDisplayTextProvider
     */
    public function testLabelWordingThatCouldOpenATagThrows(string $text): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelWording must not contain/');

        new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link', labelWording: $text);
    }

    /**
     * @dataProvider unsafeDisplayTextProvider
     */
    public function testDescriptionWordingThatCouldOpenATagThrows(string $text): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/descriptionWording must not contain/');

        new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link', descriptionWording: $text);
    }

    /**
     * @dataProvider unsafeDisplayTextProvider
     */
    public function testEnumValueThatCouldOpenATagThrows(string $text): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/enum value .* must not contain/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'size',
            type: ExtraPropertyType::CHOICE,
            enumValues: ['ok', $text],
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unsafeDisplayTextProvider(): array
    {
        return [
            'image with event handler' => ['<img src=x onerror=alert(1)>'],
            'opening angle bracket only' => ['5 < 6'],
            'script tag' => ['<script>alert(1)</script>'],
            'NUL byte' => ["Video\0link"],
            'escape sequence' => ["Video\x1b[31mlink"],
        ];
    }

    /**
     * Enum literals are read back from the live SQL ENUM column, where a backslash does not round-trip.
     */
    public function testEnumValueWithABackslashThrows(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/enum value .* must not contain/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'size',
            type: ExtraPropertyType::CHOICE,
            enumValues: ['ok', 'a\\b'],
        );
    }

    public function testPlainWordingsAreAccepted(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            labelWording: "Video link (it's > 5 chars, éàü)",
            descriptionWording: "Line one\nline two\ttabbed",
        );

        $this->assertSame("Video link (it's > 5 chars, éàü)", $definition->getLabelWording());
        $this->assertSame("Line one\nline two\ttabbed", $definition->getDescriptionWording());
    }

    /**
     * @dataProvider invalidTranslationDomainProvider
     */
    public function testMalformedTranslationDomainThrows(string $domain): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelDomain .* must be a translation domain/');

        new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link', labelWording: 'Video link', labelDomain: $domain);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidTranslationDomainProvider(): array
    {
        return [
            'ICU formatter suffix' => ['Admin.Global+intl-icu'],
            'single segment' => ['Admin'],
            'too many segments' => ['A.B.C.D'],
            'lowercase segment' => ['admin.global'],
            'space' => ['Admin. Global'],
            'slash' => ['Admin/Global'],
            'path traversal' => ['../Admin.Global'],
            'trailing dot' => ['Admin.Global.'],
        ];
    }

    public function testAModuleOwnedDefinitionCannotDeclareACoreDomain(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/must belong to the module/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            moduleName: 'demoextrafield',
            labelWording: 'Save',
            labelDomain: 'Admin.Actions',
        );
    }

    public function testAModuleOwnedDefinitionCannotDeclareAnotherModulesDomain(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/must belong to the module \("Modules\.Demoextrafield\./');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            moduleName: 'demoextrafield',
            labelWording: 'Filter',
            labelDomain: 'Modules.Facetedsearch.Admin',
        );
    }

    public function testDomainsAreAcceptedWhereTheyBelong(): void
    {
        $module = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'a',
            moduleName: 'demoextrafield',
            labelWording: 'Video link',
            labelDomain: 'Modules.Demoextrafield.Admin',
            descriptionDomain: 'Modules.Demoextrafield.Help',
        );
        $this->assertSame('Modules.Demoextrafield.Admin', $module->getLabelDomain());

        // A core-owned definition may reuse a core domain: its wording then resolves to the
        // existing translation instead of overriding it (see TranslatorLanguageLoader).
        $core = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'b', labelWording: 'Name', labelDomain: 'Admin.Global');
        $this->assertSame('Admin.Global', $core->getLabelDomain());
    }

    // -------------------------------------------------------------------------
    // fromRow hydration
    // -------------------------------------------------------------------------

    public function testDefaultsWhenMetadataKeysAreAbsent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW);

        $this->assertTrue($definition->isNullable());
        $this->assertNull($definition->getEnumValues());
    }

    /**
     * nullable, enum_values and primary_key_name are synthetic keys injected by the repository
     * from the live column structure; they are not persisted in the registry table.
     */
    public function testNullableComesFromRow(): void
    {
        $notNull = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['nullable' => false]);
        $nullable = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['nullable' => true]);

        $this->assertFalse($notNull->isNullable());
        $this->assertTrue($nullable->isNullable());
    }

    public function testEnumValuesComeFromRow(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['enum_values' => ['box', 'bag', 'pallet']]);

        $this->assertSame(ExtraPropertyType::CHOICE, $definition->getType());
        $this->assertSame(['box', 'bag', 'pallet'], $definition->getEnumValues());
    }

    public function testEmptyOrInvalidEnumValuesFallBackToNull(): void
    {
        $empty = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['enum_values' => []]);
        $invalid = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['enum_values' => 'not-an-array']);

        $this->assertNull($empty->getEnumValues());
        $this->assertNull($invalid->getEnumValues());
    }

    /**
     * Decoding is the repository's job (it owns the normalizer and can report a rejected constraint
     * with its row): fromRow() only carries already decoded constraints through.
     */
    public function testConstraintsAreCarriedThroughAlreadyDecoded(): void
    {
        $row = self::BASE_ROW + ['constraints' => [new Assert\Url(), new Assert\Length(['max' => 50])]];

        $constraints = ExtraPropertyDefinition::fromRow($row)->getConstraints();

        $this->assertIsArray($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Url::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Length::class, $constraints[1]);
        $this->assertSame(50, $constraints[1]->max);
    }

    public function testConstraintsAbsentOrEmptyFallBackToNull(): void
    {
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW)->getConstraints(), 'No constraints key → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => []])->getConstraints(), 'Empty list → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => null])->getConstraints(), 'Null → null.');
        $this->assertNull(
            ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => "Url\nLength(max: 50)"])->getConstraints(),
            'A raw stored string is not decoded here: that belongs to the repository.'
        );
    }

    /**
     * The BOOL '0' row pins a historical bug: a naive (string)/(bool) round-trip lost a false default on reload.
     *
     * @dataProvider defaultValueRowProvider
     */
    public function testDefaultValueIsHydratedWithItsDeclaredType(string $type, ?string $rawDefault, int|float|string|bool|null $expected): void
    {
        $row = array_merge(self::BASE_ROW, ['type' => $type]);
        if (null !== $rawDefault) {
            $row['default_value'] = $rawDefault;
        }

        $this->assertSame($expected, ExtraPropertyDefinition::fromRow($row)->getDefaultValue());
    }

    public static function defaultValueRowProvider(): iterable
    {
        yield 'bool 0 hydrates to false' => ['bool', '0', false];
        yield 'bool 1 hydrates to true' => ['bool', '1', true];
        yield 'int 0 hydrates to int zero' => ['int', '0', 0];
        yield 'float hydrates to float' => ['float', '1.5', 1.5];
        yield 'string passes through' => ['string', 'hello', 'hello'];
        yield 'json default stays a string' => ['json', '{"a":1}', '{"a":1}'];
        yield 'absent key hydrates to null' => ['string', null, null];
        yield 'empty cell hydrates to null' => ['string', '', null];
    }

    /**
     * The stored table_name is authoritative: hydration never re-resolves it.
     */
    public function testTableNameComesFromRowWhenPresent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['table_name' => 'some_physical_table']);

        $this->assertSame('some_physical_table', $definition->getTableName());
    }

    public function testPrimaryKeyNameComesFromRowWhenPresent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['primary_key_name' => 'id_custom']);

        $this->assertSame('id_custom', $definition->getPrimaryKeyName());
    }

    /**
     * Rows predating the table_name column (or without an introspected PK) fall back to the
     * constructor resolution, detailed in the naming section.
     */
    public function testTableAndPrimaryKeyFallBackToConstructorResolutionWhenAbsent(): void
    {
        $combination = ExtraPropertyDefinition::fromRow(['entity_name' => 'combination', 'property_name' => 'field']);
        $bareTable = ExtraPropertyDefinition::fromRow(['entity_name' => 'my_custom_table', 'property_name' => 'field']);

        $this->assertSame('product_attribute', $combination->getTableName());
        $this->assertSame('id_product_attribute', $combination->getPrimaryKeyName());
        $this->assertSame('my_custom_table', $bareTable->getTableName());
        $this->assertSame('id_my_custom_table', $bareTable->getPrimaryKeyName());
    }

    // -------------------------------------------------------------------------
    // Naming
    // -------------------------------------------------------------------------

    public function testCoreModuleKeyConstant(): void
    {
        $this->assertSame('_core', ExtraPropertyDefinition::CORE_MODULE_KEY);
    }

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
     * Both tables are built from the PHYSICAL table, never the logical entity name.
     *
     * @dataProvider scopeTableNameProvider
     */
    public function testBaseAndExtraTableNamePerScope(ExtraPropertyScope $scope, string $expectedBaseTable, string $expectedExtraTable): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'combination', propertyName: 'field', scope: $scope);

        $this->assertSame($expectedBaseTable, $definition->getBaseTableName());
        $this->assertSame($expectedExtraTable, $definition->getExtraTableName());
    }

    public static function scopeTableNameProvider(): iterable
    {
        yield 'common' => [ExtraPropertyScope::COMMON, 'product_attribute', 'product_attribute_extra'];
        yield 'lang' => [ExtraPropertyScope::LANG, 'product_attribute_lang', 'product_attribute_extra_lang'];
        yield 'shop' => [ExtraPropertyScope::SHOP, 'product_attribute_shop', 'product_attribute_extra_shop'];
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
        // A property is unique per module + name, so the scope is not part of the field name.
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
     * getModuleName() is null for every core spelling, getNormalizedModuleKey() is its '_core'-keyed
     * counterpart and isModuleOwned() the boolean view: callers never re-normalize.
     *
     * @dataProvider moduleNameNormalizationProvider
     */
    public function testModuleNameIsNormalizedAtConstruction(?string $inputModuleName, ?string $expectedModuleName, string $expectedKey, bool $expectedOwned): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'field', moduleName: $inputModuleName);

        $this->assertSame($expectedModuleName, $definition->getModuleName());
        $this->assertSame($expectedKey, $definition->getNormalizedModuleKey());
        $this->assertSame($expectedOwned, $definition->isModuleOwned());
    }

    public static function moduleNameNormalizationProvider(): array
    {
        return [
            'null stays null' => [null, null, ExtraPropertyDefinition::CORE_MODULE_KEY, false],
            'empty string normalized to null' => ['', null, ExtraPropertyDefinition::CORE_MODULE_KEY, false],
            '_core sentinel normalized to null' => [ExtraPropertyDefinition::CORE_MODULE_KEY, null, ExtraPropertyDefinition::CORE_MODULE_KEY, false],
            'module name kept as-is' => ['ps_mymodule', 'ps_mymodule', 'ps_mymodule', true],
            'another module' => ['demomodule', 'demomodule', 'demomodule', true],
        ];
    }

    /**
     * Resolution: explicit/introspected value, then the entity's own ObjectModel
     * $definition['primary'], then 'id_' + the normalized entity name.
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
            // ManufacturerAddress only INHERITS Address::$definition: must fall back to the convention.
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
     * Every accepted spelling of an entity converges onto ONE canonical (entityName, tableName) pair.
     * The canonical map runs before class resolution, which keeps the modern ProductAttribute class
     * (the renamed Attribute entity, table `attribute`) from hijacking the combination spellings.
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
        // Normalization to lower snake_case, before any canonicalization.
        yield 'uppercase entity is lowercased' => ['Product', 'product', 'product'];
        yield 'CamelCase entity is tableized' => ['TaxRulesGroup', 'tax_rules_group', 'tax_rules_group'];
        yield 'hyphen in entity becomes underscore' => ['my-entity', 'my_entity', 'my_entity'];
        yield 'alphanumeric with digits' => ['entity123', 'entity123', 'entity123'];
        yield 'underscore separators' => ['ps_product', 'ps_product', 'ps_product'];

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

        // Modern CQRS domain names are canonical while the physical table stays the legacy one.
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

        // Table spellings of class/table disparities converge, otherwise each would register a
        // second entity name on the same storage table.
        yield 'lang table spelling' => ['lang', 'language', 'lang'];
        yield 'language' => ['language', 'language', 'lang'];
        yield 'connections table spelling' => ['connections', 'connection', 'connections'];
        yield 'webservice_account table spelling' => ['webservice_account', 'webservice_key', 'webservice_account'];

        // BO grid/menu spelling converges on the entity behind the grid.
        yield 'merchandise_return grid spelling' => ['merchandise_return', 'order_return', 'order_return'];

        // No matching ObjectModel class: bare-table registration, the name is the table.
        yield 'unknown table' => ['my_custom_table', 'my_custom_table', 'my_custom_table'];

        // Link is not an ObjectModel; ManufacturerAddress only INHERITS Address::$definition.
        yield 'non-ObjectModel class name' => ['link', 'link', 'link'];
        yield 'class with inherited definition' => ['manufacturer_address', 'manufacturer_address', 'manufacturer_address'];
    }

    /**
     * Explicit values are the escape hatch for third-party ObjectModels whose entity name differs from their table.
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
     * The controller name is the BO permission subject (e.g. grid toggle): security-relevant.
     * A name matching no tab is deny-safe (Access::isGranted() grants nothing for unknown subjects).
     *
     * @dataProvider controllerNameProvider
     */
    public function testControllerNameResolution(string $entityName, string $expected): void
    {
        $definition = new ExtraPropertyDefinition(entityName: $entityName, propertyName: 'field');

        $this->assertSame($expected, $definition->getControllerName());
        // No override given: nothing to persist, the deduction stays live.
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
        yield 'combination (managed on the product page)' => ['combination', 'AdminProducts'];
        yield 'tax_rule (grid on the tax rules group page)' => ['tax_rule', 'AdminTaxRulesGroup'];
        // Singular tabs — no migrated grid today, mapped so a future one cannot 403.
        yield 'shop_group (singular tab)' => ['shop_group', 'AdminShopGroup'];
        yield 'shop_url (singular tab)' => ['shop_url', 'AdminShopUrl'];
        yield 'feature_flag (singular tab)' => ['feature_flag', 'AdminFeatureFlag'];

        // The map / convention applies AFTER the entity alias, whatever the registered spelling.
        yield 'cms_page' => ['cms_page', 'AdminCmsContent'];
        yield 'cms legacy spelling' => ['cms', 'AdminCmsContent'];
        yield 'credit_slip' => ['credit_slip', 'AdminSlip'];
        yield 'discount' => ['discount', 'AdminCartRules'];
        yield 'cart_rule legacy spelling' => ['cart_rule', 'AdminCartRules'];
        yield 'title' => ['title', 'AdminGenders'];
        yield 'gender legacy spelling' => ['gender', 'AdminGenders'];
        yield 'merchandise_return grid spelling' => ['merchandise_return', 'AdminReturn'];
        yield 'mail (email logs entity)' => ['mail', 'AdminEmails'];
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
     * A value equal to the deduction collapses to null, so re-registering with the conventional
     * name also cleans a previously stored override.
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
        $this->assertNull(ExtraPropertyDefinition::fromRow([
            'entity_name' => 'product',
            'property_name' => 'field',
        ])->getControllerNameOverride());
    }

    // -------------------------------------------------------------------------
    // Placement accessors (entry grammar itself: AssociationEntryParserTest)
    // -------------------------------------------------------------------------

    /**
     * @dataProvider formEntryProvider
     *
     * @param array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null} $expected
     */
    public function testGetFormEntryDelegatesToTheParser(string $entry, array $expected): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            associatedForms: [$entry],
            labelWording: 'Test',
        );

        $this->assertSame($expected, $definition->getFormEntry($expected['formId']));
    }

    public static function formEntryProvider(): array
    {
        return [
            'anchor placement resolved to parent path + anchor' => [
                'product:options.suppliers:before',
                ['formId' => 'product', 'mode' => 'before', 'path' => 'options', 'anchor' => 'suppliers'],
            ],
            'compound form id with container path' => [
                'manufacturer_address:city',
                ['formId' => 'manufacturer_address', 'mode' => null, 'path' => 'city', 'anchor' => null],
            ],
        ];
    }

    public function testGetFormEntryPicksTheEntryOfTheRequestedForm(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            associatedForms: ['product:options', 'category:description:after', 'manufacturer'],
            labelWording: 'Test',
        );

        $this->assertSame(['formId' => 'category', 'mode' => 'after', 'path' => '', 'anchor' => 'description'], $definition->getFormEntry('category'));
        $this->assertSame(['formId' => 'manufacturer', 'mode' => null, 'path' => null, 'anchor' => null], $definition->getFormEntry('manufacturer'));
        $this->assertSame('options', $definition->getFormEntry('product')['path'] ?? null);
        $this->assertNull($definition->getFormEntry('supplier'));
        // Form ids are matched exactly, never by prefix.
        $this->assertNull($definition->getFormEntry('prod'));
    }

    public function testGetFormEntryIsNullWithoutAssociatedForms(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'test_field');

        $this->assertNull($definition->getAssociatedForms());
        $this->assertNull($definition->getFormEntry('product'));
    }

    /**
     * @dataProvider gridEntryProvider
     *
     * @param array{gridId: string, columnId: string|null, mode: 'before'|'after'|null} $expected
     */
    public function testGetGridEntryDelegatesToTheParser(string $entry, array $expected): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            associatedGrids: [$entry],
            labelWording: 'Test',
        );

        $this->assertSame($expected, $definition->getGridEntry($expected['gridId']));
    }

    public static function gridEntryProvider(): array
    {
        return [
            'grid with column and before mode' => [
                'product:reference:before',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'before'],
            ],
            'compound grid id with column (default mode after)' => [
                'manufacturer_address:city',
                ['gridId' => 'manufacturer_address', 'columnId' => 'city', 'mode' => 'after'],
            ],
        ];
    }

    public function testGetGridEntryPicksTheEntryOfTheRequestedGrid(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            associatedGrids: ['product:reference', 'customer:email:before', 'order'],
            labelWording: 'Test',
        );

        $this->assertSame(['gridId' => 'customer', 'columnId' => 'email', 'mode' => 'before'], $definition->getGridEntry('customer'));
        $this->assertSame(['gridId' => 'order', 'columnId' => null, 'mode' => null], $definition->getGridEntry('order'));
        $this->assertSame('reference', $definition->getGridEntry('product')['columnId'] ?? null);
        $this->assertNull($definition->getGridEntry('category'));
        $this->assertNull($definition->getGridEntry('prod'));
    }

    public function testGetGridEntryIsNullWithoutAssociatedGrids(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'test_field');

        $this->assertNull($definition->getAssociatedGrids());
        $this->assertNull($definition->getGridEntry('product'));
    }

    // -------------------------------------------------------------------------
    // Copy-with methods
    // -------------------------------------------------------------------------

    public function testWithModuleNameSetsTheModuleAndItsDerivedNames(): void
    {
        $core = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link');

        $owned = $core->withModuleName('ps_mymodule');

        $this->assertSame('ps_mymodule', $owned->getModuleName());
        $this->assertSame('ps_mymodule', $owned->getNormalizedModuleKey());
        $this->assertTrue($owned->isModuleOwned());
        $this->assertSame('ps_mymodule_video_link', $owned->getStorageColumnName());
        $this->assertSame('extra_ps_mymodule_video_link', $owned->getFieldName());
        // Immutability: the original is untouched.
        $this->assertNull($core->getModuleName());
        $this->assertSame('video_link', $core->getStorageColumnName());
    }

    public function testWithModuleNameCoreSentinelKeepsTheDefinitionCoreOwned(): void
    {
        $owned = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link', moduleName: 'ps_mymodule');

        $core = $owned->withModuleName(ExtraPropertyDefinition::CORE_MODULE_KEY);

        $this->assertNull($core->getModuleName());
        $this->assertFalse($core->isModuleOwned());
    }

    public function testWithModuleNameCopiesEveryOtherField(): void
    {
        $definition = $this->buildFullDefinition();

        $this->assertEquals(
            $definition->withOverrides(['moduleName' => 'ps_demoextrafield']),
            $definition->withModuleName('ps_demoextrafield')
        );
    }

    public function testWithModuleNameRejectsAnInvalidModuleName(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/moduleName .* is not a valid PrestaShop module name/');

        (new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link'))->withModuleName('my module');
    }

    /**
     * @dataProvider overrideProvider
     */
    public function testWithOverridesReplacesEachConstructorParameter(string $key, mixed $value, string $getter, mixed $expected): void
    {
        $definition = $this->buildFullDefinition();
        $this->assertNotSame($expected, $definition->$getter());

        $this->assertSame($expected, $definition->withOverrides([$key => $value])->$getter());
    }

    public static function overrideProvider(): iterable
    {
        $constraints = [new Assert\NotBlank()];

        yield 'entityName' => ['entityName', 'category', 'getEntityName', 'category'];
        yield 'propertyName' => ['propertyName', 'color', 'getPropertyName', 'color'];
        yield 'type' => ['type', ExtraPropertyType::INT, 'getType', ExtraPropertyType::INT];
        yield 'scope' => ['scope', ExtraPropertyScope::LANG, 'getScope', ExtraPropertyScope::LANG];
        // The ps_ prefix is dropped from the module's translation domain, so the fixture's domains stay valid.
        yield 'moduleName' => ['moduleName', 'ps_demoextrafield', 'getModuleName', 'ps_demoextrafield'];
        yield 'enumValues' => ['enumValues', ['a', 'b'], 'getEnumValues', ['a', 'b']];
        yield 'defaultValue' => ['defaultValue', 'other', 'getDefaultValue', 'other'];
        yield 'nullable' => ['nullable', false, 'isNullable', false];
        yield 'required' => ['required', true, 'isRequired', true];
        yield 'size' => ['size', 64, 'getSize', 64];
        yield 'sqlIndex' => ['sqlIndex', ExtraPropertySqlIndex::UNIQUE, 'getSqlIndex', ExtraPropertySqlIndex::UNIQUE];
        yield 'displayFront' => ['displayFront', true, 'isDisplayFront', true];
        yield 'associatedForms' => ['associatedForms', ['category:description'], 'getAssociatedForms', ['category:description']];
        yield 'associatedGrids' => ['associatedGrids', ['category'], 'getAssociatedGrids', ['category']];
        yield 'associatedApis' => ['associatedApis', ['/categories/{categoryId}'], 'getAssociatedApis', ['/categories/{categoryId}']];
        yield 'formType' => ['formType', TextareaType::class, 'getFormType', TextareaType::class];
        yield 'formOptions' => ['formOptions', ['required' => false], 'getFormOptions', ['required' => false]];
        yield 'constraints' => ['constraints', $constraints, 'getConstraints', $constraints];
        yield 'labelWording' => ['labelWording', 'Other label', 'getLabelWording', 'Other label'];
        yield 'labelDomain' => ['labelDomain', 'Modules.Demoextrafield.Shop', 'getLabelDomain', 'Modules.Demoextrafield.Shop'];
        yield 'descriptionWording' => ['descriptionWording', 'Other help', 'getDescriptionWording', 'Other help'];
        yield 'descriptionDomain' => ['descriptionDomain', 'Modules.Demoextrafield.Front', 'getDescriptionDomain', 'Modules.Demoextrafield.Front'];
        yield 'multiShop' => ['multiShop', true, 'isMultiShop', true];
        yield 'associatedShopIds' => ['associatedShopIds', [2, 3], 'getAssociatedShopIds', [2, 3]];
        yield 'tableName' => ['tableName', 'my_table', 'getTableName', 'my_table'];
        yield 'primaryKeyName' => ['primaryKeyName', 'id_custom', 'getPrimaryKeyName', 'id_custom'];
        yield 'controllerName' => ['controllerName', 'AdminCustom', 'getControllerNameOverride', 'AdminCustom'];
    }

    /**
     * The reason withOverrides() uses array_key_exists() rather than ??: a null override must win.
     *
     * @dataProvider nullOverrideProvider
     */
    public function testWithOverridesCanResetANullableFieldToNull(string $key, string $getter): void
    {
        $definition = $this->buildFullDefinition();
        $this->assertNotNull($definition->$getter());

        $this->assertNull($definition->withOverrides([$key => null])->$getter());
    }

    public static function nullOverrideProvider(): iterable
    {
        yield 'moduleName' => ['moduleName', 'getModuleName'];
        yield 'enumValues' => ['enumValues', 'getEnumValues'];
        yield 'defaultValue' => ['defaultValue', 'getDefaultValue'];
        yield 'size' => ['size', 'getSize'];
        yield 'associatedApis' => ['associatedApis', 'getAssociatedApis'];
        yield 'formType' => ['formType', 'getFormType'];
        yield 'formOptions' => ['formOptions', 'getFormOptions'];
        yield 'constraints' => ['constraints', 'getConstraints'];
        yield 'labelDomain' => ['labelDomain', 'getLabelDomain'];
        yield 'descriptionWording' => ['descriptionWording', 'getDescriptionWording'];
        yield 'descriptionDomain' => ['descriptionDomain', 'getDescriptionDomain'];
        yield 'associatedShopIds' => ['associatedShopIds', 'getAssociatedShopIds'];
        yield 'controllerName' => ['controllerName', 'getControllerNameOverride'];
    }

    public function testWithOverridesCanClearPlacementsAndLabelTogether(): void
    {
        $cleared = $this->buildFullDefinition()->withOverrides([
            'associatedForms' => null,
            'associatedGrids' => null,
            'labelWording' => null,
        ]);

        $this->assertNull($cleared->getAssociatedForms());
        $this->assertNull($cleared->getAssociatedGrids());
        $this->assertNull($cleared->getLabelWording());
    }

    /**
     * Resetting the explicit table / PK falls back to the constructor resolution.
     */
    public function testWithOverridesNullTableAndPrimaryKeyReResolve(): void
    {
        $definition = $this->buildFullDefinition()->withOverrides(['tableName' => null, 'primaryKeyName' => null]);

        $this->assertSame('product', $definition->getTableName());
        $this->assertSame('id_product', $definition->getPrimaryKeyName());
    }

    /**
     * @dataProvider boolCastOverrideProvider
     */
    public function testWithOverridesCastsBooleanFields(string $key, mixed $value, string $getter, bool $expected): void
    {
        $this->assertSame($expected, $this->buildFullDefinition()->withOverrides([$key => $value])->$getter());
    }

    public static function boolCastOverrideProvider(): iterable
    {
        yield 'nullable from int 0' => ['nullable', 0, 'isNullable', false];
        yield 'nullable from string "0"' => ['nullable', '0', 'isNullable', false];
        yield 'nullable from null' => ['nullable', null, 'isNullable', false];
        yield 'required from int 1' => ['required', 1, 'isRequired', true];
        yield 'required from string "1"' => ['required', '1', 'isRequired', true];
        yield 'displayFront from non-empty string' => ['displayFront', 'yes', 'isDisplayFront', true];
        yield 'displayFront from empty string' => ['displayFront', '', 'isDisplayFront', false];
    }

    public function testWithOverridesLeavesTheOriginalUnchanged(): void
    {
        $definition = $this->buildFullDefinition();
        $snapshot = clone $definition;

        $copy = $definition->withOverrides([
            'propertyName' => 'color',
            'displayFront' => true,
            'associatedShopIds' => null,
        ]);

        $this->assertNotSame($definition, $copy);
        $this->assertEquals($snapshot, $definition);
        $this->assertSame('color', $copy->getPropertyName());
    }

    public function testWithOverridesRunsTheConstructorValidation(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/propertyName/');

        $this->buildFullDefinition()->withOverrides(['propertyName' => 'bad name']);
    }

    public function testWithOverridesEnforcesCrossFieldRules(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelWording is required/');

        $this->buildFullDefinition()->withOverrides(['labelWording' => null]);
    }

    /**
     * Keys must match constructor parameter names: registry (snake_case) column names are not understood.
     */
    public function testWithOverridesIgnoresUnknownKeys(): void
    {
        $definition = $this->buildFullDefinition();

        $this->assertEquals($definition, $definition->withOverrides(['unknown' => 'x', 'entity_name' => 'category', 'display_front' => true]));
    }

    /**
     * Includes the resolved/derived state (tableName, primaryKeyName, controllerName, associatedShopIds,
     * multiShop, normalized module key) that a naive copy would lose or re-resolve.
     */
    public function testEmptyOverridesReturnAnEquivalentDefinition(): void
    {
        $definition = $this->buildFullDefinition();

        $copy = $definition->withOverrides([]);

        $this->assertNotSame($definition, $copy);
        $this->assertEquals($definition, $copy);
        $this->assertSame('my_product_table', $copy->getTableName());
        $this->assertSame('id_my_product', $copy->getPrimaryKeyName());
        $this->assertSame('AdminMyProducts', $copy->getControllerNameOverride());
        $this->assertSame([1, 4], $copy->getAssociatedShopIds());
        $this->assertFalse($copy->isMultiShop());
    }

    public function testEmptyOverridesKeepAClassResolvedTable(): void
    {
        $combination = ExtraPropertyDefinition::fromRow(['entity_name' => 'combination', 'property_name' => 'field', 'multi_shop' => '1']);

        $copy = $combination->withOverrides([]);

        $this->assertEquals($combination, $copy);
        $this->assertSame('product_attribute', $copy->getTableName());
        $this->assertTrue($copy->isMultiShop());
    }

    // -------------------------------------------------------------------------
    // API matching / ownership
    // -------------------------------------------------------------------------

    /**
     * @dataProvider matchesApiProvider
     */
    public function testMatchesApi(string $uriTemplate, string $method, bool $expected): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedApis: [
                '/products/{productId}:GET,PATCH',
                '/categories/{categoryId}',
                'customers/{customerId}/',
                '/orders/{orderId}:post',
            ],
        );

        $this->assertSame($expected, $definition->matchesApi($uriTemplate, $method));
    }

    public static function matchesApiProvider(): iterable
    {
        yield 'listed method' => ['/products/{productId}', 'GET', true];
        yield 'second listed method' => ['/products/{productId}', 'PATCH', true];
        yield 'method is case-insensitive' => ['/products/{productId}', 'patch', true];
        yield 'unlisted method' => ['/products/{productId}', 'DELETE', false];
        yield 'no modifier matches every method' => ['/categories/{categoryId}', 'DELETE', true];
        yield 'lowercase modifier in the entry' => ['/orders/{orderId}', 'POST', true];
        yield 'entry without leading slash and with trailing slash' => ['/customers/{customerId}', 'PUT', true];
        yield 'requested template with trailing slash' => ['/products/{productId}/', 'GET', true];
        yield 'requested template without leading slash' => ['products/{productId}', 'GET', true];
        yield 'collection path is another template' => ['/products', 'GET', false];
        yield 'placeholder name is part of the template' => ['/products/{id}', 'GET', false];
        yield 'sub-resource is another template' => ['/products/{productId}/images', 'GET', false];
    }

    public function testMatchesApiIsFalseWithoutAssociatedApis(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'video_link');

        $this->assertFalse($definition->matchesApi('/products/{productId}', 'GET'));
    }

    /**
     * No core caller: kept as public API for modules.
     */
    public function testGetApiEntries(): void
    {
        $this->assertSame([], (new ExtraPropertyDefinition(entityName: 'product', propertyName: 'a'))->getApiEntries());

        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'b',
            associatedApis: ['/products/{productId}:get, patch', 'categories/{categoryId}/'],
        );

        $this->assertSame(
            [
                ['path' => '/products/{productId}', 'methods' => ['GET', 'PATCH']],
                ['path' => '/categories/{categoryId}', 'methods' => null],
            ],
            $definition->getApiEntries()
        );
    }

    /**
     * Module-owned, with non-default values in every field, so each override and each copy is observable.
     */
    private function buildFullDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            type: ExtraPropertyType::CHOICE,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            enumValues: ['small', 'large'],
            defaultValue: 'small',
            nullable: true,
            required: false,
            size: 128,
            sqlIndex: ExtraPropertySqlIndex::KEY,
            displayFront: false,
            associatedForms: ['product:options'],
            associatedGrids: ['product:reference'],
            associatedApis: ['/products/{productId}:GET'],
            formType: TextType::class,
            formOptions: ['attr' => ['placeholder' => 'x']],
            constraints: [new Assert\Url()],
            labelWording: 'Video link',
            labelDomain: 'Modules.Demoextrafield.Admin',
            descriptionWording: 'Help',
            descriptionDomain: 'Modules.Demoextrafield.Help',
            multiShop: false,
            associatedShopIds: [1, 4],
            tableName: 'my_product_table',
            primaryKeyName: 'id_my_product',
            controllerName: 'AdminMyProducts',
        );
    }
}
