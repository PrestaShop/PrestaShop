<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use Currency;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;

/**
 * Installed Currencies data layer.
 *
 * Provides currencies' installation info
 */
class CurrencyInstalled
{
    /**
     * This adapter will provide data from DB / ORM about Currency (via legacy entity).
     *
     * @var CurrencyDataProviderInterface
     */
    protected $dataProvider;

    public function __construct(CurrencyDataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Check if a currency is currently available (not deleted + active).
     *
     * @param string $currencyCode The said currency ISO code
     *
     * @return bool True if this currency is available
     */
    public function isAvailable($currencyCode)
    {
        $currency = $this->dataProvider->getCurrencyByIsoCode($currencyCode);

        if ($currency instanceof Currency) {
            return (bool) $currency->active;
        }

        return false;
    }

    /**
     * Get all available (not deleted + active) currencies' ISO codes.
     *
     * @return string[]
     */
    public function getAvailableCurrencyCodes()
    {
        $currencies = $this->dataProvider->findAll();
        $currencyIsoCodes = array_column($currencies, 'iso_code');

        return $currencyIsoCodes;
    }

    /**
     * Get all the available currencies' ISO codes (present in database no matter if it's deleted or active).
     *
     * @return string[]
     */
    public function getAllInstalledCurrencyIsoCodes()
    {
        $currencies = $this->dataProvider->findAllInstalled();
        $currencyIsoCodes = array_column($currencies, 'iso_code');

        return $currencyIsoCodes;
    }
}
