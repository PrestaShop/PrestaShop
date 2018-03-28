<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use Currency;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;

/**
 * Installed Currencies data layer
 *
 * Provides currencies' installation info
 */
class CurrencyInstalled
{
    protected $dataProvider;

    public function __construct(CurrencyDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Check if a currency is currently available (not deleted + active)
     *
     * @param $currencyCode
     *  The said currency ISO code
     *
     * @return bool
     *  True if this currency is available
     */
    public function isAvailable($currencyCode)
    {
        $currency = $this->dataProvider->getCurrencyByIsoCode($currencyCode);

        if ($currency instanceof Currency) {
            return (bool)$currency->active;
        }

        return false;
    }

    /**
     * Get all available (not deleted + active) currencies' ISO codes
     *
     * @return string[]
     */
    public function getAvailableCurrencyCodes()
    {
        $currencies  = $this->dataProvider->getCurrencies();
        $currencyIds = array_column($currencies, 'iso_code');

        return $currencyIds;
    }
}
