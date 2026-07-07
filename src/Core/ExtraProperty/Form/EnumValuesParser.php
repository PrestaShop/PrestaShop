<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

/**
 * Parses the "one value per line" enum_values textarea of the definition form into the list the
 * CQRS commands expect. Shared by the form-level validation (ExtraPropertyDefinitionType) and
 * the data handler so both read the SAME values from the same raw string.
 */
class EnumValuesParser
{
    /**
     * @param mixed $rawValue the raw textarea value (anything but a non-blank string reads as "no values")
     *
     * @return list<string>|null the trimmed non-empty lines, or null when there are none
     */
    public static function parse(mixed $rawValue): ?array
    {
        if (!is_string($rawValue) || '' === trim($rawValue)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode("\n", $rawValue)), static fn (string $v): bool => '' !== $v)) ?: null;
    }
}
