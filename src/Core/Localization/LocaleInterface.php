<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

/**
 * Locale entity interface.
 *
 * Describes the behavior of locale classes
 */
interface LocaleInterface
{
    /**
     * Latin numbering system is the "occidental" numbering system. Number digits are 0123456789.
     * This is the default numbering system in PrestaShop, even for arabian or asian languages, until we
     * provide a way to configure this in admin.
     */
    public const NUMBERING_SYSTEM_LATIN = 'latn';

    /**
     * Get this locale's code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Format a number according to locale rules.
     *
     * @param int|float|string $number The number to be formatted
     *
     * @return string The formatted number
     */
    public function formatNumber(int|float|string $number): string;

    /**
     * Format a number as a price.
     *
     * @param int|float|string $number Number to be formatted as a price
     * @param string $currencyCode Currency of the price
     *
     * @return string The formatted price
     */
    public function formatPrice(int|float|string $number, string $currencyCode): string;

    /**
     * Get price specification by currency code.
     *
     * @param string $currencyCode Currency of the price
     *
     * @return NumberInterface
     */
    public function getPriceSpecification(string $currencyCode): NumberInterface;

    /**
     * Get number specification
     *
     * @return NumberInterface
     */
    public function getNumberSpecification(): NumberInterface;
}
