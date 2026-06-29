<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
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
    // Data providers
    // -------------------------------------------------------------------------

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
            'CamelCase entity is tableized' => ['ProductAttribute', 'field', 'product_attribute'],
            'hyphen in entity becomes underscore' => ['my-entity', 'field', 'my_entity'],
            'hyphen in property' => ['product', 'my-field', 'product'],
            'alphanumeric with digits' => ['entity123', 'field456', 'entity123'],
            'underscore separators' => ['ps_product', 'extra_video_link', 'ps_product'],
        ];
    }
}
