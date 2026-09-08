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

    /**
     * Decoding is the repository's job — it owns the normalizer and can report a rejected constraint
     * with the row it came from. fromRow() receives constraints already decoded, and only carries
     * them through.
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
