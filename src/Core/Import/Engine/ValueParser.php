<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;

/**
 * Pure parsing helpers for raw CSV cell values.
 *
 * parseBoolean() deliberately replaces the legacy bare (bool) cast, under
 * which "false" and "no" were truthy — a reviewed behavior change of the
 * import refactoring (see the behavior inventory in the Import plan).
 */
final class ValueParser
{
    private const TRUE_VALUES = ['1', 'true', 'yes'];
    private const FALSE_VALUES = ['0', 'false', 'no', ''];

    /**
     * Tolerant boolean parsing: 0/1/true/false/yes/no (case-insensitive).
     * Returns null when the value is not recognized — callers emit a warning
     * and treat the value as false.
     */
    public function parseBoolean(string $value): ?bool
    {
        $normalized = strtolower(trim($value));

        if (in_array($normalized, self::TRUE_VALUES, true)) {
            return true;
        }
        if (in_array($normalized, self::FALSE_VALUES, true)) {
            return false;
        }

        return null;
    }

    /**
     * Decimal parsing tolerant to the legacy price format (',' as decimal
     * separator, stray '%' sign). Returns null when not numeric.
     */
    public function parseDecimal(string $value): ?DecimalNumber
    {
        $normalized = str_replace([',', '%', ' '], ['.', '', ''], trim($value));

        if ('' === $normalized || !is_numeric($normalized)) {
            return null;
        }

        return new DecimalNumber($normalized);
    }

    /**
     * Splits a multi-value cell on the configured separator, honoring
     * enclosures (legacy ImportDataFormatter::split parity, without the
     * temporary stream). Empty entries are dropped, values are trimmed.
     *
     * @return list<string>
     */
    public function split(string $value, string $separator): array
    {
        if ('' === trim($value)) {
            return [];
        }

        $parts = str_getcsv($value, $separator, '"', '');

        $values = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ('' !== $part) {
                $values[] = $part;
            }
        }

        return $values;
    }

    /**
     * Parses 'Y-m-d' or 'Y-m-d H:i:s' dates. Returns null when unparseable.
     */
    public function parseDate(string $value): ?DateTimeImmutable
    {
        $value = trim($value);

        foreach (['Y-m-d H:i:s', 'Y-m-d'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
            if (false !== $date && $date->format($format) === $value) {
                return $date;
            }
        }

        return null;
    }
}
