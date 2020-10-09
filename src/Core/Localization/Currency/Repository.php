<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        return new Currency(
            $currencyData->isActive(),
            $currencyData->getConversionRate(),
            $currencyData->getIsoCode(),
            $currencyData->getNumericIsoCode(),
            $currencyData->getSymbols(),
            $currencyData->getPrecision(),
            $currencyData->getNames(),
            $currencyData->getPatterns()
        );
    }
}
