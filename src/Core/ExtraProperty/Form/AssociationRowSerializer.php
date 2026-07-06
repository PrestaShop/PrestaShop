<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

/**
 * Turns the definition form's placement rows back into the "one placement entry" strings the
 * CQRS commands accept — the mirror image of AssociationRowPresenter, which splits the stored
 * entries into rows. Both directions only carry a row's EXPLICIT parts, so a presenter->serializer
 * round trip re-emits the stored entry byte for byte.
 *
 * Rows whose identifying field is empty are skipped: an added-then-abandoned builder row must not
 * produce an entry. No grammar check happens here — the row form types validate each serialized
 * entry through AssociationEntryParser before the data handler runs.
 */
class AssociationRowSerializer
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * @param list<array{form_id?: string|null, path?: string|null, mode?: string|null}> $rows
     *
     * @return list<string>
     */
    public static function formEntries(array $rows): array
    {
        return self::entries($rows, static fn (array $row): string => self::joinParts(
            $row['form_id'] ?? '',
            $row['path'] ?? '',
            $row['mode'] ?? ''
        ));
    }

    /**
     * @param list<array{grid_id?: string|null, column_id?: string|null, mode?: string|null}> $rows
     *
     * @return list<string>
     */
    public static function gridEntries(array $rows): array
    {
        return self::entries($rows, static fn (array $row): string => self::joinParts(
            $row['grid_id'] ?? '',
            $row['column_id'] ?? '',
            $row['mode'] ?? ''
        ));
    }

    /**
     * @param list<array{uri?: string|null, methods?: string|null}> $rows
     *
     * @return list<string>
     */
    public static function apiEntries(array $rows): array
    {
        return self::entries($rows, static fn (array $row): string => self::joinParts(
            $row['uri'] ?? '',
            $row['methods'] ?? ''
        ));
    }

    /**
     * Serializes one row per entry through $serializeRow, dropping the rows that serialize to
     * nothing (empty identifying field).
     *
     * @param list<array<string, string|null>> $rows
     * @param callable(array<string, string|null>): string $serializeRow
     *
     * @return list<string>
     */
    private static function entries(array $rows, callable $serializeRow): array
    {
        $entries = [];
        foreach ($rows as $row) {
            $entry = $serializeRow($row);
            if ('' !== $entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Joins the entry's parts with ":" up to the last non-empty one — "product", "product:name",
     * "product:name:after". An empty leading part empties the whole entry (a path or mode without
     * an id is not an entry), and a part after a gap is dropped (a mode without a path/column has
     * no anchor to apply to — the presenter never produces that shape either).
     */
    private static function joinParts(?string ...$parts): string
    {
        $kept = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ('' === $part) {
                break;
            }
            $kept[] = $part;
        }

        return implode(':', $kept);
    }
}
