<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

use DateTime as NativeDateTime;
use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

/**
 * Defines reusable values for DateTime
 */
final class DateTime
{
    /**
     * Default format for date string
     */
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * Default format for date time string
     */
    public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * ISO Date time format string
     */
    public const ISO_DATETIME_FORMAT = 'c';

    /**
     * DateTime value which should be considered same as null
     */
    public const NULL_DATETIME = '0000-00-00 00:00:00';

    /**
     * @deprecated use NULL_DATETIME or NULL_DATE depending on usecase
     */
    public const NULL_VALUE = '0000-00-00 00:00:00';

    /**
     * Date value which should be considered same as null
     */
    public const NULL_DATE = '0000-00-00';

    /**
     * This class only defines constants and has no reason to be initialized
     */
    public function __construct()
    {
        throw new RuntimeException(sprintf('This class purpose is to define constants only. You might have mistaken it with "%s"', NativeDateTime::class));
    }

    /**
     * @param DateTimeInterface|string|null $value
     *
     * @return bool
     */
    public static function isNull($value): bool
    {
        return $value instanceof NullDateTime || empty($value) || $value === self::NULL_DATE || $value === self::NULL_DATETIME;
    }

    /**
     * Returns NullDateTime if input value is nullable (including 0000-00-00 value), return a DateTimeImmutable object otherwise.
     *
     * @param string|null $value
     *
     * @return DateTimeImmutable|NullDateTime
     */
    public static function buildNullableDateTime(?string $value): DateTimeImmutable
    {
        return empty($value) || $value === self::NULL_DATETIME || $value === self::NULL_DATE ? new NullDateTime() : new DateTimeImmutable($value);
    }

    /**
     * Returns null if input value is nullable (including 0000-00-00 value), return a DateTimeImmutable object otherwise.
     *
     * @param string|null $value
     *
     * @return DateTimeImmutable|NullDateTime|null
     */
    public static function buildDateTimeOrNull(?string $value): ?DateTimeImmutable
    {
        return empty($value) || $value === self::NULL_DATETIME || $value === self::NULL_DATE ? null : new DateTimeImmutable($value);
    }
}
