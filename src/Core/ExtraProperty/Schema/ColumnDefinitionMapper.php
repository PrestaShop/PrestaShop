<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Schema;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;

/**
 * Maps an ExtraPropertyDefinition VO to a complete SQL column definition fragment.
 *
 * The returned string is ready to be appended after the column name in an ALTER TABLE … ADD COLUMN statement.
 * NULL/NOT NULL and DEFAULT clauses are always explicit so the caller does not need to add them.
 */
class ColumnDefinitionMapper
{
    /**
     * Returns the full SQL column definition fragment for the given options.
     *
     * @param ExtraPropertyDefinition $options Property options containing type, enumValues, nullable, defaultValue
     *
     * @return string e.g. "VARCHAR(255) NULL" or "ENUM('a','b') NOT NULL DEFAULT 'a'"
     */
    public static function getSqlDefinition(ExtraPropertyDefinition $options, bool $postgreSql = false): string
    {
        $parts = [
            $postgreSql ? self::getPostgreSqlBaseDefinition($options) : self::buildBaseDefinition($options->getType(), self::getEnumValues($options), $options->getSize()),
            $options->isNullable() ? 'NULL' : 'NOT NULL',
        ];

        $defaultClauseValue = self::getDefaultClauseValue($options, $postgreSql);
        if (null !== $defaultClauseValue) {
            $parts[] = 'DEFAULT ' . $defaultClauseValue;
        }

        return implode(' ', $parts);
    }

    /**
     * PostgreSQL has no unsigned, TINYINT, DATETIME, LONGTEXT nor inline ENUM type: a CHOICE
     * column is a VARCHAR whose literals are enforced by the CHECK of getPostgreSqlEnumCheck().
     */
    public static function getPostgreSqlBaseDefinition(ExtraPropertyDefinition $options): string
    {
        return match ($options->getType()) {
            ExtraPropertyType::INT => 'INTEGER',
            ExtraPropertyType::BOOL => 'SMALLINT',
            ExtraPropertyType::STRING => 'VARCHAR(' . (null !== $options->getSize() && $options->getSize() > 0 ? $options->getSize() : 255) . ')',
            ExtraPropertyType::FLOAT => 'DECIMAL(20,6)',
            ExtraPropertyType::DATE => 'TIMESTAMP',
            ExtraPropertyType::HTML, ExtraPropertyType::JSON => 'TEXT',
            ExtraPropertyType::CHOICE => !empty(self::getEnumValues($options)) ? 'VARCHAR(255)' : 'VARCHAR(64)',
        };
    }

    /**
     * Returns the CHECK expression restricting a PostgreSQL CHOICE column to its literals,
     * or null when the column has no literal list.
     */
    public static function getPostgreSqlEnumCheck(ExtraPropertyDefinition $options, string $quotedColumnName): ?string
    {
        $enumValues = self::getEnumValues($options);
        if (ExtraPropertyType::CHOICE !== $options->getType() || empty($enumValues)) {
            return null;
        }

        return sprintf(
            '%s IN (%s)',
            $quotedColumnName,
            implode(',', array_map(static fn (string $value): string => "'" . str_replace("'", "''", $value) . "'", $enumValues))
        );
    }

    /**
     * Returns the SQL literal of the DDL DEFAULT clause, or null when the column carries none.
     *
     * TEXT-backed columns (HTML → TEXT, JSON → LONGTEXT) cannot carry a DDL DEFAULT
     * clause in MySQL: their declared default lives in the registry only and is served
     * at read time by the reader's default injection.
     */
    public static function getDefaultClauseValue(ExtraPropertyDefinition $options, bool $postgreSql = false): ?string
    {
        $defaultValue = $options->getDefaultValue();
        if (null === $defaultValue || in_array($options->getType(), [ExtraPropertyType::HTML, ExtraPropertyType::JSON], true)) {
            return null;
        }

        return self::quoteDefaultValue($options->getType(), $defaultValue, $postgreSql);
    }

    /**
     * @return list<string>
     */
    private static function getEnumValues(ExtraPropertyDefinition $options): array
    {
        $rawEnumValues = $options->getEnumValues();

        return null !== $rawEnumValues ? array_values(array_filter($rawEnumValues, 'is_string')) : [];
    }

    /**
     * Returns the base SQL type string (without NULL/NOT NULL or DEFAULT).
     *
     * @param ExtraPropertyType $type
     * @param list<string> $enumValues Only used for Choice type
     * @param int|null $size For STRING type: varchar length (1–16383). Defaults to 255 when null.
     *                       Ignored for all other types.
     *
     * @return string
     */
    private static function buildBaseDefinition(ExtraPropertyType $type, array $enumValues, ?int $size = null): string
    {
        return match ($type) {
            ExtraPropertyType::INT => 'INT(11)',
            ExtraPropertyType::BOOL => 'TINYINT(1) UNSIGNED',
            ExtraPropertyType::STRING => 'VARCHAR(' . (null !== $size && $size > 0 ? $size : 255) . ')',
            ExtraPropertyType::FLOAT => 'DECIMAL(20,6)',
            ExtraPropertyType::DATE => 'DATETIME',
            ExtraPropertyType::HTML => 'TEXT',
            ExtraPropertyType::JSON => 'LONGTEXT',
            ExtraPropertyType::CHOICE => !empty($enumValues) ? self::buildEnumDefinition($enumValues) : 'VARCHAR(64)',
        };
    }

    /**
     * Extracts the literals of a SQL ENUM column type, e.g. "enum('a','b')" → ['a', 'b'].
     * Parsing counterpart of buildEnumDefinition().
     *
     * Returns null for any non-ENUM column type.
     *
     * @return list<string>|null
     */
    public static function parseEnumValues(string $sqlColumnType): ?array
    {
        if (!str_starts_with(strtolower($sqlColumnType), 'enum(')) {
            return null;
        }

        // Literals are single-quoted; embedded quotes are doubled ('').
        preg_match_all("/'((?:[^']|'')*)'/", $sqlColumnType, $matches);
        $values = array_map(
            static fn (string $value): string => str_replace("''", "'", $value),
            $matches[1]
        );

        return [] !== $values ? array_values($values) : null;
    }

    /**
     * Builds an ENUM SQL definition from a list of allowed values, with proper string-literal escaping.
     *
     * @param list<string> $enumValues
     *
     * @return string e.g. "ENUM('pending','active','closed')"
     */
    private static function buildEnumDefinition(array $enumValues): string
    {
        $quotedValues = array_map(
            static fn (string $v): string => "'" . self::escapeStringLiteral($v) . "'",
            $enumValues
        );

        return 'ENUM(' . implode(',', $quotedValues) . ')';
    }

    /**
     * Escapes a value for embedding inside a single-quoted MySQL string literal.
     *
     * Doubling the single quote is NOT enough: unless the session runs with
     * NO_BACKSLASH_ESCAPES (not the default), MySQL treats the backslash as an escape
     * character, so a value ending in "\" would escape the closing quote and let the
     * following characters break out of the literal into executable SQL. The enum
     * literals and the DEFAULT clause are built by string concatenation here (this mapper
     * is static and has no DBAL connection to delegate quoting to), so every literal must
     * neutralise the backslash as well as the quote. The backslash is escaped FIRST so it
     * never doubles the escaping introduced for the quote.
     */
    private static function escapeStringLiteral(string $value): string
    {
        return str_replace(['\\', "'"], ['\\\\', "''"], $value);
    }

    /**
     * Formats a default value as a SQL literal appropriate for the given type.
     *
     * Numeric types are unquoted; string-like types are single-quoted with escaping.
     *
     * PostgreSQL's standard-conforming strings give the backslash no special meaning:
     * only the quote is doubled there.
     *
     * @param ExtraPropertyType $type
     * @param scalar $defaultValue
     *
     * @return string
     */
    private static function quoteDefaultValue(ExtraPropertyType $type, mixed $defaultValue, bool $postgreSql = false): string
    {
        // The canonical stringification is shared with the registry row write and the
        // live-schema comparison; only the SQL quoting is added here.
        $stringValue = (string) ExtraPropertyValueCaster::castDefaultValueForDb($type, $defaultValue);

        return match ($type) {
            ExtraPropertyType::INT,
            ExtraPropertyType::FLOAT,
            ExtraPropertyType::BOOL => $stringValue,
            ExtraPropertyType::STRING,
            ExtraPropertyType::DATE,
            ExtraPropertyType::HTML,
            ExtraPropertyType::JSON,
            ExtraPropertyType::CHOICE => "'" . ($postgreSql ? str_replace("'", "''", $stringValue) : self::escapeStringLiteral($stringValue)) . "'",
        };
    }
}
