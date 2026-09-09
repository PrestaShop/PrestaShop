<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Constraint;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Composite;
use Throwable;

/**
 * Renders Symfony Constraint instances into the extra property constraint DSL — the inverse of
 * ExtraPropertyConstraintParser, and the only way a constraint reaches the registry column.
 *
 * Composites render with indentation and brackets, a constraint whose only configured option is
 * its default option renders the positional shape ("TypedRegex('generic_name')",
 * "Choice(['a', 'b'])") and any other configured options render the named shape
 * ("Length(min: 2, max: 64)").
 *
 * Two guarantees are enforced on every render, and they are independent:
 *
 * - **Fidelity** — what is rendered re-parses to the very same constraints. Rather than
 *   maintaining an exhaustive per-constraint option schema, render() parses what it just produced
 *   and compares the two graphs strictly. Anything the grammar cannot carry (a class outside the
 *   allowlist, an option holding an object, a value whose type would drift) is refused instead of
 *   being dropped silently and weakening the declared validation weeks later.
 * - **Safety** — the round-trip says nothing about danger: an executable option such as
 *   `normalizer: 'trim'` would survive it perfectly. Those options are refused by the parser on the
 *   way back, and the options the DSL never carries (groups, payload) are refused here when they
 *   hold a non-default value.
 *
 * Rendering fails closed: it only ever receives Constraint objects (module code, or objects the
 * parser already built), so there is nothing to tolerate — the write path must refuse what it
 * cannot store, and a read-path failure exposes a corrupt definition instead of displaying a wrong
 * one.
 */
class ExtraPropertyConstraintRenderer
{
    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * Renders constraints into the DSL, one top-level constraint per line.
     *
     * @param list<Constraint>|null $constraints
     *
     * @return string|null null for no constraints, mirroring an empty registry column
     *
     * @throws InvalidExtraPropertyConstraintException when a constraint cannot be represented
     *                                                 safely or would not survive the round-trip
     */
    public static function render(?array $constraints): ?string
    {
        if (null === $constraints || [] === $constraints) {
            return null;
        }
        if (!array_is_list($constraints)) {
            throw new InvalidExtraPropertyConstraintException('Extra property constraints must be provided as a list.');
        }

        $lines = [];
        foreach ($constraints as $index => $constraint) {
            if (!$constraint instanceof Constraint) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Extra property constraint at index %d must extend %s, got %s.',
                    $index,
                    Constraint::class,
                    get_debug_type($constraint)
                ));
            }
            $lines[] = self::renderConstraint($constraint, 0);
        }
        $rendered = implode("\n", $lines);

        // Fidelity contract: what we render must parse back to what we were given. This is what
        // removes the need for an exhaustive option schema per constraint class.
        $decoded = ExtraPropertyConstraintParser::parse($rendered);
        if ($decoded->hasRejections()) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraints cannot be represented in the extra property constraint format: the rendered form is not readable back (%s).',
                implode('; ', $decoded->getRejectionMessages())
            ));
        }
        if (self::toComparableForm($constraints) !== self::toComparableForm($decoded->getConstraints() ?? [])) {
            throw new InvalidExtraPropertyConstraintException(
                'Constraints cannot be represented without loss: parsing the rendered form does not yield the original constraints. '
                . 'This usually means an option or a value type the extra property constraint format does not carry.'
            );
        }

        return $rendered;
    }

    /**
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function renderConstraint(Constraint $constraint, int $indent): string
    {
        // A composite built in PHP can reference itself; the indent doubles as the recursion bound.
        if ($indent > ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint nesting exceeds the maximum depth of %d.',
                ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH
            ));
        }

        $pad = str_repeat('  ', $indent);
        $alias = self::aliasOf($constraint);

        if ($constraint instanceof Composite) {
            // The children travel in their own "[...]" tail, so the option carrying them must not be
            // rendered a second time as a regular option.
            $configured = self::configuredOptions($constraint);
            unset($configured[ExtraPropertyConstraintGrammar::childrenOptionOf($constraint::class)]);
            $optionsTail = self::renderOptionsTail($configured, $alias);

            $nested = $constraint->getNestedConstraints();
            if ([] === $nested) {
                return $pad . $alias . $optionsTail . '[]';
            }

            $children = [];
            foreach ($nested as $key => $child) {
                $rendered = self::renderConstraint($child, $indent + 1);
                // Collection keys its nested constraints by field name; prefix them so they stay readable.
                if (is_string($key)) {
                    $rendered = str_repeat('  ', $indent + 1) . $key . ': ' . ltrim($rendered);
                }
                $children[] = $rendered;
            }

            return $pad . $alias . $optionsTail . "[\n" . implode(",\n", $children) . "\n" . $pad . ']';
        }

        $configured = self::configuredOptions($constraint);
        $defaultOption = $constraint->getDefaultOption();

        // Positional shape when the default option is the only configured one.
        if (null !== $defaultOption && [$defaultOption] === array_keys($configured)) {
            $value = $configured[$defaultOption];
            if (is_scalar($value) && '' !== (string) $value) {
                return $pad . $alias . '(' . self::renderValue($value) . ')';
            }
            if (is_array($value) && [] !== $value && self::isScalarList($value)) {
                return $pad . $alias . '([' . implode(', ', array_map(self::renderValue(...), $value)) . '])';
            }
        }

        return $pad . $alias . self::renderOptionsTail($configured, $alias);
    }

    /**
     * Renders the "(option: value, ...)" tail, alphabetically for determinism, or an empty string
     * when nothing is configured.
     *
     * Anything the grammar cannot express raises instead of being dropped: this render is the
     * persisted form, so a silent omission would be a silent loss of validation.
     *
     * @param array<string, mixed> $options
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function renderOptionsTail(array $options, string $alias): string
    {
        if ([] === $options) {
            return '';
        }

        ksort($options);
        $rendered = [];
        foreach ($options as $option => $value) {
            if (is_scalar($value)) {
                $rendered[] = $option . ': ' . self::renderValue($value);
            } elseif (is_array($value) && self::isScalarList($value)) {
                $rendered[] = $option . ': [' . implode(', ', array_map(self::renderValue(...), $value)) . ']';
            } else {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" of constraint "%s" holds a %s, which the extra property constraint format cannot represent. Only scalars and lists of scalars are supported.',
                    $option,
                    $alias,
                    get_debug_type($value)
                ));
            }
        }

        return '(' . implode(', ', $rendered) . ')';
    }

    /**
     * The grammar alias of a constraint instance.
     *
     * @throws InvalidExtraPropertyConstraintException when the class is outside the grammar
     */
    private static function aliasOf(Constraint $constraint): string
    {
        $alias = ExtraPropertyConstraintGrammar::aliasOf($constraint::class);
        if (null === $alias) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint class "%s" is not part of the extra property constraint grammar and cannot be represented.',
                $constraint::class
            ));
        }

        return $alias;
    }

    /**
     * The constraint's public options whose value differs from a default-constructed instance
     * (or from the declared property defaults when the constraint has required options and cannot
     * be default-constructed). Internal sentinels (e.g. PositiveOrZero's value=0) are therefore
     * not reported.
     *
     * The options the DSL never carries (groups, payload) are not reported either — but a
     * non-default value there is refused rather than lost, since dropping it would silently change
     * what the module or merchant declared.
     *
     * @return array<string, mixed>
     *
     * @throws InvalidExtraPropertyConstraintException
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
            if ($property->isStatic() || !$property->isInitialized($constraint)) {
                continue;
            }
            $option = $property->getName();
            $value = $constraint->{$option};

            if (!ExtraPropertyConstraintGrammar::isRenderableOption($option)) {
                if ('groups' === $option && [Constraint::DEFAULT_GROUP] !== $value) {
                    throw new InvalidExtraPropertyConstraintException(sprintf(
                        'Validation groups are not supported for extra property constraints (constraint "%s"); only the default group applies.',
                        self::aliasOf($constraint)
                    ));
                }
                if ('payload' === $option && null !== $value) {
                    throw new InvalidExtraPropertyConstraintException(sprintf(
                        'The "payload" option is not supported for extra property constraints (constraint "%s").',
                        self::aliasOf($constraint)
                    ));
                }

                continue;
            }

            if (null === $value || $value === ($defaults[$option] ?? null)) {
                continue;
            }
            $configured[$option] = $value;
        }

        return $configured;
    }

    /**
     * Renders a single value: strings are single-quoted with \\ and \' escaped (so any string
     * round-trips back identically), numbers are left bare and booleans render as the true/false
     * literals. Inverse of the parser's value reading.
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

    /**
     * Reduces a constraint graph to nested arrays of scalars, keeping class names, keys, order and
     * scalar types, so a plain `===` becomes a strict structural comparison.
     *
     * PHP's `==` is deliberately not used: it treats 5 and '5', or ['1'] and [1], as equal, whereas
     * the DSL distinguishes them on purpose. A type drift would therefore pass unnoticed through a
     * loose comparison, which is exactly what the fidelity contract exists to catch. groups and
     * payload are left out: they are never rendered, and configuredOptions() already refused any
     * non-default value.
     */
    private static function toComparableForm(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = self::toComparableForm($item);
            }

            return $normalized;
        }

        if (!is_object($value)) {
            // Keeps 5 and '5' apart, which a loose comparison would not.
            return [get_debug_type($value), $value];
        }

        $normalized = ['#class' => $value::class];
        $properties = (new ReflectionObject($value))->getProperties();
        usort($properties, static fn (ReflectionProperty $a, ReflectionProperty $b): int => strcmp($a->getName(), $b->getName()));
        foreach ($properties as $property) {
            if ($property->isStatic() || !ExtraPropertyConstraintGrammar::isRenderableOption($property->getName())) {
                continue;
            }
            $normalized[$property->getName()] = $property->isInitialized($value)
                ? self::toComparableForm($property->getValue($value))
                : '#uninitialized';
        }

        return $normalized;
    }
}
