<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException;

/**
 * Splits a constraints DSL string (the ExtraPropertyConstraintRenderer output) into the row models backing the
 * definition form's constraint builder (one row = one collection entry, keys = row field names) —
 * the mirror image of ConstraintRowSerializer, which the data handler runs on submit.
 *
 * A row keeps the constraint's argument as the VERBATIM token tail (the text between the token's
 * "(...)" or "[...]" delimiters): the builder renders typed inputs over the tail when it can, and
 * shows it as-is when it can't — either way nothing is lost. The FIRST top-level All[...] token is
 * exploded into per_language rows (the builder's "Applied to each language's value" zone) and folds
 * back into a single All[...] line on serialization; any further All[...] tokens stay opaque
 * set-level rows. Names are NOT checked against the mapper's whitelist here — a module-attached
 * constraint outside the whitelist still presents as a row (the read-only view renders it; on the
 * editable form the row form type validates names on submit). A token without the Name/Name(...)/
 * Name[...] shape cannot be represented as a row and is skipped; the mapper never renders such a
 * token, so this only drops hand-edited database values.
 *
 * Every row carries 'composite_options' even when it is empty, which is only ever filled for a
 * composite. This is deliberate: the field is declared on the row form type, so Symfony binds it on
 * every row regardless — a submitted row comes back with 'composite_options' => null even when the
 * request did not carry it. Emitting it only for composites would make this output disagree with the
 * shape of the bound data, which is exactly what the form round-trip compares.
 */
class ConstraintRowPresenter
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * @return list<array{name: string, options: string, composite_options: string, per_language: string}>
     */
    public static function rows(?string $raw): array
    {
        if (null === $raw || '' === trim($raw)) {
            return [];
        }

        // Displaying a definition must never fail: tokenize() enforces the grammar bounds and throws
        // past them, but a row that cannot be shown is better rendered as an empty builder than as a
        // broken page. The definition itself is unaffected — only this view of it.
        try {
            $tokens = ExtraPropertyConstraintParser::tokenize($raw);
        } catch (ExtraPropertyException) {
            return [];
        }

        $rows = [];
        $allExploded = false;
        foreach ($tokens as [$token, $line]) {
            // The first top-level All[...] feeds the per-language zone: each child becomes its own
            // per_language row, folded back into one All[...] line on serialization.
            if (!$allExploded && 1 === preg_match('/^All\s*\[(.*)\]$/s', $token, $matches)) {
                try {
                    $children = ExtraPropertyConstraintParser::tokenize($matches[1]);
                } catch (ExtraPropertyException) {
                    // Unrepresentable children (hand-edited database value): this All is dropped and
                    // the per-language zone stays available to the next top-level All.
                    continue;
                }
                $allExploded = true;
                foreach ($children as [$childToken, $childLine]) {
                    self::appendTokenRow($rows, $childToken, '1');
                }
                continue;
            }

            self::appendTokenRow($rows, $token, '0');
        }

        return $rows;
    }

    /**
     * @param list<array{name: string, options: string, composite_options: string, per_language: string}> $rows
     */
    private static function appendTokenRow(array &$rows, string $token, string $perLanguage): void
    {
        // The mapper owns the grammar: it splits the token with the same quote and delimiter rules
        // the parser applies, so the builder never drifts from what the server will accept.
        $parts = ExtraPropertyConstraintParser::splitToken($token);
        if (null === $parts) {
            // Any other shape is unrepresentable without the raw edition — dropped (see class docblock).
            return;
        }

        if (null !== $parts['children']) {
            // Composite, with or without its own options tail:
            // "All[ Url ]" or "Collection(allowExtraFields: true)[ name: NotBlank ]".
            $rows[] = [
                'name' => $parts['name'],
                'options' => trim($parts['children']),
                'composite_options' => trim($parts['options'] ?? ''),
                'per_language' => $perLanguage,
            ];

            return;
        }

        $rows[] = [
            'name' => $parts['name'],
            'options' => trim($parts['options'] ?? ''),
            'composite_options' => '',
            'per_language' => $perLanguage,
        ];
    }
}
