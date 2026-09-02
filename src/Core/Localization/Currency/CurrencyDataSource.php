<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyInstalled as CurrencyInstalledDataLayer;

/**
 * Localization CurrencyData source
 * Uses a stack of middleware data layers to read / write CurrencyData objects.
 */
class CurrencyDataSource implements DataSourceInterface
{
    /**
     * The top layer of the middleware stack.
     *
     * @var CurrencyDataLayerInterface
     */
    protected $topLayer;

    /**
     * @var CurrencyInstalledDataLayer
     */
    protected $installedDataLayer;

    /**
     * CurrencyDataSource constructor needs CurrencyDataLayer objects.
     * This top layer might be chained with lower layers and will be the entry point of this middleware stack.
     *
     * @param CurrencyDataLayerInterface $topLayer
     * @param CurrencyInstalledDataLayer $installedDataLayer
     */
    public function __construct(CurrencyDataLayerInterface $topLayer, CurrencyInstalledDataLayer $installedDataLayer)
    {
        $this->topLayer = $topLayer;
        $this->installedDataLayer = $installedDataLayer;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedCurrencyData(LocalizedCurrencyId $localizedCurrencyId)
    {
        return $this->topLayer->read($localizedCurrencyId);
    }

    /**
     * Is this currency available ?
     * (an available currency is not deleted AND is active).
     *
     * @param string $currencyCode
     *
     * @return bool True if currency is available
     */
    public function isCurrencyAvailable($currencyCode)
    {
        return $this->installedDataLayer->isAvailable($currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrenciesData($localeCode)
    {
        return $this->formatCurrenciesData($this->installedDataLayer->getAvailableCurrencyCodes(), $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllInstalledCurrenciesData($localeCode)
    {
        return $this->formatCurrenciesData($this->installedDataLayer->getAllInstalledCurrencyIsoCodes(), $localeCode);
    }

    /**
     * @param array $currencyCodes
     * @param string $localeCode
     *
     * @return array
     */
    private function formatCurrenciesData(array $currencyCodes, $localeCode)
    {
        $currenciesData = [];
        foreach ($currencyCodes as $currencyCode) {
            $currenciesData[] = $this->getLocalizedCurrencyData(new LocalizedCurrencyId($currencyCode, $localeCode));
        }

        return $currenciesData;
    }
}
