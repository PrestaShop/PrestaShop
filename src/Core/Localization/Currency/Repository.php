<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataSourceInterface as CurrencyDataSourceInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\RepositoryInterface as CurrencyRepositoryInterface;

/**
 * Currency repository class.
 *
 * Used to get Localization/Currency instances (by currency code for example)
 */
class Repository implements CurrencyRepositoryInterface
{
    /**
     * Available currencies, indexed by ISO code.
     * Lazy loaded.
     *
     * @var Currency[]
     */
    protected $currencies;

    /**
     * @var CurrencyDataSourceInterface
     */
    protected $dataSource;

    public function __construct(CurrencyDataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency($currencyCode, $localeCode)
    {
        if (!isset($this->currencies[$currencyCode])) {
            $data = $this->dataSource->getLocalizedCurrencyData(
                new LocalizedCurrencyId($currencyCode, $localeCode)
            );

            $this->currencies[$currencyCode] = $this->createCurrencyFromData($data);
        }

        return $this->currencies[$currencyCode];
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrencies($localeCode)
    {
        return $this->createCurrenciesFromData($this->dataSource->getAvailableCurrenciesData($localeCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getAllInstalledCurrencies($localeCode)
    {
        return $this->createCurrenciesFromData($this->dataSource->getAllInstalledCurrenciesData($localeCode));
    }

    /**
     * @param array $currenciesData
     *
     * @return CurrencyCollection
     */
    private function createCurrenciesFromData(array $currenciesData)
    {
        $currencies = new CurrencyCollection();
        /** @var CurrencyData $currencyDatum */
        foreach ($currenciesData as $currencyDatum) {
            $currencies->add($this->createCurrencyFromData($currencyDatum));
        }

        return $currencies;
    }

    /**
     * @param CurrencyData $currencyData
     *
     * @return Currency
     */
    private function createCurrencyFromData(CurrencyData $currencyData)
    {
        $numericIsoCode = $currencyData->getNumericIsoCode() ? (int) $currencyData->getNumericIsoCode() : null;

        return new Currency(
            $currencyData->isActive(),
            $currencyData->getConversionRate(),
            $currencyData->getIsoCode(),
            $numericIsoCode,
            $currencyData->getSymbols(),
            $currencyData->getPrecision(),
            $currencyData->getNames(),
            $currencyData->getPatterns()
        );
    }
}
