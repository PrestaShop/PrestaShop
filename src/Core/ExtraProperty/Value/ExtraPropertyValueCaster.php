<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Converts extra property values between DB storage format (raw scalars/strings from PDO)
 * and their declared PHP types (bool, int, float, formatted date strings).
 *
 * Two directions:
 *   - castFromDb: DB → PHP. The canonical cast point is ExtraPropertyReader (every value it
 *     returns is typed, lang-scoped values are cast entry by entry); it also serves consumers
 *     holding raw DB rows outside the reader (e.g. grid records in
 *     ExtraPropertiesGridQueryBuilderModifier).
 *   - castForDb:  PHP → DB, for persisting values submitted by form widgets.
 *
 * Casting is based on ExtraPropertyType only (not on the registered form field type), so the
 * behavior is consistent regardless of which Symfony form widget is used.
 *
 * NULL handling is nullable-aware: a NULL read from a nullable column stays NULL for every
 * type; on NOT NULL columns (value can only be NULL when the row is missing) BOOL coerces
 * to false and other types stay NULL.
 */
class ExtraPropertyValueCaster
{
    /**
     * Converts a PHP value (from a Symfony form widget) to a DB-compatible scalar.
     *
     * For lang-scoped fields, $value should be an array [id_lang => mixed];
     * each entry is cast individually.
     *
     * @param ExtraPropertyDefinition $definition
     * @param mixed $value PHP value as submitted by a form widget
     *
     * @return mixed DB-compatible scalar or array
     */
    public static function castForDb(ExtraPropertyDefinition $definition, mixed $value): mixed
    {
        if (ExtraPropertyScope::LANG === $definition->getScope()) {
            if (!is_array($value)) {
                return [];
            }

            $cast = [];
            foreach ($value as $idLang => $langVal) {
                $cast[$idLang] = static::castScalarForDb($definition->getType(), $langVal);
            }

            return $cast;
        }

        return static::castScalarForDb($definition->getType(), $value);
    }

    /**
     * Casts a single scalar value from DB format to PHP type.
     *
     * DATE fields are returned as formatted strings ('Y-m-d H:i:s') rather than DateTimeImmutable
     * objects because BO form widgets default to TextType which expects a string. Modules using
     * a DateTimeType widget should configure it with input: 'string'.
     *
     * @param mixed $rawValue
     * @param bool $nullable When true (nullable storage column), a NULL value is preserved as-is;
     *                       when false, BOOL coerces NULL to false (missing row semantics)
     *
     * @return mixed
     */
    public static function castFromDb(ExtraPropertyType $type, mixed $rawValue, bool $nullable = false): mixed
    {
        if (null === $rawValue) {
            return $nullable ? null : match ($type) {
                ExtraPropertyType::BOOL => false,
                default => null,
            };
        }

        return match ($type) {
            ExtraPropertyType::BOOL => (bool) (int) $rawValue,
            ExtraPropertyType::INT => (int) $rawValue,
            ExtraPropertyType::FLOAT => (float) $rawValue,
            ExtraPropertyType::DATE => static::toFormattedDateOrNull($rawValue),
            default => $rawValue,
        };
    }

    /**
     * Parses a raw date value and returns it as a formatted string, or null when empty/invalid.
     *
     * @param mixed $value
     */
    protected static function toFormattedDateOrNull(mixed $value): ?string
    {
        $dt = static::toDateTimeOrNull($value);

        return null !== $dt ? $dt->format('Y-m-d H:i:s') : null;
    }

    /**
     * Casts a single PHP value to a DB-compatible scalar.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function castScalarForDb(ExtraPropertyType $type, mixed $value): mixed
    {
        return match ($type) {
            ExtraPropertyType::BOOL => (int) (bool) $value,
            ExtraPropertyType::DATE => $value instanceof DateTimeInterface
                ? $value->format('Y-m-d H:i:s')
                : $value,
            default => $value,
        };
    }

    /**
     * Converts a raw date string to a DateTimeImmutable, or null when the value is empty/null.
     *
     * @param mixed $value
     */
    protected static function toDateTimeOrNull(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }
        if (DateTime::isNull($value)) {
            return null;
        }

        try {
            return new DateTimeImmutable((string) $value);
        } catch (Exception) {
            return null;
        }
    }
}
