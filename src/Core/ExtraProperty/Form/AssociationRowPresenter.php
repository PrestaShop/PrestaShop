<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\AssociationEntryParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;

/**
 * Splits the stored "one placement entry per line" values into the row models backing the
 * definition form's builder rows (one row = one collection entry, keys = row field names) — the
 * mirror image of AssociationRowSerializer, which the data handler runs on submit.
 *
 * Rows only carry the entry's EXPLICIT parts so that serializing a row back re-emits the original
 * entry: a grid entry "product:reference" keeps mode = '' here even though the runtime resolves it
 * to "after". A line that fails the grammar (the same assertValid* checks the
 * ExtraPropertyDefinition constructor runs) is skipped — every persisted entry went through that
 * grammar already, so this only drops hand-edited database values.
 */
class AssociationRowPresenter
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * @return list<array{form_id: string, path: string, mode: string}>
     */
    public static function formRows(?string $raw): array
    {
        return self::mapLines($raw, static function (string $line): array {
            $parsed = AssociationEntryParser::assertValidFormEntry($line);

            // Recombine the raw path the user typed (the parser splits it into parent + anchor
            // when a mode is present). A mode with no path ("product::before") is meaningless —
            // the runtime ignores it — so it canonicalizes to the bare-formId row.
            if (null === $parsed['mode']) {
                $path = $parsed['path'] ?? '';
            } elseif (null === $parsed['anchor']) {
                $path = '';
            } else {
                $path = '' === $parsed['path'] ? $parsed['anchor'] : $parsed['path'] . '.' . $parsed['anchor'];
            }

            return [
                'form_id' => $parsed['formId'],
                'path' => $path,
                'mode' => '' === $path ? '' : ($parsed['mode'] ?? ''),
            ];
        });
    }

    /**
     * @return list<array{grid_id: string, column_id: string, mode: string}>
     */
    public static function gridRows(?string $raw): array
    {
        return self::mapLines($raw, static function (string $line): array {
            $parsed = AssociationEntryParser::assertValidGridEntry($line);

            // The parser defaults mode to "after" when a column is given; the row keeps only an
            // explicit ":before"/":after" suffix so serializing re-emits the original line.
            $explicitMode = '';
            if (null !== $parsed['columnId']) {
                foreach (['before', 'after'] as $mode) {
                    if (str_ends_with($line, ':' . $mode)) {
                        $explicitMode = $mode;
                        break;
                    }
                }
            }

            return [
                'grid_id' => $parsed['gridId'],
                'column_id' => $parsed['columnId'] ?? '',
                'mode' => $explicitMode,
            ];
        });
    }

    /**
     * @return list<array{uri: string, methods: string}>
     */
    public static function apiRows(?string $raw): array
    {
        return self::mapLines($raw, static function (string $line): array {
            $parsed = AssociationEntryParser::assertValidApiEntry($line);

            // Keep the URI as typed (normalizeApiPath() is a comparison helper, not a rewrite);
            // methods are canonicalized to the uppercase CSV the chips write back.
            $colonPos = strpos($line, ':');

            return [
                'uri' => trim(false !== $colonPos ? substr($line, 0, $colonPos) : $line),
                'methods' => null !== $parsed['methods'] ? implode(',', $parsed['methods']) : '',
            ];
        });
    }

    /**
     * Splits the stored value into non-empty trimmed lines and maps each through $mapLine,
     * skipping the lines that fail the grammar.
     *
     * @param callable(string): array<string, string> $mapLine
     *
     * @return list<array<string, string>>
     */
    private static function mapLines(?string $raw, callable $mapLine): array
    {
        if (null === $raw || '' === trim($raw)) {
            return [];
        }

        $rows = [];
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }

            try {
                $rows[] = $mapLine($line);
            } catch (InvalidExtraPropertyDefinitionException) {
                // Unrepresentable without the raw edition — dropped (see class docblock).
            }
        }

        return $rows;
    }
}
