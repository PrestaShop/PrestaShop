<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Currency data repository interface.
 *
 * Describes the behavior of currency DataRepository classes
 */
interface DataSourceInterface
{
    /**
     * Get complete currency data by currency code, in a given language.
     *
     * @param LocalizedCurrencyId $localizedCurrencyId The currency data identifier (currency code + locale code)
     *
     * @return CurrencyData The currency data
     */
    public function getLocalizedCurrencyData(LocalizedCurrencyId $localizedCurrencyId);

    /**
     * Is this currency available ?
     * (an available currency is not deleted AND is active).
     *
     * @param string $currencyCode
     *
     * @return bool True if currency is available
     */
    public function isCurrencyAvailable($currencyCode);

    /**
     * Get all the available (installed + active) currencies' data.
     *
     * @param string $localeCode Data will be translated in this language
     *
     * @return CurrencyData[] The available currencies' data
     */
    public function getAvailableCurrenciesData($localeCode);

    /**
     * Get all installed currencies' data in database (regardless of their active or soft deleted status).
     *
     * @param string $localeCode Data will be translated in this language
     *
     * @return CurrencyData[] The installed currencies' database data
     */
    public function getAllInstalledCurrenciesData($localeCode);
}
