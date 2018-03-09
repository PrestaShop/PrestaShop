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

namespace PrestaShop\PrestaShop\Adapter\Currency;

use Currency;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;

/**
 * This class will provide data from DB / ORM about Currency
 */
class CurrencyDataProvider
{
    /**
     * Return available currencies
     *
     * @return array Currencies
     */
    public function getCurrencies($object = false, $active = true, $group_by = false)
    {
        return Currency::getCurrencies($object = false, $active = true, $group_by = false);
    }

    /**
     * Get a Currency entity instance by ISO code
     *
     * @param string $isoCode
     *  An ISO 4217 currency code
     *
     * @param int|null $idLang
     *  Set this parameter if you want the currency in a specific language.
     *  If null, default language will be used.
     *
     * @return Currency|null
     *  The asked Currency object, or null if not found.
     */
    public function getCurrencyByIsoCode($isoCode, $idLang = null)
    {
        $currencyId = Currency::getIdByIsoCode($isoCode);
        if (!$currencyId) {
            return null;
        }

        if (null === $idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        return new Currency($currencyId, $idLang);
    }
}
