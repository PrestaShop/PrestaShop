<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * Turns the definition form's constraint rows back into the DSL string the constraint mapper
 * parses — the mirror image of ConstraintRowPresenter, which splits a DSL string into rows.
 *
 * A row's options tail is re-emitted VERBATIM inside the token's delimiters — parenthesis shape
 * for regular constraints ("Length(min: 2, max: 64)"), bracket shape for composites ("All[ Url ]",
 * see ExtraPropertyConstraintMapper::compositeNames()). Set-level rows serialize one per line in
 * order; all per_language rows fold into ONE "All[ a, b ]" line inserted where the first
 * per-language row sits among the rows — the inverse of the presenter's first-All explosion, so a
 * presenter->serializer round trip is order-stable.
 *
 * Rows with an empty name are skipped: an added-then-abandoned builder row must not produce a
 * token. No name/options check happens here — the row form type validates each serialized token
 * through the mapper before the data handler runs.
 */
class ConstraintRowSerializer
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * @param list<array{name?: string|null, options?: string|null, per_language?: string|null}> $rows
     */
    public static function serialize(array $rows): ?string
    {
        $lines = [];
        $perLanguageTokens = [];
        $foldIndex = null;

        foreach ($rows as $row) {
            $token = self::token($row);
            if ('' === $token) {
                continue;
            }

            if ('1' === ($row['per_language'] ?? '0')) {
                if (null === $foldIndex) {
                    // The fold line takes the position of the first per-language row; reserve it.
                    $foldIndex = count($lines);
                    $lines[] = '';
                }
                $perLanguageTokens[] = $token;
                continue;
            }

            $lines[] = $token;
        }

        if (null !== $foldIndex) {
            $lines[$foldIndex] = 'All[ ' . implode(', ', $perLanguageTokens) . ' ]';
        }

        return [] !== $lines ? implode("\n", $lines) : null;
    }

    /**
     * Serializes a single row into its DSL token: "Name", "Name(tail)" or "Name[tail]" for
     * composites. An empty name serializes to nothing (skipped row).
     *
     * @param array{name?: string|null, options?: string|null, per_language?: string|null} $row
     */
    public static function token(array $row): string
    {
        $name = trim($row['name'] ?? '');
        if ('' === $name) {
            return '';
        }

        $tail = trim($row['options'] ?? '');
        if ('' === $tail) {
            // An empty composite keeps its brackets ("All[]" — the mapper's own toNames render);
            // a regular constraint reads bare ("NotBlank").
            return in_array($name, ExtraPropertyConstraintMapper::compositeNames(), true) ? $name . '[]' : $name;
        }

        if (in_array($name, ExtraPropertyConstraintMapper::compositeNames(), true)) {
            return $name . '[' . $tail . ']';
        }

        return $name . '(' . $tail . ')';
    }
}
