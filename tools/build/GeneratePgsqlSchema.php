<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

/**
 * Converts install-dev/data/db_structure.sql (MySQL DDL) into a valid PostgreSQL
 * equivalent. The MySQL file remains the single source of truth for the schema;
 * this script keeps db_structure.pgsql.sql from drifting out of sync with it.
 *
 * Type mapping notes:
 *  - `... unsigned` integer columns are widened to the next signed PostgreSQL
 *    integer type (e.g. INT UNSIGNED -> BIGINT) since PostgreSQL has no unsigned
 *    types. This mirrors pgloader's default MySQL->PostgreSQL cast rules and
 *    trades a few extra bytes per row for safety against overflow.
 *  - `ENUM(...)` becomes `VARCHAR(255)` plus an inline `CHECK (... IN (...))`
 *    constraint, since PostgreSQL enums require a separate CREATE TYPE and are
 *    awkward to evolve.
 *  - `BOOLEAN` (a MySQL alias for TINYINT(1)) becomes SMALLINT rather than the
 *    native PostgreSQL boolean type, so the column keeps returning 0/1 instead
 *    of PDO pgsql's 't'/'f', which legacy code reading these columns expects.
 *  - MySQL-only clauses (ENGINE=, DEFAULT CHARSET=, COLLATE=) are dropped.
 *  - Non-unique `KEY`/`INDEX` clauses are not supported inline by PostgreSQL and
 *    are emitted as separate `CREATE INDEX` statements after their table.
 *  - `UNIQUE KEY`/`UNIQUE INDEX` clauses become inline named `CONSTRAINT ...
 *    UNIQUE (...)`.
 *  - Named `PRIMARY KEY` clauses (MySQL doesn't actually support naming a
 *    primary key, but a couple of definitions carry a dead name token anyway)
 *    have their name dropped; PostgreSQL names the constraint itself.
 *  - A `` `col`(N) `` index prefix-length reference (MySQL-only, used to index
 *    only the first N bytes/characters of a column) becomes a functional index
 *    on `substring("col", 1, N)` (works on both text and bytea columns, unlike
 *    `left()` which PostgreSQL only defines for text).
 *
 * There are no FOREIGN KEY/CONSTRAINT or FULLTEXT clauses in db_structure.sql
 * today, so none are handled here; if either is ever introduced this script
 * will throw on the unrecognized clause rather than silently mis-converting it.
 *
 * Generated index/constraint names are prefixed with their table name (itself
 * still carrying the literal "PREFIX_" placeholder substituted at install time)
 * to stay unique PostgreSQL-wide, and truncated with a hash suffix once they'd
 * exceed PostgreSQL's 63-byte identifier limit. That budget is computed against
 * the "PREFIX_" placeholder, not the real table prefix an install may configure;
 * an unusually long custom prefix combined with an already near-63-char name
 * could still collide after PostgreSQL's own silent truncation.
 */
final class PgsqlSchemaGenerator
{
    private const IDENTIFIER_MAX_LENGTH = 63;

    /** @var array<string, true> */
    private array $usedIndexNames = [];

    public function convert(string $mysqlSql): string
    {
        $this->usedIndexNames = [];

        $tables = [];
        foreach ($this->splitStatements($mysqlSql) as $statement) {
            if (!preg_match('/CREATE\s+TABLE/i', $statement)) {
                // Only CREATE TABLE statements are present (and relevant) in db_structure.sql;
                // MySQL-only statements such as `SET SESSION sql_mode=...` are dropped.
                continue;
            }
            $tables[] = $this->convertCreateTable($statement);
        }

        return implode("\n\n", $tables) . "\n";
    }

    private function convertCreateTable(string $statement): string
    {
        if (!preg_match('/^(?<comment>\/\*.*?\*\/\s*)?CREATE\s+TABLE\s+(?<ifNotExists>IF\s+NOT\s+EXISTS\s+)?`(?<table>[^`]+)`\s*\(/is', $statement, $m, PREG_OFFSET_CAPTURE)) {
            throw new RuntimeException('Cannot parse CREATE TABLE statement: ' . $statement);
        }

        $comment = trim($m['comment'][0]);
        $ifNotExists = $m['ifNotExists'][0] !== '';
        $tableName = $m['table'][0];
        $openParenPos = strpos($statement, '(', $m['table'][1] + strlen($tableName));
        $closeParenPos = $this->findMatchingParen($statement, $openParenPos);
        $body = substr($statement, $openParenPos + 1, $closeParenPos - $openParenPos - 1);
        // Strip inline documentation comments (e.g. "/* Deprecated since ... */") found
        // within a column list; the leading per-table comment was already captured above.
        $body = preg_replace('#/\*.*?\*/#s', '', $body) ?? $body;

        $columnLines = [];
        $indexStatements = [];

        foreach ($this->splitTopLevel($body) as $entry) {
            $entry = trim(preg_replace('/\s+/', ' ', $entry) ?? '');
            if ($entry === '') {
                continue;
            }

            if (preg_match('/^PRIMARY\s+KEY\s*(?:`?[A-Za-z_][A-Za-z0-9_]*`?\s*)?\((?<cols>.*)\)$/is', $entry, $km)) {
                $columnLines[] = 'PRIMARY KEY (' . $this->convertKeyColumnList($km['cols']) . ')';

                continue;
            }

            if (preg_match('/^UNIQUE(?:\s+(?:KEY|INDEX))?\s*(?:`?(?<name>[A-Za-z_][A-Za-z0-9_]*)`?\s*)?\((?<cols>.*)\)$/is', $entry, $km)) {
                $cols = $this->convertKeyColumnList($km['cols']);
                $baseName = $km['name'] !== '' ? $km['name'] : $this->slugifyColumnList($km['cols']) . '_unique';
                $name = $this->uniqueIndexName($tableName, $baseName);
                $columnLines[] = 'CONSTRAINT "' . $name . '" UNIQUE (' . $cols . ')';

                continue;
            }

            if (preg_match('/^(?:KEY|INDEX)\s*(?:`?(?<name>[A-Za-z_][A-Za-z0-9_]*)`?\s*)?\((?<cols>.*)\)$/is', $entry, $km)) {
                $cols = $this->convertKeyColumnList($km['cols']);
                $baseName = $km['name'] !== '' ? $km['name'] : $this->slugifyColumnList($km['cols']) . '_idx';
                $name = $this->uniqueIndexName($tableName, $baseName);
                $indexStatements[] = 'CREATE INDEX "' . $name . '" ON "' . $tableName . '" (' . $cols . ');';

                continue;
            }

            $columnLines[] = $this->convertColumn($entry);
        }

        $sql = ($comment !== '' ? $comment . "\n" : '')
            . 'CREATE TABLE ' . ($ifNotExists ? 'IF NOT EXISTS ' : '') . '"' . $tableName . "\" (\n  " . implode(",\n  ", $columnLines) . "\n);";

        if ($indexStatements) {
            $sql .= "\n" . implode("\n", $indexStatements);
        }

        return $sql;
    }

    private function convertColumn(string $entry): string
    {
        if (!preg_match('/^(?:`(?<quoted>[^`]+)`|(?<bare>[A-Za-z_][A-Za-z0-9_]*))\s+(?<rest>.*)$/s', $entry, $m)) {
            throw new RuntimeException('Cannot parse column definition: ' . $entry);
        }
        $colName = $m['quoted'] !== '' ? $m['quoted'] : $m['bare'];
        $rest = $m['rest'];

        if (!preg_match('/^(?<type>[A-Za-z]+)\s*(?:\((?<args>[^)]*)\))?\s*(?<modifiers>.*)$/s', $rest, $tm)) {
            throw new RuntimeException("Cannot parse type for column \"$colName\": $rest");
        }
        $rawType = $tm['type'];
        $args = $tm['args'] !== '' ? $tm['args'] : null;
        $modifiers = $tm['modifiers'];

        $unsigned = false;
        if (preg_match('/^unsigned\s*(?<rest>.*)$/is', $modifiers, $um)) {
            $unsigned = true;
            $modifiers = $um['rest'];
        }

        $isAutoIncrement = (bool) preg_match('/\bauto_increment\b/i', $modifiers);
        $modifiers = preg_replace('/\bauto_increment\b/i', '', $modifiers) ?? '';
        // Character set/collation are database-wide in PostgreSQL, not per-column.
        $modifiers = preg_replace('/\bcharacter\s+set\s+\w+\b/i', '', $modifiers) ?? '';
        // MySQL's all-zero "unset" date/datetime sentinel isn't a valid PostgreSQL
        // date. '-infinity' preserves the "always earlier than a real date" semantics.
        $modifiers = preg_replace("/'0000-00-00(?:\s+00:00:00)?'/", "'-infinity'", $modifiers) ?? '';
        $modifiers = trim(preg_replace('/\s+/', ' ', $modifiers) ?? '');

        [$pgType, $isEnum, $enumValues] = $this->mapType($rawType, $args, $unsigned);

        $parts = ['"' . $colName . '"', $pgType];
        if ($modifiers !== '') {
            $parts[] = $modifiers;
        }
        if ($isAutoIncrement) {
            $parts[] = 'GENERATED BY DEFAULT AS IDENTITY';
        }
        if ($isEnum) {
            $parts[] = 'CHECK ("' . $colName . '" IN (' . $enumValues . '))';
        }

        return implode(' ', $parts);
    }

    /**
     * @return array{0: string, 1: bool, 2: ?string}
     */
    private function mapType(string $rawType, ?string $args, bool $unsigned): array
    {
        switch (strtoupper($rawType)) {
            case 'TINYINT':
                return ['SMALLINT', false, null];
            case 'SMALLINT':
                return [$unsigned ? 'INTEGER' : 'SMALLINT', false, null];
            case 'MEDIUMINT':
                return ['INTEGER', false, null];
            case 'INT':
            case 'INTEGER':
                return [$unsigned ? 'BIGINT' : 'INTEGER', false, null];
            case 'BIGINT':
                return ['BIGINT', false, null];
            case 'DECIMAL':
            case 'NUMERIC':
                return [$args !== null ? "NUMERIC($args)" : 'NUMERIC', false, null];
            case 'FLOAT':
                return ['REAL', false, null];
            case 'DOUBLE':
                return ['DOUBLE PRECISION', false, null];
            case 'BOOLEAN':
            case 'BOOL':
                return ['SMALLINT', false, null];
            case 'VARCHAR':
                return ["VARCHAR($args)", false, null];
            case 'CHAR':
                return [$args !== null ? "CHAR($args)" : 'CHAR', false, null];
            case 'VARBINARY':
            case 'BINARY':
            case 'BLOB':
            case 'TINYBLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
                return ['BYTEA', false, null];
            case 'TEXT':
            case 'TINYTEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
                return ['TEXT', false, null];
            case 'DATE':
                return ['DATE', false, null];
            case 'DATETIME':
            case 'TIMESTAMP':
                return ['TIMESTAMP', false, null];
            case 'ENUM':
                return ['VARCHAR(255)', true, $args];
            case 'JSON':
                return ['JSON', false, null];
            default:
                throw new RuntimeException("Unmapped MySQL type: $rawType");
        }
    }

    private function convertKeyColumnList(string $colList): string
    {
        $converted = [];
        foreach ($this->splitTopLevel($colList) as $col) {
            $col = trim($col);
            if (preg_match('/^`(?<name>[^`]+)`(?:\((?<length>\d+)\))?$/', $col, $m)
                || preg_match('/^(?<name>[A-Za-z_][A-Za-z0-9_]*)(?:\((?<length>\d+)\))?$/', $col, $m)
            ) {
                $converted[] = ($m['length'] ?? '') !== ''
                    ? 'substring("' . $m['name'] . '", 1, ' . $m['length'] . ')'
                    : '"' . $m['name'] . '"';

                continue;
            }

            throw new RuntimeException("Unexpected column reference in key/index list: $col");
        }

        return implode(', ', $converted);
    }

    private function slugifyColumnList(string $colList): string
    {
        $names = [];
        foreach ($this->splitTopLevel($colList) as $col) {
            $col = trim($col, " `\t");
            $col = preg_replace('/\(\d+\)$/', '', $col) ?? $col;
            $names[] = $col;
        }

        return implode('_', $names);
    }

    private function uniqueIndexName(string $tableName, string $baseName): string
    {
        $name = $tableName . '_' . $baseName;
        if (strlen($name) > self::IDENTIFIER_MAX_LENGTH) {
            $name = substr($name, 0, self::IDENTIFIER_MAX_LENGTH - 9) . '_' . substr(md5($name), 0, 8);
        }

        $candidate = $name;
        $suffix = 2;
        while (isset($this->usedIndexNames[$candidate])) {
            $candidate = $name . '_' . $suffix;
            ++$suffix;
        }
        $this->usedIndexNames[$candidate] = true;

        return $candidate;
    }

    /**
     * Splits SQL statements the same way SqlLoader::parse() does at runtime,
     * so the set of statements this script sees matches what actually gets executed.
     *
     * @return string[]
     */
    private function splitStatements(string $content): array
    {
        $statements = preg_split('#;\s*[\r\n]+#', $content) ?: [];

        return array_values(array_filter(array_map('trim', $statements), static fn (string $s) => $s !== ''));
    }

    /**
     * Splits a string on top-level commas only, ignoring commas that are
     * nested inside parentheses (e.g. `decimal(13,6)`, a multi-column key,
     * or an ENUM value list) or inside a single-quoted string literal
     * (e.g. `DEFAULT 'env,dotenv,db'`).
     *
     * @return string[]
     */
    private function splitTopLevel(string $s, string $delimiter = ','): array
    {
        $parts = [];
        $depth = 0;
        $inString = false;
        $current = '';
        $len = strlen($s);

        for ($i = 0; $i < $len; ++$i) {
            $ch = $s[$i];

            if ($inString) {
                $current .= $ch;
                if ($ch === '\\') {
                    // Consume the escaped character too, so an escaped quote doesn't end the string.
                    if ($i + 1 < $len) {
                        $current .= $s[++$i];
                    }
                } elseif ($ch === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($ch === "'") {
                $inString = true;
                $current .= $ch;

                continue;
            }

            if ($ch === '(') {
                ++$depth;
            } elseif ($ch === ')') {
                --$depth;
            }

            if ($ch === $delimiter && $depth === 0) {
                $parts[] = $current;
                $current = '';

                continue;
            }

            $current .= $ch;
        }

        if (trim($current) !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    /**
     * Finds the index of the ')' matching the '(' at $openPos, honoring
     * single-quoted string literals so a literal ')' inside a value can't
     * be mistaken for the closing paren.
     */
    private function findMatchingParen(string $s, int $openPos): int
    {
        $depth = 0;
        $inString = false;
        $len = strlen($s);

        for ($i = $openPos; $i < $len; ++$i) {
            $ch = $s[$i];

            if ($inString) {
                if ($ch === '\\') {
                    ++$i;
                } elseif ($ch === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($ch === "'") {
                $inString = true;
            } elseif ($ch === '(') {
                ++$depth;
            } elseif ($ch === ')') {
                --$depth;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        throw new RuntimeException('Unbalanced parentheses while parsing CREATE TABLE body');
    }
}

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the CLI.\n");
    exit(1);
}

$rootDir = dirname(__DIR__, 2);
$source = $rootDir . '/install-dev/data/db_structure.sql';
$destination = $rootDir . '/install-dev/data/db_structure.pgsql.sql';

if (!is_readable($source)) {
    fwrite(STDERR, "Cannot read $source\n");
    exit(1);
}

$generator = new PgsqlSchemaGenerator();

try {
    $pgsql = $generator->convert((string) file_get_contents($source));
} catch (Throwable $e) {
    fwrite(STDERR, 'Conversion failed: ' . $e->getMessage() . "\n");
    exit(1);
}

file_put_contents($destination, $pgsql);
echo "Generated $destination\n";
