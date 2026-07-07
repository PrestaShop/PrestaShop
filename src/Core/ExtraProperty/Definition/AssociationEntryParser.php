<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;

/**
 * Stateless parser for extra property association entries.
 *
 * Centralizes the three placement-entry grammars used by ExtraPropertyDefinition:
 *  - forms: "formId[:path[:before|after]]" (nested path segments separated by dots)
 *  - grids: "gridId[:columnId[:before|after]]"
 *  - apis:  "uriPath[:METHOD[,METHOD...]]" (split on the FIRST colon; URI templates never contain ":")
 *
 * The parse*() methods never throw — they return the best-effort decomposition of an entry.
 * The assertValid*() methods run the exact syntax checks enforced by the
 * ExtraPropertyDefinition constructor and throw InvalidExtraPropertyDefinitionException with
 * identical messages, so the VO and the BO form validation stay in sync by construction.
 * Note: they intentionally do NOT check that the formId/gridId/uriPath exists — pointing to a
 * form or grid not (yet) detected by the catalog is a supported manual override.
 */
class AssociationEntryParser
{
    /**
     * HTTP methods accepted as modifiers of an associated_apis entry.
     */
    public const ALLOWED_HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * Parses one associated_forms entry into its fully-resolved components.
     *
     * Format: "formId[:path[:before|after]]"
     * The ":" separates the form id from the field path, and the field path from the optional mode.
     * Nesting *within* the path still uses "." (e.g. "options.suppliers").
     *
     * Placement is resolved here, once, so consumers never re-interpret the entry. path is the form node
     * the field belongs to; it is null (and only then) when there is no path — the signal for fallback:
     * - no path  => fallback section (path/anchor are null).
     * - no mode  => the path is a container; path is the full path, anchor is null
     *               (the field is appended inside that node).
     * - mode set => the last raw segment is an anchor; path is its parent (the raw path minus its last
     *               segment, "" when the anchor is at the root) and anchor is that last segment
     *               (the field is positioned before/after the anchor inside the parent).
     *
     * @return array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null}
     */
    public static function parseFormEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['formId' => $entry, 'mode' => null, 'path' => null, 'anchor' => null];
        }

        $formId = substr($entry, 0, $colonPos);
        $rest = substr($entry, $colonPos + 1);

        $mode = null;
        foreach ([':before', ':after'] as $suffix) {
            if (str_ends_with($rest, $suffix)) {
                $mode = ltrim($suffix, ':');
                $rest = substr($rest, 0, -strlen($suffix));
                break;
            }
        }

        $rawPath = '' !== $rest ? $rest : null;

        if (null === $rawPath) {
            $path = null;
            $anchor = null;
        } elseif (null === $mode) {
            // Container placement: the field is appended inside the full path.
            $path = $rawPath;
            $anchor = null;
        } else {
            // Anchor placement: the field is a sibling of the last raw segment inside its parent.
            $lastDot = strrpos($rawPath, '.');
            $path = false !== $lastDot ? substr($rawPath, 0, $lastDot) : '';
            $anchor = false !== $lastDot ? substr($rawPath, $lastDot + 1) : $rawPath;
        }

        return ['formId' => $formId, 'mode' => $mode, 'path' => $path, 'anchor' => $anchor];
    }

    /**
     * Parses one associated_grids entry into its components.
     *
     * Format: "gridId[:columnId[:before|after]]"
     * The ":" separates the grid id from the column id, and the column id from the optional mode.
     * Grid columns are flat (no nesting), so the column id never contains a separator.
     *
     * Unlike parseFormEntry() — where a path without a mode means "append inside the container"
     * (mode null) — a column id without an explicit mode defaults to 'after': a grid has no
     * containers, a column is always a before/after anchor.
     *
     * @return array{gridId: string, columnId: string|null, mode: 'before'|'after'|null}
     */
    public static function parseGridEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['gridId' => $entry, 'columnId' => null, 'mode' => null];
        }

        $gridId = substr($entry, 0, $colonPos);
        $rest = substr($entry, $colonPos + 1);

        $mode = 'after';
        foreach ([':before', ':after'] as $suffix) {
            if (str_ends_with($rest, $suffix)) {
                $mode = ltrim($suffix, ':');
                $rest = substr($rest, 0, -strlen($suffix));
                break;
            }
        }

        return ['gridId' => $gridId, 'columnId' => '' !== $rest ? $rest : null, 'mode' => '' !== $rest ? $mode : null];
    }

    /**
     * Parses one associated_apis entry.
     *
     * Format: "uriPath[:METHOD[,METHOD...]]"
     * The ":" separates the URI path from an optional comma-separated HTTP method list; URI
     * templates never contain ":", so splitting on the first ":" is unambiguous. With no method
     * list the entry matches every HTTP method on that URI template.
     *
     * @return array{path: string, methods: list<string>|null}
     */
    public static function parseApiEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['path' => self::normalizeApiPath($entry), 'methods' => null];
        }

        $path = self::normalizeApiPath(substr($entry, 0, $colonPos));
        $methodsSpec = trim(substr($entry, $colonPos + 1));
        if ('' === $methodsSpec) {
            return ['path' => $path, 'methods' => null];
        }

        $methods = array_values(array_filter(
            array_map(static fn (string $m): string => strtoupper(trim($m)), explode(',', $methodsSpec)),
            static fn (string $m): bool => '' !== $m
        ));

        return ['path' => $path, 'methods' => [] !== $methods ? $methods : null];
    }

    /**
     * Normalizes a URI path for comparison: trims, forces a single leading slash, and drops a
     * trailing slash (except for the root "/").
     */
    public static function normalizeApiPath(string $path): string
    {
        $normalized = '/' . ltrim(trim($path), '/');

        return '/' !== $normalized ? rtrim($normalized, '/') : $normalized;
    }

    /**
     * Parses an associated_forms entry and enforces its syntax rules (non-empty formId).
     *
     * @return array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null}
     *
     * @throws InvalidExtraPropertyDefinitionException when the formId part is empty
     */
    public static function assertValidFormEntry(string $entry): array
    {
        $parsed = self::parseFormEntry($entry);
        if ('' === $parsed['formId']) {
            // prefixed() keeps the bare message retrievable for consumers already pointing at
            // the offending entry (e.g. a placement form row).
            throw InvalidExtraPropertyDefinitionException::prefixed('ExtraPropertyDefinition: ', sprintf(
                'invalid associatedForms entry "%s" — formId must not be empty.',
                $entry
            ));
        }

        return $parsed;
    }

    /**
     * Parses an associated_grids entry and enforces its syntax rules (non-empty gridId).
     *
     * @return array{gridId: string, columnId: string|null, mode: 'before'|'after'|null}
     *
     * @throws InvalidExtraPropertyDefinitionException when the gridId part is empty
     */
    public static function assertValidGridEntry(string $entry): array
    {
        $parsed = self::parseGridEntry($entry);
        if ('' === $parsed['gridId']) {
            throw InvalidExtraPropertyDefinitionException::prefixed('ExtraPropertyDefinition: ', sprintf(
                'invalid associatedGrids entry "%s" — gridId must not be empty.',
                $entry
            ));
        }

        return $parsed;
    }

    /**
     * Parses an associated_apis entry and enforces its syntax rules (non-empty URI path,
     * whitelisted HTTP methods).
     *
     * @return array{path: string, methods: list<string>|null}
     *
     * @throws InvalidExtraPropertyDefinitionException when the URI path is empty or a method is not allowed
     */
    public static function assertValidApiEntry(string $entry): array
    {
        // The emptiness check runs on the RAW path (before normalizeApiPath() forces a leading
        // slash), otherwise an entry like ":GET" would normalize to "/" and slip through.
        $colonPos = strpos($entry, ':');
        $rawPath = trim(false !== $colonPos ? substr($entry, 0, $colonPos) : $entry);
        if ('' === $rawPath) {
            throw InvalidExtraPropertyDefinitionException::prefixed('ExtraPropertyDefinition: ', sprintf(
                'invalid associatedApis entry "%s" — URI path must not be empty.',
                $entry
            ));
        }

        $parsed = self::parseApiEntry($entry);
        foreach ($parsed['methods'] ?? [] as $method) {
            if (!in_array($method, self::ALLOWED_HTTP_METHODS, true)) {
                throw InvalidExtraPropertyDefinitionException::prefixed('ExtraPropertyDefinition: ', sprintf(
                    'invalid HTTP method "%s" in associatedApis entry "%s" (allowed: %s).',
                    $method,
                    $entry,
                    implode(', ', self::ALLOWED_HTTP_METHODS)
                ));
            }
        }

        return $parsed;
    }
}
