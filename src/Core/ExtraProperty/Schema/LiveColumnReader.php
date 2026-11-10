<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/**
 * Reads the live columns of a table as MySQL SHOW COLUMNS rows (Field/Type/Null/Default/Key),
 * the shape the extra property schema code compares definitions with.
 *
 * On PostgreSQL the rows are rebuilt from the catalog: a CHOICE column is a VARCHAR whose
 * literals live in a CHECK constraint, reported back as an "enum('a','b')" type.
 */
final class LiveColumnReader
{
    /**
     * @param string $tableName Full table name (with prefix)
     *
     * @return list<array<string, mixed>>
     */
    public static function fetchColumns(Connection $connection, string $tableName): array
    {
        if (!$connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            return $connection->fetchAllAssociative('SHOW COLUMNS FROM ' . $connection->quoteIdentifier($tableName));
        }

        $rows = $connection->fetchAllAssociative(
            'SELECT a.attname AS field,
                format_type(a.atttypid, a.atttypmod) AS type,
                a.attnotnull AS not_null,
                pg_get_expr(d.adbin, d.adrelid) AS default_value,
                EXISTS (
                    SELECT 1 FROM pg_index i
                    WHERE i.indrelid = a.attrelid AND i.indisprimary AND a.attnum = ANY (i.indkey)
                ) AS is_primary,
                (
                    SELECT pg_get_constraintdef(c.oid) FROM pg_constraint c
                    WHERE c.conrelid = a.attrelid AND c.contype = \'c\' AND c.conkey = ARRAY[a.attnum]
                    LIMIT 1
                ) AS check_definition
            FROM pg_attribute a
            LEFT JOIN pg_attrdef d ON d.adrelid = a.attrelid AND d.adnum = a.attnum
            WHERE a.attrelid = to_regclass(:tableName) AND a.attnum > 0 AND NOT a.attisdropped
            ORDER BY a.attnum',
            ['tableName' => $connection->quoteIdentifier($tableName)]
        );

        return array_map(self::toShowColumnsRow(...), $rows);
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private static function toShowColumnsRow(array $row): array
    {
        $type = (string) $row['type'];
        $enumValues = self::parseCheckLiterals((string) ($row['check_definition'] ?? ''));
        if (null !== $enumValues) {
            $type = 'enum(' . implode(',', array_map(static fn (string $value): string => "'" . str_replace("'", "''", $value) . "'", $enumValues)) . ')';
        } else {
            $type = preg_replace('/^character varying/', 'varchar', $type) ?? $type;
        }

        return [
            'Field' => (string) $row['field'],
            'Type' => $type,
            'Null' => self::isTrue($row['not_null']) ? 'NO' : 'YES',
            'Default' => self::normalizeDefault($row['default_value'] ?? null),
            'Key' => self::isTrue($row['is_primary']) ? 'PRI' : '',
        ];
    }

    /**
     * Literals of a "CHECK ((col)::text = ANY ((ARRAY['a'::character varying, ...])::text[]))" definition,
     * the form PostgreSQL gives back for "col IN ('a', ...)".
     *
     * @return list<string>|null
     */
    private static function parseCheckLiterals(string $checkDefinition): ?array
    {
        if (!str_contains($checkDefinition, 'ANY') || !preg_match_all("/'((?:[^']|'')*)'::/", $checkDefinition, $matches)) {
            return null;
        }

        return array_map(static fn (string $value): string => str_replace("''", "'", $value), $matches[1]);
    }

    /**
     * Drops the cast PostgreSQL appends to a literal default ('foo'::character varying, '-1'::integer).
     */
    private static function normalizeDefault(mixed $default): ?string
    {
        if (null === $default) {
            return null;
        }

        $default = (string) $default;
        if (preg_match("/^'((?:[^']|'')*)'::[a-z ]+(?:\\(\\d+(?:,\\d+)?\\))?$/i", $default, $matches)) {
            return str_replace("''", "'", $matches[1]);
        }

        return $default;
    }

    private static function isTrue(mixed $value): bool
    {
        return true === $value || 't' === $value || '1' === (string) $value;
    }
}
