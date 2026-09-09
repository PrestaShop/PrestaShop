<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Constraint;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Throwable;

/**
 * Parses the extra property constraint DSL ("one constraint per line", or comma-separated) into
 * Symfony Constraint instances.
 *
 * Each top-level token takes one of four shapes:
 * - bare:            NotBlank
 * - positional:      TypedRegex('generic_name'), GreaterThan(5), Choice(['a', 'b', 'c'])
 *                    — the single value feeds the constraint's default option
 * - named options:   Length(min: 2, max: 64), Choice(choices: ['a', 'b'], multiple: true)
 * - composite:       All[ Url, NotBlank ] — nested constraints between brackets, any depth;
 *                    Collection keys its children: Collection[ name: NotBlank, code: Length(max: 5) ]
 *                    and a composite may carry its own options ahead of them:
 *                    Collection(allowExtraFields: true)[ name: NotBlank ]
 *
 * Value typing is explicit: a 'single'- or "double"-quoted value is always a string (backslash
 * escapes \\ \' \" are honored), while an unquoted value is a number when numeric (int/float),
 * one of the literals true/false/null, and a string otherwise. This is what tells "01" (string)
 * apart from 01 (int 1), and 5 (int) apart from "5" (string).
 *
 * Names are resolved against the ExtraPropertyConstraintGrammar allowlist and never taken from
 * the text; options Symfony invokes at validation time are refused. Parsing never throws: the
 * stored DSL is parsed on front-office requests too, so a corrupt or tampered row must not take a
 * page down. Each top-level token is decoded on its own — a failing one is reported as a
 * rejection while its valid siblings stay active, and a composite is always dropped as a whole
 * (a partially decoded composite would silently weaken the validation it describes). The caller
 * decides what a rejection means: a command refuses the whole input, the repository logs it.
 */
class ExtraPropertyConstraintParser
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * Parses a DSL value into constraints, reporting every token it had to drop.
     */
    public static function parse(?string $raw): DecodedConstraints
    {
        if (null === $raw || '' === trim($raw)) {
            return new DecodedConstraints();
        }

        try {
            $tokens = self::tokenize($raw);
        } catch (InvalidExtraPropertyConstraintException $exception) {
            // A bound exceeded rejects the whole definition: nothing can be trusted past it.
            return new DecodedConstraints([], [['index' => null, 'line' => null, 'reason' => $exception->getMessage()]]);
        }

        $constraints = [];
        $rejections = [];
        foreach ($tokens as $index => [$token, $line]) {
            try {
                $constraints[] = self::parseToken($token);
            } catch (UnknownExtraPropertyConstraintException|InvalidExtraPropertyConstraintException $exception) {
                $rejections[] = ['index' => $index, 'line' => $line, 'reason' => $exception->getMessage()];
            }
        }

        return new DecodedConstraints($constraints, $rejections);
    }

    /**
     * Splits a raw DSL value into its top-level constraint tokens without interpreting them, each
     * with the 1-based line it starts on, and enforces the grammar bounds. Exposed so other readers
     * of the DSL (e.g. the BO builder's row presenter) stay pinned to the same grammar authority
     * instead of re-implementing the quote/bracket rules.
     *
     * @return list<array{0: string, 1: int}>
     *
     * @throws InvalidExtraPropertyConstraintException when the value exceeds a grammar bound
     */
    public static function tokenize(string $raw): array
    {
        if (strlen($raw) > ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint definition exceeds the maximum length of %d characters.',
                ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH
            ));
        }

        $tokens = self::splitTopLevelWithLines($raw, ",\n");
        if (count($tokens) > ExtraPropertyConstraintGrammar::MAX_TOKENS) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint definition exceeds the maximum of %d top-level constraints.',
                ExtraPropertyConstraintGrammar::MAX_TOKENS
            ));
        }

        return $tokens;
    }

    /**
     * Splits one token into its name, its optional "(...)" options tail and its optional "[...]"
     * children tail, or returns null when the token does not match the grammar.
     *
     * Exposed so the BO builder's row presenter splits tokens with the exact same quote and
     * delimiter rules as the parser, instead of re-implementing them with a weaker regex.
     *
     * @return array{name: string, options: string|null, children: string|null}|null
     */
    public static function splitToken(string $token): ?array
    {
        return self::splitTokenParts(trim($token));
    }

    /**
     * Splits a string on any of the given separator characters, but only at the top level — separators
     * inside "(...)"/"[...]" or inside a 'single'- or "double"-quoted run are part of a value and kept.
     * A backslash escapes the next character inside a quoted run. Returned parts are trimmed and the
     * empty ones dropped.
     *
     * @return list<string>
     */
    private static function splitTopLevel(string $raw, string $separators): array
    {
        return array_map(static fn (array $part): string => $part[0], self::splitTopLevelWithLines($raw, $separators));
    }

    /**
     * Same as splitTopLevel() but each part carries the 1-based line it starts on, for error messages.
     *
     * @return list<array{0: string, 1: int}>
     */
    private static function splitTopLevelWithLines(string $raw, string $separators): array
    {
        $parts = [];
        $buffer = '';
        $bufferLine = 1;
        $bufferStarted = false;
        $line = 1;
        $depth = 0;
        $quote = null;
        $length = strlen($raw);

        for ($i = 0; $i < $length; ++$i) {
            $char = $raw[$i];
            if ("\n" === $char) {
                ++$line;
            }

            if (null !== $quote) {
                $buffer .= $char;
                if ('\\' === $char && $i + 1 < $length) {
                    // Escaped character inside a quoted run: consume it blindly.
                    $buffer .= $raw[++$i];
                } elseif ($char === $quote) {
                    $quote = null;
                }
            } elseif ("'" === $char || '"' === $char) {
                $quote = $char;
                self::bufferChar($buffer, $char, $bufferStarted, $bufferLine, $line);
            } elseif ('(' === $char || '[' === $char) {
                ++$depth;
                self::bufferChar($buffer, $char, $bufferStarted, $bufferLine, $line);
            } elseif (')' === $char || ']' === $char) {
                $depth = max(0, $depth - 1);
                self::bufferChar($buffer, $char, $bufferStarted, $bufferLine, $line);
            } elseif (0 === $depth && str_contains($separators, $char)) {
                if ('' !== trim($buffer)) {
                    $parts[] = [trim($buffer), $bufferLine];
                }
                $buffer = '';
                $bufferStarted = false;
            } else {
                self::bufferChar($buffer, $char, $bufferStarted, $bufferLine, $line);
            }
        }
        if ('' !== trim($buffer)) {
            $parts[] = [trim($buffer), $bufferLine];
        }

        return $parts;
    }

    /**
     * Appends a char to the current buffer, recording the line the part starts on (ignoring the
     * leading whitespace that trim() will drop anyway).
     */
    private static function bufferChar(string &$buffer, string $char, bool &$bufferStarted, int &$bufferLine, int $line): void
    {
        if (!$bufferStarted && !ctype_space($char)) {
            $bufferStarted = true;
            $bufferLine = $line;
        }
        $buffer .= $char;
    }

    /**
     * @throws UnknownExtraPropertyConstraintException
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function parseToken(string $token, int $depth = 0, bool $allowInternal = false): Constraint
    {
        if ($depth > ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint nesting exceeds the maximum depth of %d.',
                ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH
            ));
        }

        $parts = self::splitTokenParts($token);
        if (null === $parts) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Malformed constraint "%s". Use a name optionally followed by a value and/or nested constraints — e.g. NotBlank, TypedRegex(generic_name), Length(min: 2, max: 64), All[Url] or Collection(allowExtraFields: true)[ name: NotBlank ].',
                $token
            ));
        }

        // Composite shape: Name[ children ], optionally preceded by its own options.
        if (null !== $parts['children']) {
            return self::parseComposite($parts['name'], $parts['children'], $parts['options'], $token, $depth, $allowInternal);
        }

        $fqcn = ExtraPropertyConstraintGrammar::resolveName($parts['name'], $allowInternal);
        $inner = null !== $parts['options'] ? trim($parts['options']) : '';

        if ('' === $inner) {
            return self::instantiate($fqcn, $token, null);
        }

        if (self::looksLikeNamedOptions($inner)) {
            return self::instantiate($fqcn, $token, self::assertOptionsAllowed(self::parseNamedOptions($inner, $token), $token));
        }

        $defaultOption = ExtraPropertyConstraintGrammar::defaultOptionOf($fqcn);
        if (null === $defaultOption) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint "%s" does not accept a value.',
                $parts['name']
            ));
        }

        return self::instantiate($fqcn, $token, self::assertOptionsAllowed([$defaultOption => self::parseArgument($inner)], $token));
    }

    /**
     * Splits a token into its name, its optional "(...)" options tail and its optional "[...]"
     * children tail, honoring quoted runs and nested delimiters. Returns null when the token has a
     * shape this grammar does not describe (missing name, unbalanced delimiters, trailing text).
     *
     * Both tails may be present, in that order: "Collection(allowExtraFields: true)[ name: NotBlank ]".
     *
     * @return array{name: string, options: string|null, children: string|null}|null
     */
    private static function splitTokenParts(string $token): ?array
    {
        $length = strlen($token);
        $cursor = 0;
        while ($cursor < $length && (ctype_alnum($token[$cursor]) || '_' === $token[$cursor])) {
            ++$cursor;
        }
        if (0 === $cursor) {
            return null;
        }

        $name = substr($token, 0, $cursor);
        $options = null;
        $children = null;

        $cursor = self::skipWhitespace($token, $cursor);
        if ($cursor < $length && '(' === $token[$cursor]) {
            $end = self::matchingDelimiter($token, $cursor, '(', ')');
            if (null === $end) {
                return null;
            }
            $options = substr($token, $cursor + 1, $end - $cursor - 1);
            $cursor = self::skipWhitespace($token, $end + 1);
        }

        if ($cursor < $length && '[' === $token[$cursor]) {
            $end = self::matchingDelimiter($token, $cursor, '[', ']');
            if (null === $end) {
                return null;
            }
            $children = substr($token, $cursor + 1, $end - $cursor - 1);
            $cursor = self::skipWhitespace($token, $end + 1);
        }

        // Anything left after both tails means the token is not fully described by this grammar.
        return $cursor === $length ? ['name' => $name, 'options' => $options, 'children' => $children] : null;
    }

    /**
     * Returns the offset of the delimiter closing the run opened at $start, or null when the run is
     * never closed. Quoted sections (with backslash escapes) are skipped, and nested pairs counted.
     */
    private static function matchingDelimiter(string $raw, int $start, string $open, string $close): ?int
    {
        $length = strlen($raw);
        $depth = 0;
        $quote = null;

        for ($i = $start; $i < $length; ++$i) {
            $char = $raw[$i];
            if (null !== $quote) {
                if ('\\' === $char && $i + 1 < $length) {
                    ++$i;
                } elseif ($char === $quote) {
                    $quote = null;
                }

                continue;
            }
            if ("'" === $char || '"' === $char) {
                $quote = $char;
            } elseif ($char === $open) {
                ++$depth;
            } elseif ($char === $close && 0 === --$depth) {
                return $i;
            }
        }

        return null;
    }

    private static function skipWhitespace(string $raw, int $cursor): int
    {
        $length = strlen($raw);
        while ($cursor < $length && ctype_space($raw[$cursor])) {
            ++$cursor;
        }

        return $cursor;
    }

    /**
     * Rejects the options that make a stored constraint executable or able to traverse the
     * validated object. They are refused wherever a constraint is built — the BO builder, a CQRS
     * command and the registry row alike — because the DSL is also the persisted format.
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function assertOptionsAllowed(array $options, string $token): array
    {
        foreach ($options as $option => $value) {
            if (null === $value) {
                continue;
            }
            if (ExtraPropertyConstraintGrammar::isCallableOption($option)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" is not supported in constraint "%s" because it may execute a callable. Normalize the value in module code before validation.',
                    $option,
                    $token
                ));
            }
            if (ExtraPropertyConstraintGrammar::isForbiddenOption($option)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" is not supported in constraint "%s" because it may traverse the validated object. An extra property constraint validates a single value.',
                    $option,
                    $token
                ));
            }
        }

        return $options;
    }

    /**
     * Parses the bracket shape "Name[ child, child ]" into a composite constraint. Children are
     * regular constraint tokens; Collection children are keyed ("fieldName: constraint").
     *
     * @throws UnknownExtraPropertyConstraintException
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function parseComposite(
        string $name,
        string $inner,
        ?string $rawOptions,
        string $token,
        int $depth = 0,
        bool $allowInternal = false
    ): Constraint {
        $fqcn = ExtraPropertyConstraintGrammar::resolveName($name, $allowInternal);

        if (!ExtraPropertyConstraintGrammar::isComposite($fqcn)) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint "%s" does not accept nested constraints — the [...] shape is reserved for composites (%s).',
                $name,
                implode(', ', ExtraPropertyConstraintGrammar::compositeNames())
            ));
        }

        $children = [];
        foreach (self::splitTopLevel($inner, ",\n") as $childToken) {
            // Keyed child ("fieldName: constraint") — only Collection accepts them. The colon must come
            // right after the key, before any parenthesis/bracket of the child constraint itself.
            if (1 === preg_match('/^(\w+)\s*:\s*(.+)$/s', $childToken, $childMatches)) {
                if (Assert\Collection::class !== $fqcn) {
                    throw new InvalidExtraPropertyConstraintException(sprintf(
                        'Constraint "%s" does not accept keyed nested constraints — only Collection does.',
                        $name
                    ));
                }
                // A Collection field may name its Required/Optional wrapper explicitly.
                $children[$childMatches[1]] = self::parseToken(trim($childMatches[2]), $depth + 1, true);
            } else {
                // Required/Optional only exist as Collection fields, so they stay refused here.
                $children[] = self::parseToken($childToken, $depth + 1);
            }
        }

        // A composite may carry its own options ahead of its children:
        // "Collection(allowExtraFields: true)[ name: NotBlank ]".
        $options = [];
        $trimmedOptions = null !== $rawOptions ? trim($rawOptions) : '';
        if ('' !== $trimmedOptions) {
            if (!self::looksLikeNamedOptions($trimmedOptions)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Composite constraint "%s" only accepts named options before its nested constraints — e.g. Collection(allowExtraFields: true)[ name: NotBlank ].',
                    $name
                ));
            }
            $options = self::assertOptionsAllowed(self::parseNamedOptions($trimmedOptions, $token), $token);
        }

        // The children always win over a same-named option coming from the options tail, so a
        // hand-written "constraints:"/"fields:" cannot smuggle a second children list.
        $options[ExtraPropertyConstraintGrammar::childrenOptionOf($fqcn)] = $children;

        return self::instantiate($fqcn, $token, $options);
    }

    /**
     * A parenthesised argument is a named-options list when its first top-level part reads
     * "identifier: …" — e.g. "min: 2, max: 64". A quoted value ("'a:b'") or a list ("[a, b]")
     * never matches.
     */
    private static function looksLikeNamedOptions(string $inner): bool
    {
        $parts = self::splitTopLevel($inner, ',');

        return [] !== $parts && 1 === preg_match('/^\w+\s*:/', $parts[0]);
    }

    /**
     * Parses "opt: value, opt2: [a, b]" into a constraint options array.
     *
     * @return array<string, mixed>
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function parseNamedOptions(string $inner, string $token): array
    {
        $options = [];
        foreach (self::splitTopLevel($inner, ',') as $part) {
            if (1 !== preg_match('/^(\w+)\s*:\s*(.+)$/s', $part, $matches)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Invalid option "%s" in constraint "%s". Use "option: value" pairs, e.g. Length(min: 2, max: 64).',
                    $part,
                    $token
                ));
            }
            $options[$matches[1]] = self::parseArgument(trim($matches[2]));
        }

        return $options;
    }

    /**
     * Builds the constraint, converting any construction failure (missing required value, wrong
     * argument type, unknown option, …) into a friendly domain exception.
     *
     * @param class-string<Constraint> $fqcn
     * @param array<string, mixed>|null $options
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function instantiate(string $fqcn, string $token, ?array $options): Constraint
    {
        try {
            return null === $options ? new $fqcn() : new $fqcn($options);
        } catch (Throwable $e) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Invalid constraint "%s": %s',
                $token,
                $e->getMessage()
            ), 0, $e);
        }
    }

    /**
     * A "[a, b, c]" wrapper means a flat list, anything else a single value. Each value is then read
     * by parseValue() — quoted values stay strings, unquoted numeric values become int/float and the
     * true/false/null literals become their native type.
     *
     * @return string|int|float|bool|list<string|int|float|bool|null>|null
     */
    private static function parseArgument(string $raw): string|int|float|bool|array|null
    {
        if (1 !== preg_match('/^\[(.*)\]$/s', $raw, $matches)) {
            return self::parseValue($raw);
        }

        return array_map(self::parseValue(...), self::splitTopLevel($matches[1], ','));
    }

    /**
     * Reads a single value: a 'single'- or "double"-quoted run is taken literally as a string (with
     * \\ \' \" backslash escapes honored); otherwise the true/false/null literals become their native
     * type, a numeric value is cast to its natural int/float type and anything else stays a string.
     * This is what lets "01" (string) and 01 (int 1), and 'true' (string) and true (bool), be told apart.
     */
    private static function parseValue(string $raw): string|int|float|bool|null
    {
        if (strlen($raw) >= 2
            && (("'" === $raw[0] && "'" === $raw[-1]) || ('"' === $raw[0] && '"' === $raw[-1]))
        ) {
            return self::unescapeQuoted(substr($raw, 1, -1));
        }

        switch ($raw) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }

        if (1 === preg_match('/^-?\d+$/', $raw)) {
            return (int) $raw;
        }

        return is_numeric($raw) ? (float) $raw : $raw;
    }

    /**
     * Resolves the \\ \' \" escapes inside a quoted run. Any other backslash sequence is kept
     * verbatim (so a hand-typed Regex('/^\d+$/') keeps its \d).
     */
    private static function unescapeQuoted(string $raw): string
    {
        $result = '';
        $length = strlen($raw);
        for ($i = 0; $i < $length; ++$i) {
            $char = $raw[$i];
            if ('\\' === $char && $i + 1 < $length && str_contains('\\\'"', $raw[$i + 1])) {
                $result .= $raw[++$i];
            } else {
                $result .= $char;
            }
        }

        return $result;
    }
}
