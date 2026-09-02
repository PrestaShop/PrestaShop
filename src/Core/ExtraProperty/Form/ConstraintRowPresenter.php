<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * Splits a constraints DSL string (the mapper's toNames() render) into the row models backing the
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
     * @return list<array{name: string, options: string, per_language: string}>
     */
    public static function rows(?string $raw): array
    {
        if (null === $raw || '' === trim($raw)) {
            return [];
        }

        $rows = [];
        $allExploded = false;
        foreach (ExtraPropertyConstraintMapper::tokenize($raw) as [$token, $line]) {
            // The first top-level All[...] feeds the per-language zone: each child becomes its own
            // per_language row, folded back into one All[...] line on serialization.
            if (!$allExploded && 1 === preg_match('/^All\s*\[(.*)\]$/s', $token, $matches)) {
                $allExploded = true;
                foreach (ExtraPropertyConstraintMapper::tokenize($matches[1]) as [$childToken, $childLine]) {
                    self::appendTokenRow($rows, $childToken, '1');
                }
                continue;
            }

            self::appendTokenRow($rows, $token, '0');
        }

        return $rows;
    }

    /**
     * @param list<array{name: string, options: string, per_language: string}> $rows
     */
    private static function appendTokenRow(array &$rows, string $token, string $perLanguage): void
    {
        // Composite shape: Name[ nested, constraints ]
        if (1 === preg_match('/^(\w+)\s*\[(.*)\]$/s', $token, $matches)) {
            $rows[] = ['name' => $matches[1], 'options' => trim($matches[2]), 'per_language' => $perLanguage];

            return;
        }

        // Regular shape: Name or Name(argument)
        if (1 === preg_match('/^(\w+)\s*(?:\((.*)\))?$/s', $token, $matches)) {
            $rows[] = ['name' => $matches[1], 'options' => trim($matches[2] ?? ''), 'per_language' => $perLanguage];
        }

        // Any other shape is unrepresentable without the raw edition — dropped (see class docblock).
    }
}
