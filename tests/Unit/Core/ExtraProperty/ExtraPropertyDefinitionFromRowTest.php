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
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Covers the schema-deduced attributes flowing through ExtraPropertyDefinition::fromRow():
 * nullable and enum_values are synthetic row keys injected by the repository from the live
 * column structure (they are not persisted in the registry table).
 */
class ExtraPropertyDefinitionFromRowTest extends TestCase
{
    private const BASE_ROW = [
        'entity_name' => 'product',
        'property_name' => 'packaging_type',
        'type' => 'choice',
        'scope' => 'common',
        'module_name' => 'demoextrafield',
    ];

    public function testDefaultsWhenMetadataKeysAreAbsent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW);

        $this->assertTrue($definition->isNullable());
        $this->assertNull($definition->getEnumValues());
    }

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

    public function testConstraintsRoundTripFromJsonRow(): void
    {
        $json = ExtraPropertyConstraintMapper::constraintsToJson([new Assert\Url(), new Assert\Length(['max' => 50])]);
        $row = self::BASE_ROW + ['constraints' => $json];

        $constraints = ExtraPropertyDefinition::fromRow($row)->getConstraints();

        $this->assertIsArray($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Url::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Length::class, $constraints[1]);
    }

    public function testConstraintsRoundTripFromLegacySerializedRow(): void
    {
        // Legacy rows written before the JSON migration use PHP serialize().
        // With allowed_classes => false (CWE-502 hardening), objects become
        // __PHP_Incomplete_Class and are filtered out — legacy rows lose their
        // constraints until re-saved through the BO form or a module re-register.
        // This is the intentional trade-off: no __wakeup(), no object instantiation.
        $row = self::BASE_ROW + ['constraints' => serialize([new Assert\Url(), new Assert\Length(['max' => 50])])];

        $constraints = ExtraPropertyDefinition::fromRow($row)->getConstraints();

        $this->assertNull($constraints, 'Legacy serialized constraints are dropped (allowed_classes => false) until re-saved as JSON.');
    }

    public function testConstraintsAbsentOrUnusableFallBackToNull(): void
    {
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW)->getConstraints(), 'No constraints key → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => ''])->getConstraints(), 'Empty string → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => 'not-serialized'])->getConstraints(), 'Unserializable garbage → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => serialize(['x', 123])])->getConstraints(), 'Non-Constraint entries are filtered out → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => '{invalid json'])->getConstraints(), 'Invalid JSON → null.');
    }

    /**
     * @dataProvider enumColumnTypeProvider
     */
    public function testParseEnumValuesFromSqlColumnType(string $sqlColumnType, ?array $expected): void
    {
        $this->assertSame($expected, ColumnDefinitionMapper::parseEnumValues($sqlColumnType));
    }

    public static function enumColumnTypeProvider(): iterable
    {
        yield 'plain enum' => ["enum('box','bag','pallet')", ['box', 'bag', 'pallet']];
        yield 'uppercase enum' => ["ENUM('a','b')", ['a', 'b']];
        yield 'escaped quote in literal' => ["enum('it''s','plain')", ["it's", 'plain']];
        yield 'varchar is not enum' => ['varchar(255)', null];
        yield 'int is not enum' => ['int(11)', null];
        yield 'set is not enum' => ["set('a','b')", null];
    }
}
