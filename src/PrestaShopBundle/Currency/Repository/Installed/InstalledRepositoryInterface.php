<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency\Repository\Installed;

use PrestaShopBundle\Currency\Currency;

/**
 * Interface InstalledRepositoryInterface
 *
 * Contract to specify how an "installed" currency repository should behave.
 * A currency repository is used as a Currency CRUD
 * An installed currency repository deals with installed currencies only
 *
 * @package PrestaShopBundle\Currency\Repository\Installed
 */
interface InstalledRepositoryInterface
{
    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     * ,  The requested currency's id
     *
     * @param $localeCode
     *   Used to localize currency's data
     *
     * @return Currency
     *   The requested Currency
     */
    public function getInstalledCurrencyById($id, $localeCode);

    /**
     * Get currency data by ISO 4217 alphabetic code
     *
     * @param string $isoCode
     *   The requested currency's ISO code
     *
     * @param $localeCode
     *   Used to localize currency's data
     *
     * @return Currency
     *   The requested Currency
     */
    public function getInstalledCurrencyByIsoCode($isoCode, $localeCode);

    /**
     * @param Currency $currency
     *
     * @return mixed
     */
    public function addInstalledCurrency(Currency $currency);

    /**
     * @param \PrestaShopBundle\Currency\Currency $currency
     *
     * @return Currency
     */
    public function updateInstalledCurrency(Currency $currency);

    /**
     * @param \PrestaShopBundle\Currency\Currency $currency
     *
     * @return bool
     */
    public function deleteInstalledCurrency(Currency $currency);
}
