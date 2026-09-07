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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Covers the schema-deduced attributes flowing through ExtraPropertyDefinition::fromRow():
 * nullable, enum_values and primary_key_name are synthetic row keys injected by the
 * repository from the live column structure (they are not persisted in the registry
 * table), while table_name and default_value are persisted registry columns whose
 * hydration carries specific rules (stored-resolved table, typed default).
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

    public function testConstraintsRoundTripFromSerializedRow(): void
    {
        $row = self::BASE_ROW + ['constraints' => serialize([new Assert\Url(), new Assert\Length(['max' => 50])])];

        $constraints = ExtraPropertyDefinition::fromRow($row)->getConstraints();

        $this->assertIsArray($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Url::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Length::class, $constraints[1]);
    }

    public function testConstraintsAbsentOrUnusableFallBackToNull(): void
    {
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW)->getConstraints(), 'No constraints key → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => ''])->getConstraints(), 'Empty string → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => 'not-serialized'])->getConstraints(), 'Unserializable garbage → null.');
        $this->assertNull(ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['constraints' => serialize(['x', 123])])->getConstraints(), 'Non-Constraint entries are filtered out → null.');
    }

    /**
     * The registry default_value cell is always a string: hydration casts it back to the
     * declared type through castDefaultValueFromDb(). The BOOL '0' case is the historical
     * bug pin — a naive (string)/(bool) round-trip lost a false default on reload.
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
     * table_name is persisted resolved at registration: when present it is authoritative
     * (hydration never re-resolves); when absent (rows predating the column) the
     * constructor resolution applies — ObjectModel class, then the entity name itself.
     */
    public function testTableNameComesFromRowWhenPresent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['table_name' => 'some_physical_table']);

        $this->assertSame('some_physical_table', $definition->getTableName());
    }

    public function testTableNameFallsBackToConstructorResolutionWhenAbsent(): void
    {
        $combination = ExtraPropertyDefinition::fromRow(['entity_name' => 'combination', 'property_name' => 'field']);
        $bareTable = ExtraPropertyDefinition::fromRow(['entity_name' => 'my_custom_table', 'property_name' => 'field']);

        // 'combination' resolves its physical table through the Combination ObjectModel class.
        $this->assertSame('product_attribute', $combination->getTableName());
        // No matching ObjectModel class: the entity name is the table (bare-table registration).
        $this->assertSame('my_custom_table', $bareTable->getTableName());
    }

    /**
     * primary_key_name is the introspected live extra-table PK (synthetic key). Absent,
     * the ObjectModel class resolution applies, then the 'id_' + entityName convention.
     */
    public function testPrimaryKeyNameComesFromRowWhenPresent(): void
    {
        $definition = ExtraPropertyDefinition::fromRow(self::BASE_ROW + ['primary_key_name' => 'id_custom']);

        $this->assertSame('id_custom', $definition->getPrimaryKeyName());
    }

    public function testPrimaryKeyNameFallsBackToClassThenConvention(): void
    {
        $order = ExtraPropertyDefinition::fromRow(['entity_name' => 'order', 'property_name' => 'field']);
        $bareTable = ExtraPropertyDefinition::fromRow(['entity_name' => 'my_custom_table', 'property_name' => 'field']);

        // Order::$definition['primary'] resolves the irregular PK.
        $this->assertSame('id_order', $order->getPrimaryKeyName());
        // No class: naming convention.
        $this->assertSame('id_my_custom_table', $bareTable->getPrimaryKeyName());
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
