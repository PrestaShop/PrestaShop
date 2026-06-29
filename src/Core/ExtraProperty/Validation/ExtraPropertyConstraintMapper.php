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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Composite;
use Throwable;

/**
 * Maps the BO "Validation" card's plain-text constraint names ("one per line", or comma-separated)
 * to real Symfony Constraint instances, and back.
 *
 * Parse direction (fromNames): a whitelist of names, each either bare ("NotBlank") or carrying a
 * single value via the constraint's default option — scalar ("TypedRegex('generic_name')",
 * "GreaterThan(5)") or a flat list ("Choice(['a', 'b', 'c'])"). Unknown names and bad arguments
 * raise an exception rather than being silently dropped.
 *
 * Value typing is explicit: a 'single'- or "double"-quoted value is always a string, while an
 * unquoted value is a number when numeric (int/float) and a string otherwise. This is what tells
 * "01" (string) apart from 01 (int 1), and 5 (int) apart from "5" (string).
 *
 * Display direction (toNames): generic and lossless. Any constraint is rendered, including composites
 * (All, Collection, …) with indentation and parametric constraints showing their primary option;
 * string values are emitted single-quoted and numbers bare, so the output parses back identically.
 * This is what powers the read-only "view" page of a module-owned definition, whose constraints may
 * be richer than the textarea can write back.
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
        'Type' => Assert\Type::class,
        // PrestaShop custom
        'TypedRegex' => TypedRegex::class,
        'DefaultLanguage' => DefaultLanguage::class,
        'CleanHtml' => CleanHtml::class,
    ];

    /**
     * Parses a "one constraint per line (or comma-separated)" textarea value into Constraint instances.
     *
     * Each token is a whitelisted name, optionally followed by a value: a scalar ("TypedRegex(generic_name)")
     * or a flat list ("Choice([a, b, c])"). Blank tokens are skipped.
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
        foreach (self::tokenize($rawNames) as $token) {
            $constraints[] = self::parseToken($token);
        }

        return [] !== $constraints ? $constraints : null;
    }

    /**
     * Formats a list of Constraint instances back into the textarea's "one per line" shape.
     *
     * Lossless and generic: every constraint is rendered. Composites (All, Collection, …) are shown with
     * their nested constraints indented; a constraint with a primary option shows it as a scalar
     * ("TypedRegex(generic_name)") or a flat list ("Choice([a, b, c])"). Names and arguments that
     * fromNames can parse back round-trip exactly.
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
     * Splits the raw value into constraint tokens on top-level "," or newline separators.
     *
     * @return list<string>
     */
    private static function tokenize(string $raw): array
    {
        return self::splitTopLevel($raw, ",\n");
    }

    /**
     * Splits a string on any of the given separator characters, but only at the top level — separators
     * inside "(...)"/"[...]" or inside a 'single'- or "double"-quoted run are part of a value and kept.
     * Returned parts are trimmed and the empty ones dropped.
     *
     * @return list<string>
     */
    private static function splitTopLevel(string $raw, string $separators): array
    {
        $parts = [];
        $buffer = '';
        $depth = 0;
        $quote = null;
        $length = strlen($raw);

        for ($i = 0; $i < $length; ++$i) {
            $char = $raw[$i];

            if (null !== $quote) {
                $buffer .= $char;
                if ($char === $quote) {
                    $quote = null;
                }
            } elseif ("'" === $char || '"' === $char) {
                $quote = $char;
                $buffer .= $char;
            } elseif ('(' === $char || '[' === $char) {
                ++$depth;
                $buffer .= $char;
            } elseif (')' === $char || ']' === $char) {
                $depth = max(0, $depth - 1);
                $buffer .= $char;
            } elseif (0 === $depth && str_contains($separators, $char)) {
                $parts[] = $buffer;
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }
        $parts[] = $buffer;

        return array_values(array_filter(
            array_map('trim', $parts),
            static fn (string $part): bool => '' !== $part
        ));
    }

    /**
     * @throws UnknownExtraPropertyConstraintException
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function parseToken(string $token): Constraint
    {
        if (1 !== preg_match('/^(\w+)\s*(?:\((.*)\))?$/s', $token, $matches)) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Malformed constraint "%s". Use a name optionally followed by a value, e.g. NotBlank or TypedRegex(generic_name).',
                $token
            ));
        }

        $name = $matches[1];
        if (!isset(self::ALLOWED_CONSTRAINTS[$name])) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Unknown extra property constraint "%s". Allowed constraints: %s.',
                $name,
                implode(', ', self::getAllowedNames())
            ));
        }

        $fqcn = self::ALLOWED_CONSTRAINTS[$name];
        $hasArgument = isset($matches[2]) && '' !== trim($matches[2]);

        if (!$hasArgument) {
            return self::instantiate($fqcn, $token, null);
        }

        $defaultOption = self::defaultOptionOf($fqcn);
        if (null === $defaultOption) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint "%s" does not accept a value.',
                $name
            ));
        }

        return self::instantiate($fqcn, $token, [$defaultOption => self::parseArgument(trim($matches[2]))]);
    }

    /**
     * Builds the constraint, converting any construction failure (missing required value, wrong
     * argument type, …) into a friendly domain exception.
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
     * by parseValue() — quoted values stay strings, unquoted numeric values become int/float.
     *
     * @return string|int|float|list<string|int|float>
     */
    private static function parseArgument(string $raw): string|int|float|array
    {
        if (1 !== preg_match('/^\[(.*)\]$/s', $raw, $matches)) {
            return self::parseValue($raw);
        }

        return array_map(self::parseValue(...), self::splitTopLevel($matches[1], ','));
    }

    /**
     * Reads a single value: a 'single'- or "double"-quoted run is taken literally as a string;
     * otherwise a numeric value is cast to its natural int/float type and anything else stays a string.
     * This is what lets "01" (string) and 01 (int 1) be told apart.
     */
    private static function parseValue(string $raw): string|int|float
    {
        if (strlen($raw) >= 2
            && (("'" === $raw[0] && "'" === $raw[-1]) || ('"' === $raw[0] && '"' === $raw[-1]))
        ) {
            return substr($raw, 1, -1);
        }

        if (1 === preg_match('/^-?\d+$/', $raw)) {
            return (int) $raw;
        }

        return is_numeric($raw) ? (float) $raw : $raw;
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

        $defaultOption = $constraint->getDefaultOption();
        if (null !== $defaultOption && isset($constraint->{$defaultOption}) && self::isConfigured($constraint, $defaultOption)) {
            $value = $constraint->{$defaultOption};
            if (is_scalar($value) && '' !== (string) $value) {
                return $pad . $shortName . '(' . self::renderValue($value) . ')';
            }
            if (is_array($value) && [] !== $value && self::isScalarList($value)) {
                return $pad . $shortName . '([' . implode(', ', array_map(self::renderValue(...), $value)) . '])';
            }
        }

        return $pad . $shortName;
    }

    /**
     * Renders a single value for the textarea: strings are single-quoted (so they round-trip back to a
     * string), numbers are left bare. Inverse of parseValue().
     */
    private static function renderValue(bool|int|float|string $value): string
    {
        if (is_string($value)) {
            return "'" . $value . "'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * Whether a constraint's default option holds a user-supplied value rather than the built-in
     * default — so internal sentinels (e.g. PositiveOrZero's value=0) are not rendered as an argument.
     * A constraint whose value is a required option (no default instance can be built) is always
     * considered configured.
     */
    private static function isConfigured(Constraint $constraint, string $option): bool
    {
        $class = $constraint::class;

        try {
            $default = new $class();
        } catch (Throwable) {
            return true;
        }

        $defaultValue = isset($default->{$option}) ? $default->{$option} : null;

        return $constraint->{$option} !== $defaultValue;
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
