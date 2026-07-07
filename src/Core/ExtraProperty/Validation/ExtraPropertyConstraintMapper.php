<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Composite;
use Throwable;

/**
 * Maps the BO "Validation" card's plain-text constraint names ("one per line", or comma-separated)
 * to real Symfony Constraint instances, and back.
 *
 * Parse direction (fromNames): a whitelist of names, each in one of four shapes:
 * - bare:            NotBlank
 * - positional:      TypedRegex('generic_name'), GreaterThan(5), Choice(['a', 'b', 'c'])
 *                    — the single value feeds the constraint's default option
 * - named options:   Length(min: 2, max: 64), Choice(choices: ['a', 'b'], multiple: true)
 * - composite:       All[ Url, NotBlank ] — nested constraints between brackets, any depth;
 *                    Collection keys its children: Collection[ name: NotBlank, code: Length(max: 5) ]
 * Unknown names and bad arguments raise an exception (with the offending 1-based line) rather
 * than being silently dropped.
 *
 * Value typing is explicit: a 'single'- or "double"-quoted value is always a string (backslash
 * escapes \\ \' \" are honored), while an unquoted value is a number when numeric (int/float),
 * one of the literals true/false/null, and a string otherwise. This is what tells "01" (string)
 * apart from 01 (int 1), and 5 (int) apart from "5" (string).
 *
 * Display direction (toNames): generic and lossless for whitelisted shapes. Composites render with
 * indentation and brackets, a constraint whose only configured option is its default option renders
 * the positional shape, and any other configured options render the named shape — so toNames output
 * parses back through fromNames identically. Constraints outside the whitelist (module-attached) are
 * still rendered for the read-only view page, even when fromNames would reject their name.
 */
class ExtraPropertyConstraintMapper
{
    private const ALLOWED_CONSTRAINTS = [
        // Presence
        'NotBlank' => Assert\NotBlank::class,
        'NotNull' => Assert\NotNull::class,
        'Blank' => Assert\Blank::class,
        'IsNull' => Assert\IsNull::class,
        // String / format
        'Email' => Assert\Email::class,
        'Url' => Assert\Url::class,
        'Json' => Assert\Json::class,
        'Uuid' => Assert\Uuid::class,
        'Ulid' => Assert\Ulid::class,
        'Ip' => Assert\Ip::class,
        'Cidr' => Assert\Cidr::class,
        'Hostname' => Assert\Hostname::class,
        'CssColor' => Assert\CssColor::class,
        'NoSuspiciousCharacters' => Assert\NoSuspiciousCharacters::class,
        'Length' => Assert\Length::class,
        // Regex takes a user-supplied pattern, so a pathological pattern is a theoretical ReDoS
        // at validation time. Accepted: the DSL is only writable by BO admins (or module code),
        // and PCRE's backtracking limit aborts a runaway match instead of hanging the request.
        'Regex' => Assert\Regex::class,
        // Date / time
        'Date' => Assert\Date::class,
        'DateTime' => Assert\DateTime::class,
        'Time' => Assert\Time::class,
        'Timezone' => Assert\Timezone::class,
        // Numbers
        'Positive' => Assert\Positive::class,
        'PositiveOrZero' => Assert\PositiveOrZero::class,
        'Negative' => Assert\Negative::class,
        'NegativeOrZero' => Assert\NegativeOrZero::class,
        'Luhn' => Assert\Luhn::class,
        'Range' => Assert\Range::class,
        // Comparison (value coerced to int/float when numeric)
        'EqualTo' => Assert\EqualTo::class,
        'NotEqualTo' => Assert\NotEqualTo::class,
        'IdenticalTo' => Assert\IdenticalTo::class,
        'NotIdenticalTo' => Assert\NotIdenticalTo::class,
        'LessThan' => Assert\LessThan::class,
        'LessThanOrEqual' => Assert\LessThanOrEqual::class,
        'GreaterThan' => Assert\GreaterThan::class,
        'GreaterThanOrEqual' => Assert\GreaterThanOrEqual::class,
        'DivisibleBy' => Assert\DivisibleBy::class,
        // Boolean
        'IsTrue' => Assert\IsTrue::class,
        'IsFalse' => Assert\IsFalse::class,
        // Banking / identifiers
        'Iban' => Assert\Iban::class,
        'Bic' => Assert\Bic::class,
        'Isbn' => Assert\Isbn::class,
        'Issn' => Assert\Issn::class,
        'Isin' => Assert\Isin::class,
        'CardScheme' => Assert\CardScheme::class,
        // Locale (ISO)
        'Country' => Assert\Country::class,
        'Language' => Assert\Language::class,
        'Locale' => Assert\Locale::class,
        'Currency' => Assert\Currency::class,
        // Common parametric
        'Choice' => Assert\Choice::class,
        'Count' => Assert\Count::class,
        'Type' => Assert\Type::class,
        // Composites (nested constraints between brackets)
        'All' => Assert\All::class,
        'AtLeastOneOf' => Assert\AtLeastOneOf::class,
        'Collection' => Assert\Collection::class,
        'Sequentially' => Assert\Sequentially::class,
        // PrestaShop custom
        'TypedRegex' => TypedRegex::class,
        'DefaultLanguage' => DefaultLanguage::class,
        'CleanHtml' => CleanHtml::class,
    ];

    /**
     * Constraint options that exist on every constraint and never belong in the textarea shape.
     */
    private const NON_RENDERABLE_OPTIONS = ['groups', 'payload'];

    /**
     * Parses a "one constraint per line (or comma-separated)" textarea value into Constraint instances.
     *
     * @param string|null $rawNames
     *
     * @return list<Constraint>|null
     *
     * @throws UnknownExtraPropertyConstraintException when a token is malformed or its name is not whitelisted
     * @throws InvalidExtraPropertyConstraintException when a recognized name carries an invalid argument
     */
    public static function fromNames(?string $rawNames): ?array
    {
        if (null === $rawNames || '' === trim($rawNames)) {
            return null;
        }

        $constraints = [];
        foreach (self::splitTopLevelWithLines($rawNames, ",\n") as [$token, $line]) {
            try {
                $constraints[] = self::parseToken($token);
            } catch (UnknownExtraPropertyConstraintException|InvalidExtraPropertyConstraintException $e) {
                // Same exception class, line-located message; the bare message stays retrievable
                // for consumers that already point at the offending token (e.g. a form row).
                throw $e::prefixed(sprintf('Line %d: ', $line), $e->getMessage(), $e);
            }
        }

        return [] !== $constraints ? $constraints : null;
    }

    /**
     * Formats a list of Constraint instances back into the textarea's "one per line" shape.
     *
     * Lossless for whitelisted shapes: composites are shown with their nested constraints indented,
     * a constraint whose only configured option is its default option shows the positional shape
     * ("TypedRegex('generic_name')", "Choice(['a', 'b'])") and other configured options show the
     * named shape ("Length(min: 2, max: 64)"). What toNames emits, fromNames parses back.
     *
     * @param list<Constraint>|null $constraints
     */
    public static function toNames(?array $constraints): ?string
    {
        if (null === $constraints || [] === $constraints) {
            return null;
        }

        $lines = [];
        foreach ($constraints as $constraint) {
            if ($constraint instanceof Constraint) {
                $lines[] = self::render($constraint, 0);
            }
        }

        return [] !== $lines ? implode("\n", $lines) : null;
    }

    /**
     * @return list<string>
     */
    public static function getAllowedNames(): array
    {
        return array_keys(self::ALLOWED_CONSTRAINTS);
    }

    /**
     * The whitelist itself (name => constraint FQCN), e.g. to build a machine-readable catalog.
     *
     * @return array<string, class-string<Constraint>>
     */
    public static function getAllowedConstraints(): array
    {
        return self::ALLOWED_CONSTRAINTS;
    }

    /**
     * Splits a raw textarea value into its top-level constraint tokens without interpreting them,
     * each with the 1-based line it starts on. This is the exact tokenization used by fromNames(),
     * exposed so other readers of the DSL (e.g. the BO builder's row presenter) stay pinned to the
     * same grammar authority instead of re-implementing the quote/bracket rules.
     *
     * @return list<array{0: string, 1: int}>
     */
    public static function tokenize(string $raw): array
    {
        return self::splitTopLevelWithLines($raw, ",\n");
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
    private static function parseToken(string $token): Constraint
    {
        // Composite shape: Name[ nested, constraints ]
        if (1 === preg_match('/^(\w+)\s*\[(.*)\]$/s', $token, $matches)) {
            return self::parseComposite($matches[1], $matches[2], $token);
        }

        if (1 !== preg_match('/^(\w+)\s*(?:\((.*)\))?$/s', $token, $matches)) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Malformed constraint "%s". Use a name optionally followed by a value — e.g. NotBlank, TypedRegex(generic_name), Length(min: 2, max: 64) or All[Url].',
                $token
            ));
        }

        $fqcn = self::resolveName($matches[1]);
        $hasArgument = isset($matches[2]) && '' !== trim($matches[2]);

        if (!$hasArgument) {
            return self::instantiate($fqcn, $token, null);
        }

        $inner = trim($matches[2]);

        if (self::looksLikeNamedOptions($inner)) {
            return self::instantiate($fqcn, $token, self::parseNamedOptions($inner, $token));
        }

        $defaultOption = self::defaultOptionOf($fqcn);
        if (null === $defaultOption) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint "%s" does not accept a value.',
                $matches[1]
            ));
        }

        return self::instantiate($fqcn, $token, [$defaultOption => self::parseArgument($inner)]);
    }

    /**
     * Parses the bracket shape "Name[ child, child ]" into a composite constraint. Children are
     * regular constraint tokens; Collection children are keyed ("fieldName: constraint").
     *
     * @throws UnknownExtraPropertyConstraintException
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function parseComposite(string $name, string $inner, string $token): Constraint
    {
        $fqcn = self::resolveName($name);

        if (!is_subclass_of($fqcn, Composite::class)) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint "%s" does not accept nested constraints — the [...] shape is reserved for composites (%s).',
                $name,
                implode(', ', self::compositeNames())
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
                $children[$childMatches[1]] = self::parseToken(trim($childMatches[2]));
            } else {
                $children[] = self::parseToken($childToken);
            }
        }

        if (Assert\Collection::class === $fqcn) {
            return self::instantiate($fqcn, $token, ['fields' => $children]);
        }

        // List composites (All, AtLeastOneOf, Sequentially) take the children through their default
        // option ("constraints").
        return self::instantiate($fqcn, $token, [self::defaultOptionOf($fqcn) ?? 'constraints' => $children]);
    }

    /**
     * @return class-string<Constraint>
     *
     * @throws UnknownExtraPropertyConstraintException
     */
    private static function resolveName(string $name): string
    {
        if (!isset(self::ALLOWED_CONSTRAINTS[$name])) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Unknown extra property constraint "%s". Allowed constraints: %s.',
                $name,
                implode(', ', self::getAllowedNames())
            ));
        }

        return self::ALLOWED_CONSTRAINTS[$name];
    }

    /**
     * The whitelisted names whose token uses the composite bracket shape ("Name[ children ]")
     * instead of the parenthesis shape — the same distinction parseToken() applies. Exposed so
     * other writers of the DSL (e.g. the BO builder's row serializer) stay pinned to the same
     * grammar authority.
     *
     * @return list<string>
     */
    public static function compositeNames(): array
    {
        return array_keys(array_filter(
            self::ALLOWED_CONSTRAINTS,
            static fn (string $fqcn): bool => is_subclass_of($fqcn, Composite::class)
        ));
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

    /**
     * Reads a constraint's default option name without invoking its constructor (the method returns a
     * constant and touches no instance state).
     *
     * @param class-string<Constraint> $fqcn
     */
    private static function defaultOptionOf(string $fqcn): ?string
    {
        /** @var Constraint $prototype */
        $prototype = (new ReflectionClass($fqcn))->newInstanceWithoutConstructor();

        return $prototype->getDefaultOption();
    }

    private static function render(Constraint $constraint, int $indent): string
    {
        $pad = str_repeat('  ', $indent);
        $shortName = (new ReflectionClass($constraint))->getShortName();

        if ($constraint instanceof Composite) {
            $nested = $constraint->getNestedConstraints();
            if ([] === $nested) {
                return $pad . $shortName . '[]';
            }

            $children = [];
            foreach ($nested as $key => $child) {
                $rendered = self::render($child, $indent + 1);
                // Collection keys its nested constraints by field name; prefix them so they stay readable.
                if (is_string($key)) {
                    $rendered = str_repeat('  ', $indent + 1) . $key . ': ' . ltrim($rendered);
                }
                $children[] = $rendered;
            }

            return $pad . $shortName . "[\n" . implode(",\n", $children) . "\n" . $pad . ']';
        }

        $configured = self::configuredOptions($constraint);
        $defaultOption = $constraint->getDefaultOption();

        // Positional shape when the default option is the only configured one.
        if (null !== $defaultOption && [$defaultOption] === array_keys($configured)) {
            $value = $configured[$defaultOption];
            if (is_scalar($value) && '' !== (string) $value) {
                return $pad . $shortName . '(' . self::renderValue($value) . ')';
            }
            if (is_array($value) && [] !== $value && self::isScalarList($value)) {
                return $pad . $shortName . '([' . implode(', ', array_map(self::renderValue(...), $value)) . '])';
            }
        }

        // Named shape for any other configured option combination (alphabetical for determinism).
        ksort($configured);
        $rendered = [];
        foreach ($configured as $option => $value) {
            if (is_scalar($value)) {
                $rendered[] = $option . ': ' . self::renderValue($value);
            } elseif (is_array($value) && [] !== $value && self::isScalarList($value)) {
                $rendered[] = $option . ': [' . implode(', ', array_map(self::renderValue(...), $value)) . ']';
            }
            // Non-scalar options (objects, closures, nested maps) have no textarea shape: skipped.
        }

        if ([] !== $rendered) {
            return $pad . $shortName . '(' . implode(', ', $rendered) . ')';
        }

        return $pad . $shortName;
    }

    /**
     * The constraint's public options whose value differs from a default-constructed instance
     * (or from the declared property defaults when the constraint has required options and cannot
     * be default-constructed). Internal sentinels (e.g. PositiveOrZero's value=0) are therefore
     * not reported, and neither are groups/payload.
     *
     * @return array<string, mixed>
     */
    private static function configuredOptions(Constraint $constraint): array
    {
        $reflection = new ReflectionClass($constraint);

        try {
            $defaults = get_object_vars(new ($constraint::class)());
        } catch (Throwable) {
            $defaults = $reflection->getDefaultProperties();
        }

        $configured = [];
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $option = $property->getName();
            if (in_array($option, self::NON_RENDERABLE_OPTIONS, true) || !$property->isInitialized($constraint)) {
                continue;
            }
            $value = $constraint->{$option};
            if (null === $value || $value === ($defaults[$option] ?? null)) {
                continue;
            }
            $configured[$option] = $value;
        }

        return $configured;
    }

    /**
     * Renders a single value for the textarea: strings are single-quoted with \\ and \' escaped
     * (so any string round-trips back identically), numbers are left bare and booleans render as
     * the true/false literals. Inverse of parseValue().
     */
    private static function renderValue(bool|int|float|string $value): string
    {
        if (is_string($value)) {
            return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $value) . "'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * @param array<mixed> $value
     */
    private static function isScalarList(array $value): bool
    {
        if (!array_is_list($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!is_scalar($item)) {
                return false;
            }
        }

        return true;
    }
}
