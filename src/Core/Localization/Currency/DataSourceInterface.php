<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @param LocalizedCurrencyId $localizedCurrencyId
     *                                                 The currency data identifier (currency code + locale code)
     *
     * @return CurrencyData
     *                      The currency data
     */
    public function getLocalizedCurrencyData(LocalizedCurrencyId $localizedCurrencyId);

    /**
     * Is this currency available ?
     * (an available currency is not deleted AND is active).
     *
     * @param $currencyCode
     *
     * @return bool
     *              True if currency is available
     */
    public function isCurrencyAvailable($currencyCode);

    /**
     * Get all the available (installed + active) currencies' data.
     *
     * @param string $localeCode
     *                           Data will be translated in this language
     *
     * @return CurrencyData[]
     *                        The available currencies' data
     */
    public function getAvailableCurrenciesData($localeCode);

    /**
     * Get all installed currencies' data in database (regardless of their active or soft deleted status).
     *
     * @param string $localeCode
     *                           Data will be translated in this language
     *
     * @return CurrencyData[]
     *                        The installed currencies' database data
     */
    public function getAllInstalledCurrenciesData($localeCode);
}
