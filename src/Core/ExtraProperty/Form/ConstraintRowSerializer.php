<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintGrammar;

/**
 * Turns the definition form's constraint rows back into the DSL string the constraint parser
 * reads — the mirror image of ConstraintRowPresenter, which splits a DSL string into rows.
 *
 * A row's options tail is re-emitted VERBATIM inside the token's delimiters — parenthesis shape
 * for regular constraints ("Length(min: 2, max: 64)"), bracket shape for composites ("All[ Url ]",
 * see ExtraPropertyConstraintGrammar::compositeNames()). Set-level rows serialize one per line in
 * order; all per_language rows fold into ONE "All[ a, b ]" line inserted where the first
 * per-language row sits among the rows — the inverse of the presenter's first-All explosion, so a
 * presenter->serializer round trip is order-stable.
 *
 * Rows with an empty name are skipped: an added-then-abandoned builder row must not produce a
 * token. No name/options check happens here — the row form type validates each serialized token
 * through the parser before the data handler runs.
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
     * Serializes a single row into its DSL token: "Name", "Name(tail)", "Name[tail]" for composites
     * and "Name(options)[tail]" for a composite carrying its own options. An empty name serializes
     * to nothing (skipped row).
     *
     * @param array{name?: string|null, options?: string|null, composite_options?: string|null, per_language?: string|null} $row
     */
    public static function token(array $row): string
    {
        $name = trim($row['name'] ?? '');
        if ('' === $name) {
            return '';
        }

        $tail = trim($row['options'] ?? '');
        $isComposite = in_array($name, ExtraPropertyConstraintGrammar::compositeNames(), true);

        if (!$isComposite) {
            return '' === $tail ? $name : $name . '(' . $tail . ')';
        }

        // A composite may carry its own options ahead of its children:
        // "Collection(allowExtraFields: true)[ name: NotBlank ]".
        $compositeOptions = trim($row['composite_options'] ?? '');
        $head = '' === $compositeOptions ? $name : $name . '(' . $compositeOptions . ')';

        // An empty composite keeps its brackets ("All[]" — the renderer's own output).
        return $head . '[' . $tail . ']';
    }
}
