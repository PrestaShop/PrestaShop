<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Currency repository interface.
 *
 * Describes the behavior of Currency Repository classes
 */
interface RepositoryInterface
{
    /**
     * Get a Currency instance by ISO code.
     *
     * @param string $currencyCode
     *                             Wanted currency's ISO code
     *                             Must be an alphabetic ISO 4217 currency code
     * @param string $localeCode
     *                           Currency data will be translated in this language
     *
     * @return Currency
     *                  The wanted Currency instance
     */
    public function getCurrency($currencyCode, $localeCode);

    /**
     * Get all the available currencies (installed + active).
     *
     * @param string $localeCode
     *                           IETF tag. Data will be translated in this language
     *
     * @return CurrencyCollection
     *                            The available currencies
     */
    public function getAvailableCurrencies($localeCode);

    /**
     * Get all the installed currencies in database (regardless of their active or soft deleted status).
     *
     * @param string $localeCode
     *                           IETF tag. Data will be translated in this language
     *
     * @return CurrencyCollection
     *                            The installed currencies in database
     */
    public function getAllInstalledCurrencies($localeCode);
}
