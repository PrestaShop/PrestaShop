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
