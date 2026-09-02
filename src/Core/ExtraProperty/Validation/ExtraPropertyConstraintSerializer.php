<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use __PHP_Incomplete_Class;
use DateTime;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use SplObjectStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Throwable;

/**
 * Safely serializes and unserializes extra property constraints.
 *
 * The stored format intentionally remains PHP serialization for backward compatibility. Security
 * relies on a core-owned class allowlist, followed by validation of the complete decoded graph.
 * This second step is required because allowed_classes alone neither detects incomplete nested
 * objects nor prevents executable or object-traversal options from being injected into an
 * allowed class.
 */
final class ExtraPropertyConstraintSerializer
{
    private const MAX_DEPTH = 64;

    private const CALLABLE_OPTIONS = [
        'callback',
        'normalizer',
    ];

    /**
     * @var array<class-string, true>|null
     */
    private static ?array $allowedClassMap = null;

    /**
     * @return list<class-string>
     */
    public static function getAllowedClasses(): array
    {
        return array_keys(self::getAllowedClassMap());
    }

    /**
     * @return array<class-string, true>
     */
    private static function getAllowedClassMap(): array
    {
        return self::$allowedClassMap ??= array_fill_keys(array_values(array_unique([
            ...array_values(ExtraPropertyConstraintMapper::getAllowedConstraints()),
            Assert\Required::class,
            Assert\Optional::class,
            DateTime::class,
            DateTimeImmutable::class,
        ])), true);
    }

    /**
     * @param list<Constraint>|null $constraints
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    public static function assertSupported(?array $constraints): void
    {
        if (null === $constraints || [] === $constraints) {
            return;
        }
        if (!array_is_list($constraints)) {
            throw new InvalidExtraPropertyConstraintException(
                'Extra property constraints must be provided as a list.'
            );
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

            self::assertSupportedNode(
                $constraint,
                new SplObjectStorage(),
                sprintf('constraints[%d]', $index),
                0
            );
        }
    }

    /**
     * @param list<Constraint>|null $constraints
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    public static function serialize(?array $constraints): ?string
    {
        if (null === $constraints || [] === $constraints) {
            return null;
        }

        self::assertSupported($constraints);

        try {
            return serialize($constraints);
        } catch (Throwable $exception) {
            throw new InvalidExtraPropertyConstraintException(
                sprintf('Extra property constraints could not be serialized: %s', $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    /**
     * @param mixed $raw Serialized constraints or an already-decoded in-memory list
     * @param callable(int|string|null, string): void|null $onRejected
     *
     * @return list<Constraint>|null
     */
    public static function unserialize(mixed $raw, ?callable $onRejected = null): ?array
    {
        if (is_array($raw)) {
            $decoded = $raw;
        } elseif (is_string($raw) && '' !== $raw) {
            $decoded = self::decodeString($raw, $onRejected);
            if (null === $decoded) {
                return null;
            }
        } else {
            return null;
        }

        if (!array_is_list($decoded)) {
            self::reject($onRejected, null, 'Decoded extra property constraints must be a list.');

            return null;
        }

        $constraints = [];
        foreach ($decoded as $index => $constraint) {
            if (!$constraint instanceof Constraint) {
                self::reject(
                    $onRejected,
                    $index,
                    sprintf('Decoded value is not a Symfony constraint, got %s.', get_debug_type($constraint))
                );

                continue;
            }

            try {
                self::assertSupportedNode(
                    $constraint,
                    new SplObjectStorage(),
                    sprintf('constraints[%d]', $index),
                    0
                );
                $constraints[] = $constraint;
            } catch (InvalidExtraPropertyConstraintException $exception) {
                // Reject the complete root constraint, never a nested fragment of a composite.
                self::reject($onRejected, $index, $exception->getMessage());
            }
        }

        return [] !== $constraints ? $constraints : null;
    }

    /**
     * @param callable(int|string|null, string): void|null $onRejected
     *
     * @return array<mixed>|null
     */
    private static function decodeString(string $raw, ?callable $onRejected): ?array
    {
        set_error_handler(
            static function (int $severity, string $message): never {
                throw new InvalidExtraPropertyConstraintException(
                    sprintf('Invalid serialized extra property constraints: %s', $message)
                );
            }
        );

        try {
            $decoded = unserialize($raw, [
                'allowed_classes' => self::getAllowedClasses(),
                'max_depth' => self::MAX_DEPTH,
            ]);
        } catch (Throwable $exception) {
            self::reject($onRejected, null, $exception->getMessage());

            return null;
        } finally {
            restore_error_handler();
        }

        if (!is_array($decoded)) {
            self::reject(
                $onRejected,
                null,
                sprintf('Serialized extra property constraints must decode to an array, got %s.', get_debug_type($decoded))
            );

            return null;
        }

        return $decoded;
    }

    /**
     * @param SplObjectStorage<object, null> $visited
     *
     * @throws InvalidExtraPropertyConstraintException
     */
    private static function assertSupportedNode(
        mixed $value,
        SplObjectStorage $visited,
        string $path,
        int $depth
    ): void {
        if ($depth > self::MAX_DEPTH) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Extra property constraint graph exceeds the maximum depth of %d at %s.',
                self::MAX_DEPTH,
                $path
            ));
        }

        if (is_resource($value)) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Resources are not supported in extra property constraints at %s.',
                $path
            ));
        }

        if (is_array($value)) {
            foreach ($value as $key => $nestedValue) {
                self::assertSupportedNode(
                    $nestedValue,
                    $visited,
                    sprintf('%s[%s]', $path, (string) $key),
                    $depth + 1
                );
            }

            return;
        }

        if (!is_object($value)) {
            return;
        }

        if ($value instanceof __PHP_Incomplete_Class) {
            $incompleteData = (array) $value;
            $className = $incompleteData['__PHP_Incomplete_Class_Name'] ?? 'unknown';

            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Class "%s" is not allowed in extra property constraints at %s.',
                $className,
                $path
            ));
        }

        if (!isset(self::getAllowedClassMap()[$value::class])) {
            throw new InvalidExtraPropertyConstraintException(sprintf(
                'Class "%s" is not allowed in extra property constraints at %s.',
                $value::class,
                $path
            ));
        }

        if ($visited->contains($value)) {
            return;
        }
        $visited->attach($value);

        foreach ((array) $value as $rawPropertyName => $propertyValue) {
            $propertyName = self::normalizePropertyName((string) $rawPropertyName);
            if (null !== $propertyValue && in_array($propertyName, self::CALLABLE_OPTIONS, true)) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" is not supported for extra property constraints because it may execute a callable. ' .
                    'Normalize the value in module code before validation.',
                    $propertyName
                ));
            }
            if (null !== $propertyValue && str_ends_with(strtolower($propertyName), 'propertypath')) {
                throw new InvalidExtraPropertyConstraintException(sprintf(
                    'Option "%s" is not supported for extra property constraints because it may traverse the validated object. ' .
                    'Extra property constraints validate an individual value.',
                    $propertyName
                ));
            }

            self::assertSupportedNode(
                $propertyValue,
                $visited,
                $path . '.' . $propertyName,
                $depth + 1
            );
        }
    }

    private static function normalizePropertyName(string $propertyName): string
    {
        $separatorPosition = strrpos($propertyName, "\0");

        return false === $separatorPosition ? $propertyName : substr($propertyName, $separatorPosition + 1);
    }

    /**
     * @param callable(int|string|null, string): void|null $onRejected
     */
    private static function reject(?callable $onRejected, int|string|null $index, string $reason): void
    {
        if (null !== $onRejected) {
            $onRejected($index, $reason);
        }
    }
}
