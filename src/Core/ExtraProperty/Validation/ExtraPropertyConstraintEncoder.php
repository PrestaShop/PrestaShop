<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use ReflectionObject;
use Symfony\Component\Validator\Constraint;

/**
 * Converts extra property constraints to and from their persisted form.
 *
 * The persisted form is the constraint DSL itself — the very text the back-office "Validation" field
 * shows — so the registry column holds no serialized PHP object graph at all. There is therefore no
 * `unserialize()` anywhere on this path: reading a row means parsing a grammar whose class names are
 * resolved exclusively against a core-owned allowlist, never taken from the stored value.
 *
 * Two guarantees are enforced here, and they are independent:
 *
 * - **Fidelity** — everything written is re-read identically. Rather than maintaining an exhaustive
 *   per-constraint option schema, encode() decodes what it just produced and compares the two graphs
 *   strictly. Anything the grammar cannot carry is refused at write time instead of being silently
 *   dropped weeks later.
 * - **Safety** — the round-trip says nothing about danger: an executable option such as
 *   `normalizer: 'system'` survives it perfectly. Callable and property-path options are therefore
 *   rejected explicitly, by the grammar on the way in and by this encoder on the way out.
 *
 * Failure policy is asymmetric on purpose, and carried by the method names rather than by a runtime
 * flag, so a caller cannot pick the wrong one silently:
 * - {@see self::encode()} and {@see self::decode()} fail closed — they throw, and nothing is persisted;
 * - {@see self::decodeTolerant()} fails safe — it drops the offending root constraint, reports it in
 *   its result and keeps the valid siblings, because a corrupt row must never break a front-office
 *   render.
 *
 * Usable as a service (its constructor takes no argument) and instantiable on demand where no
 * container is available.
 */
class ExtraPropertyConstraintEncoder
{
    /**
     * Encodes constraints into the value stored in the registry.
     *
     * @param list<Constraint>|null $constraints
     *
     * @throws InvalidExtraPropertyConstraintException when a constraint cannot be represented safely
     *                                                 or would not survive the round-trip
     */
    public function encode(?array $constraints): ?string
    {
        if (null === $constraints || [] === $constraints) {
            return null;
        }

        $this->assertConstraintList($constraints);

        $encoded = ExtraPropertyConstraintMapper::toNames($constraints);
        if (null === $encoded) {
            return null;
        }

        // Fidelity contract: what we are about to store must decode back to what we were given.
        // This is what removes the need for an exhaustive option schema per constraint class.
        try {
            $decoded = ExtraPropertyConstraintMapper::fromNames($encoded);
        } catch (UnknownExtraPropertyConstraintException|InvalidExtraPropertyConstraintException $exception) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraints cannot be persisted: the encoded form is not readable back (%s).',
                $exception->getMessage()
            ), 0, $exception);
        }

        if (!$this->isIdentical($constraints, $decoded)) {
            throw new InvalidExtraPropertyConstraintException(
                'Constraints cannot be persisted without loss: decoding the encoded form does not yield the original constraints. '
                . 'This usually means an option the extra property constraint format does not carry.'
            );
        }

        return $encoded;
    }

    /**
     * Asserts that constraints can be persisted, without keeping the result.
     *
     * This is a genuine dry-run of {@see self::encode()} rather than a parallel set of checks: any
     * divergence between the two would let a definition pass the registry guard and then fail while
     * saving — after the DDL has already created its storage column.
     *
     * @param list<Constraint>|null $constraints
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    public function assertEncodable(?array $constraints): void
    {
        $this->encode($constraints);
    }

    /**
     * Decodes a stored value, refusing the whole set as soon as anything is wrong.
     *
     * Used wherever a human is waiting for an answer — the back-office form, and any write path
     * re-reading what it is about to store.
     *
     * @return list<Constraint>|null
     *
     * @throws UnknownExtraPropertyConstraintException
     * @throws InvalidExtraPropertyConstraintException
     */
    public function decode(mixed $raw): ?array
    {
        if (is_array($raw)) {
            return [] !== $raw ? array_values($raw) : null;
        }
        if (!is_string($raw)) {
            return null;
        }

        $this->assertWithinLengthBound($raw);

        return ExtraPropertyConstraintMapper::fromNames($raw);
    }

    /**
     * Decodes a stored value, tolerating a corrupt root constraint.
     *
     * Each top-level constraint is decoded on its own: an invalid one is dropped and reported in the
     * result, while its valid siblings stay active. A composite is always dropped as a whole — a
     * partially decoded composite would silently weaken the validation it describes.
     */
    public function decodeTolerant(mixed $raw): DecodedConstraints
    {
        if (is_array($raw)) {
            return new DecodedConstraints(array_values($raw));
        }
        if (!is_string($raw) || '' === trim($raw)) {
            return new DecodedConstraints();
        }

        try {
            $this->assertWithinLengthBound($raw);
            $tokens = ExtraPropertyConstraintMapper::tokenize($raw);
        } catch (UnknownExtraPropertyConstraintException|InvalidExtraPropertyConstraintException $exception) {
            return new DecodedConstraints([], [['index' => null, 'reason' => $exception->getMessage()]]);
        }

        $constraints = [];
        $rejections = [];
        foreach ($tokens as $index => [$token, $line]) {
            try {
                $parsed = ExtraPropertyConstraintMapper::fromNames($token);
            } catch (UnknownExtraPropertyConstraintException|InvalidExtraPropertyConstraintException $exception) {
                // fromNames() numbers lines within the single token it was handed, which is always
                // line 1 here; the meaningful number is the one from the full definition.
                $reason = preg_replace('/^Line \d+: /', '', $exception->getMessage()) ?? $exception->getMessage();
                $rejections[] = ['index' => $index, 'reason' => sprintf('line %d: %s', $line, $reason)];

                continue;
            }
            foreach ($parsed ?? [] as $constraint) {
                $constraints[] = $constraint;
            }
        }

        return new DecodedConstraints($constraints, $rejections);
    }

    /**
     * The single length bound both decoding paths apply, so neither can drift from the other.
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private function assertWithinLengthBound(string $raw): void
    {
        if (strlen($raw) > ExtraPropertyConstraintMapper::MAX_RAW_LENGTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint definition exceeds the maximum length of %d characters.',
                ExtraPropertyConstraintMapper::MAX_RAW_LENGTH
            ));
        }
    }

    /**
     * Validates the object graph handed over by module code, before anything is rendered.
     *
     * The grammar already refuses an unknown class or an unrepresentable value while rendering, but
     * two families would otherwise slip through silently: options excluded from rendering by design
     * (`groups`, `payload`), and executable options that render and re-parse perfectly.
     *
     * @param list<Constraint> $constraints
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private function assertConstraintList(array $constraints): void
    {
        if (!array_is_list($constraints)) {
            throw new InvalidExtraPropertyConstraintException('Extra property constraints must be provided as a list.');
        }

        foreach ($constraints as $index => $constraint) {
            if (!$constraint instanceof Constraint) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Extra property constraint at index %d must extend %s, got %s.',
                    $index,
                    Constraint::class,
                    get_debug_type($constraint)
                ));
            }

            $this->assertConstraintEncodable($constraint, 0, sprintf('constraints[%d]', $index));
        }
    }

    /**
     * @throws InvalidExtraPropertyConstraintException
     */
    private function assertConstraintEncodable(Constraint $constraint, int $depth, string $path): void
    {
        if ($depth > ExtraPropertyConstraintMapper::MAX_NESTING_DEPTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Constraint nesting exceeds the maximum depth of %d at %s.',
                ExtraPropertyConstraintMapper::MAX_NESTING_DEPTH,
                $path
            ));
        }

        foreach ((new ReflectionObject($constraint))->getProperties() as $property) {
            if ($property->isStatic() || !$property->isInitialized($constraint)) {
                continue;
            }

            $name = $property->getName();
            $value = $property->getValue($constraint);

            if (null !== $value && ExtraPropertyConstraintMapper::isForbiddenOption($name)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" is not supported for extra property constraints at %s: it may execute a callable or traverse the validated object. Normalize the value in module code before validation.',
                    $name,
                    $path
                ));
            }

            // Excluded from the rendered form by design, so they must be refused rather than lost.
            if ('groups' === $name && ['Default'] !== $value) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Validation groups are not supported for extra property constraints at %s; only the default group applies.',
                    $path
                ));
            }
            if ('payload' === $name && null !== $value) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'The "payload" option is not supported for extra property constraints at %s.',
                    $path
                ));
            }

            $this->assertValueEncodable($value, $depth, $path . '.' . $name);
        }
    }

    /**
     * @throws InvalidExtraPropertyConstraintException
     */
    private function assertValueEncodable(mixed $value, int $depth, string $path): void
    {
        if (null === $value || is_scalar($value)) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $this->assertValueEncodable($item, $depth + 1, sprintf('%s[%s]', $path, (string) $key));
            }

            return;
        }

        if ($value instanceof Constraint) {
            $this->assertConstraintEncodable($value, $depth + 1, $path);

            return;
        }

        throw new InvalidExtraPropertyConstraintException(sprintf(
            'Value of type %s at %s cannot be represented in an extra property constraint. Only scalars, lists of scalars and nested constraints are supported.',
            get_debug_type($value),
            $path
        ));
    }

    /**
     * Strict structural comparison of two constraint graphs.
     *
     * PHP's `==` is deliberately not used: it treats 5 and '5', or ['1'] and [1], as equal, whereas
     * the DSL distinguishes them on purpose. A type drift would therefore pass unnoticed through a
     * loose comparison, which is exactly what the fidelity contract exists to catch.
     *
     * @param list<Constraint> $expected
     * @param list<Constraint>|null $actual
     */
    private function isIdentical(array $expected, ?array $actual): bool
    {
        return null !== $actual && $this->normalize($expected) === $this->normalize($actual);
    }

    /**
     * Reduces a graph to nested arrays of scalars, keeping class names, keys, order and scalar types,
     * so a plain `===` becomes a strict structural comparison.
     */
    private function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalize($item);
            }

            return $normalized;
        }

        if (!is_object($value)) {
            // Keeps 5 and '5' apart, which a loose comparison would not.
            return [get_debug_type($value), $value];
        }

        $normalized = ['#class' => $value::class];
        $reflection = new ReflectionObject($value);
        $properties = $reflection->getProperties();
        usort($properties, static fn ($a, $b): int => strcmp($a->getName(), $b->getName()));
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $normalized[$property->getName()] = $property->isInitialized($value)
                ? $this->normalize($property->getValue($value))
                : '#uninitialized';
        }

        return $normalized;
    }
}
