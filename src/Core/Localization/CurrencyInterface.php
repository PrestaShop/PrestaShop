<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization;

/**
 * Currency entities interface.
 *
 * Describes the behavior of currency classes
 */
interface CurrencyInterface
{
    /**
     * Check if this currency is active.
     *
     * @return bool
     *              true if currency is active
     */
    public function isActive();

    /**
     * Get the conversion rate (exchange rate) of this currency against the shop's default currency.
     *
     * Price in currency A * currency A's conversion rate = price in default currency
     *
     * Example:
     * Given the Euro as default shop's currency,
     * If 1 dollar = 1.31 euros,
     * Then conversion rate for Dollar will be 1.31
     *
     * @return float
     *               The conversion rate of this currency
     */
    public function getConversionRate();

    /**
     * Get the alphabetic ISO code of this currency.
     *
     * @see https://www.iso.org/iso-4217-currency-codes.html
     *
     * @return string
     */
    public function getIsoCode();

    /**
     * Get the numeric ISO code of this currency.
     *
     * @see https://www.iso.org/iso-4217-currency-codes.html
     *
     * @return int|null
     */
    public function getNumericIsoCode();

    /**
     * Get the currency symbol for a given locale code.
     *
     * @param string $localeCode
     *                           The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     *
     * @see https://en.wikipedia.org/wiki/IETF_language_tag
     * @see https://www.w3.org/International/articles/language-tags
     *
     * @return string
     *                The currency symbol for this locale
     */
    public function getSymbol($localeCode);

    /**
     * Get the number of decimal digits to use with this currency.
     *
     * Example: Euro's decimal precision is 2 (1 234,56 EUR)
     * Example: Colombian peso's decimal precision is 0 (1 235 COP)
     *
     * @return int
     */
    public function getDecimalPrecision();

    /**
     * Get the currency's name for a given locale code.
     *
     * @param string $localeCode
     *                           The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     *
     * @see https://en.wikipedia.org/wiki/IETF_language_tag
     * @see https://www.w3.org/International/articles/language-tags
     *
     * @return string
     *                The currency's name for this locale
     */
    public function getName($localeCode);
}
