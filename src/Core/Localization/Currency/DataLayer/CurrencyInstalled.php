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
     * @param $currencyCode
     *  The said currency ISO code
     *
     * @return bool
     *              True if this currency is available
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
