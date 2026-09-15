<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use stdClass;

/**
 * Verifies that ExtraPropertyDefinition constructor rejects invalid inputs.
 *
 * Covers: empty entityName/propertyName, SQL-identifier violations, and the
 * associatedForms/associatedGrids format / labelWording requirement guards.
 */
class ExtraPropertyDefinitionConstructorTest extends TestCase
{
    // -------------------------------------------------------------------------
    // entityName validation
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

    // -------------------------------------------------------------------------
    // propertyName validation
    // -------------------------------------------------------------------------

    /**
     * @dataProvider invalidSqlIdentifierProvider
     */
    public function testInvalidPropertyNameThrows(string $invalidValue): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/propertyName/');

        new ExtraPropertyDefinition(entityName: 'product', propertyName: $invalidValue);
    }

    // -------------------------------------------------------------------------
    // constraints validation
    // -------------------------------------------------------------------------

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
        $url = new \Symfony\Component\Validator\Constraints\Url();
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            constraints: [$url],
        );

        $this->assertSame([$url], $definition->getConstraints());
    }

    // -------------------------------------------------------------------------
    // Valid identifiers must be accepted
    // -------------------------------------------------------------------------

    /**
     * @dataProvider validIdentifierProvider
     */
    public function testValidIdentifiersAccepted(string $entityName, string $propertyName, string $expectedEntityName): void
    {
        $definition = new ExtraPropertyDefinition(entityName: $entityName, propertyName: $propertyName);

        // entityName is normalized to lower snake_case at construction; propertyName is kept as-is.
        $this->assertSame($expectedEntityName, $definition->getEntityName());
        $this->assertSame($propertyName, $definition->getPropertyName());
    }

    // -------------------------------------------------------------------------
    // Storage column safety contract: every constructed definition yields a
    // SQL-safe storage column name (1–64 chars, [A-Za-z0-9_]) — DDL consumers
    // (ExtraPropertySchemaManager) embed it in SQL without re-validating.
    // -------------------------------------------------------------------------

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
        // Hyphens are valid in identifiers but not in unquoted SQL column names.
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video-link',
            moduleName: 'my-module',
        );

        $this->assertSame('my_module_video_link', $definition->getStorageColumnName());
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_]{1,64}$/', $definition->getStorageColumnName());
    }

    // -------------------------------------------------------------------------
    // associatedForms / labelWording guards (pre-existing, not covered elsewhere)
    // -------------------------------------------------------------------------

    public function testAssociatedFormsRequiresLabelWording(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/labelWording is required/');

        new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            associatedForms: ['product'],
            // labelWording intentionally omitted
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
            // labelWording intentionally omitted
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

    // -------------------------------------------------------------------------
    // Author-controlled display texts and translation domains
    // -------------------------------------------------------------------------

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

    /**
     * Enum literals are read back from the live SQL ENUM column, where a backslash does not
     * round-trip: it is refused up front, like a "<".
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
    // Data providers
    // -------------------------------------------------------------------------

    /**
     * Texts that could open a tag or carry a control character, refused wherever an author controls
     * a displayed text (wording, enum literal, choice label, constraint message).
     *
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

    /**
     * Values that are empty or contain characters outside [a-zA-Z0-9_-] and therefore must be rejected.
     *
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

    /**
     * Values allowed by the [a-zA-Z0-9_-]+ pattern.
     *
     * @return array<string, array{string, string}>
     */
    public static function validIdentifierProvider(): array
    {
        return [
            'simple lowercase' => ['product', 'video_link', 'product'],
            'uppercase entity is lowercased' => ['Product', 'VideoLink', 'product'],
            // ProductAttribute tableizes to product_attribute, which the canonical map
            // then converges onto the combination entity (see CANONICAL_ENTITY_NAMES).
            'CamelCase entity is tableized then canonicalized' => ['ProductAttribute', 'field', 'combination'],
            'CamelCase entity is tableized' => ['TaxRulesGroup', 'field', 'tax_rules_group'],
            'hyphen in entity becomes underscore' => ['my-entity', 'field', 'my_entity'],
            'hyphen in property' => ['product', 'my-field', 'product'],
            'alphanumeric with digits' => ['entity123', 'field456', 'entity123'],
            'underscore separators' => ['ps_product', 'extra_video_link', 'ps_product'],
        ];
    }
}
