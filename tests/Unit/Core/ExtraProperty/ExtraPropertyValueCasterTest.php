<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;

/**
 * Covers the four casting directions of ExtraPropertyValueCaster:
 *  - castFromDb (DB → PHP, the read shape — JSON decodes to a structure),
 *  - castForDb (PHP → DB, the single write choke point used by the writer — JSON
 *    structures are encoded, scalars coerced to their declared type),
 *  - castDefaultValueForDb (typed defaultValue → its ONE canonical registry/DDL string,
 *    shared by the registry row write, the DDL DEFAULT clause and the schema comparison),
 *  - castDefaultValueFromDb (registry default_value string → typed scalar; JSON defaults
 *    deliberately STAY strings, unlike value reads).
 */
class ExtraPropertyValueCasterTest extends TestCase
{
    /**
     * @dataProvider castFromDbProvider
     */
    public function testCastFromDb(ExtraPropertyType $type, mixed $rawValue, bool $nullable, mixed $expected): void
    {
        $this->assertSame($expected, ExtraPropertyValueCaster::castFromDb($type, $rawValue, $nullable));
    }

    public static function castFromDbProvider(): iterable
    {
        yield 'bool 1 string' => [ExtraPropertyType::BOOL, '1', false, true];
        yield 'bool 0 string' => [ExtraPropertyType::BOOL, '0', false, false];
        yield 'bool null on NOT NULL column coerces to false' => [ExtraPropertyType::BOOL, null, false, false];
        yield 'bool null on nullable column stays null' => [ExtraPropertyType::BOOL, null, true, null];
        yield 'int string' => [ExtraPropertyType::INT, '42', false, 42];
        yield 'int null stays null even on NOT NULL' => [ExtraPropertyType::INT, null, false, null];
        yield 'float string' => [ExtraPropertyType::FLOAT, '1.5', false, 1.5];
        yield 'date string is normalized' => [ExtraPropertyType::DATE, '2026-01-02', false, '2026-01-02 00:00:00'];
        yield 'invalid date becomes null' => [ExtraPropertyType::DATE, 'not-a-date', false, null];
        yield 'string passes through' => [ExtraPropertyType::STRING, 'hello', false, 'hello'];
        yield 'json string decodes to a structure' => [ExtraPropertyType::JSON, '{"a":1,"b":[2,3]}', false, ['a' => 1, 'b' => [2, 3]]];
        yield 'json scalar literal decodes' => [ExtraPropertyType::JSON, '"text"', false, 'text'];
        yield 'invalid json string becomes null' => [ExtraPropertyType::JSON, '{invalid', false, null];
        yield 'already-decoded json array passes through' => [ExtraPropertyType::JSON, ['a' => 1], false, ['a' => 1]];
        yield 'json null on nullable column stays null' => [ExtraPropertyType::JSON, null, true, null];
    }

    /**
     * @dataProvider castForDbProvider
     */
    public function testCastForDb(ExtraPropertyDefinition $definition, mixed $value, mixed $expected): void
    {
        $this->assertSame($expected, ExtraPropertyValueCaster::castForDb($definition, $value));
    }

    public static function castForDbProvider(): iterable
    {
        yield 'bool true becomes 1' => [self::definition(ExtraPropertyType::BOOL), true, 1];
        yield 'bool false becomes 0' => [self::definition(ExtraPropertyType::BOOL), false, 0];
        yield 'int string is coerced' => [self::definition(ExtraPropertyType::INT), '12', 12];
        yield 'float string is coerced' => [self::definition(ExtraPropertyType::FLOAT), '1.5', 1.5];
        yield 'date object is formatted' => [
            self::definition(ExtraPropertyType::DATE),
            new DateTimeImmutable('2026-01-02 10:20:30'),
            '2026-01-02 10:20:30',
        ];
        yield 'date string passes through' => [self::definition(ExtraPropertyType::DATE), '2026-01-02 10:20:30', '2026-01-02 10:20:30'];
        yield 'string passes through' => [self::definition(ExtraPropertyType::STRING), 'hello', 'hello'];
        yield 'json structure is encoded' => [self::definition(ExtraPropertyType::JSON), ['a' => 1, 'b' => [2]], '{"a":1,"b":[2]}'];
        yield 'json string passes through' => [self::definition(ExtraPropertyType::JSON), '{"a":1}', '{"a":1}'];
        yield 'null is preserved for bool' => [self::definition(ExtraPropertyType::BOOL), null, null];
        yield 'null is preserved for int' => [self::definition(ExtraPropertyType::INT), null, null];
        yield 'null is preserved for json' => [self::definition(ExtraPropertyType::JSON), null, null];
        yield 'lang array is cast per entry' => [
            self::definition(ExtraPropertyType::BOOL, ExtraPropertyScope::LANG),
            [1 => true, 2 => false],
            [1 => 1, 2 => 0],
        ];
        yield 'lang scalar passes the scalar cast (single-language write shape)' => [
            self::definition(ExtraPropertyType::BOOL, ExtraPropertyScope::LANG),
            true,
            1,
        ];
    }

    /**
     * @dataProvider castDefaultValueForDbProvider
     */
    public function testCastDefaultValueForDb(ExtraPropertyType $type, int|float|string|bool|null $value, ?string $expected): void
    {
        $this->assertSame($expected, ExtraPropertyValueCaster::castDefaultValueForDb($type, $value));
    }

    public static function castDefaultValueForDbProvider(): iterable
    {
        // BOOL is the reason this method exists: a naive (string) cast turns false into ''.
        yield 'bool false becomes 0' => [ExtraPropertyType::BOOL, false, '0'];
        yield 'bool true becomes 1' => [ExtraPropertyType::BOOL, true, '1'];
        yield 'int zero is preserved' => [ExtraPropertyType::INT, 0, '0'];
        yield 'float' => [ExtraPropertyType::FLOAT, 1.5, '1.5'];
        yield 'date object shape' => [ExtraPropertyType::DATE, '2026-01-02', '2026-01-02 00:00:00'];
        yield 'string passes through' => [ExtraPropertyType::STRING, 'hello', 'hello'];
        yield 'json string passes through' => [ExtraPropertyType::JSON, '{"a":1}', '{"a":1}'];
        yield 'null stays null' => [ExtraPropertyType::STRING, null, null];
    }

    /**
     * @dataProvider castDefaultValueFromDbProvider
     */
    public function testCastDefaultValueFromDb(ExtraPropertyType $type, string $rawValue, int|float|string|bool $expected): void
    {
        $this->assertSame($expected, ExtraPropertyValueCaster::castDefaultValueFromDb($type, $rawValue));
    }

    public static function castDefaultValueFromDbProvider(): iterable
    {
        yield 'bool 0 becomes false (the previously-lost default)' => [ExtraPropertyType::BOOL, '0', false];
        yield 'bool 1 becomes true' => [ExtraPropertyType::BOOL, '1', true];
        yield 'int zero' => [ExtraPropertyType::INT, '0', 0];
        yield 'float' => [ExtraPropertyType::FLOAT, '1.5', 1.5];
        yield 'valid date is normalized' => [ExtraPropertyType::DATE, '2026-01-02', '2026-01-02 00:00:00'];
        // Unlike value reads, an unparseable DATE default is preserved rather than nulled:
        // erasing it would silently drop the declared default on every hydration.
        yield 'invalid date is preserved as-is' => [ExtraPropertyType::DATE, 'not-a-date', 'not-a-date'];
        yield 'string passes through' => [ExtraPropertyType::STRING, 'hello', 'hello'];
        // JSON defaults STAY strings: ExtraPropertyDefinition::$defaultValue is scalar-typed
        // and the value feeds the DDL DEFAULT clause, not a read surface.
        yield 'json default stays a string' => [ExtraPropertyType::JSON, '{"a":1}', '{"a":1}'];
    }

    private static function definition(
        ExtraPropertyType $type,
        ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            type: $type,
            scope: $scope,
        );
    }
}
