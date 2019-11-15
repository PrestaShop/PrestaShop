<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Currency;

use Currency;
use Exception;
use PrestaShopException;

/**
 * This class will provide data from DB / ORM about Currency.
 */
interface CurrencyDataProviderInterface
{
    /**
     * Return available currencies.
     *
     * @param bool $object
     * @param bool $active
     * @param bool $group_by
     *
     * @return array Currencies
     */
    public function getCurrencies($object = false, $active = true, $group_by = false);

    /**
     * Return raw currencies data from the database (not deleted + active currencies).
     *
     * @param bool $currentShopOnly If true returns only currencies associated to current shop
     *
     * @return array[] Available currencies
     */
    public function findAll($currentShopOnly = true);

    /**
     * Return raw data of all installed currencies in the database (regardless of their active or soft deleted status).
     *
     * @return array[] Currencies installed in database
     */
    public function findAllInstalled();

    /**
     * Get a Currency entity instance by ISO code.
     *
     * @param string $isoCode
     *                        An ISO 4217 currency code
     * @param int|null $idLang
     *                         Set this parameter if you want the currency in a specific language.
     *                         If null, default language will be used
     *
     * @return currency|null
     *                       The asked Currency object, or null if not found
     */
    public function getCurrencyByIsoCode($isoCode, $idLang = null);

    /**
     * Get a Currency entity instance.
     * If the passed ISO code is known, this Currency entity will be loaded with known data.
     *
     * @param string $isoCode
     *                        An ISO 4217 currency code
     * @param int|null $idLang
     *                         Set this parameter if you want the currency in a specific language.
     *                         If null, default language will be used
     *
     * @return currency
     *                  The asked Currency object, loaded with relevant data if passed ISO code is known
     */
    public function getCurrencyByIsoCodeOrCreate($isoCode, $idLang = null);

    /**
     * Persists a Currency entity into DB.
     * If this entity already exists in DB (has a known currency_id), it will be updated.
     *
     * @param Currency $currencyEntity
     *                                 Currency object model to save
     *
     * @throws PrestaShopException
     *                             If something wrong happened with DB when saving $currencyEntity
     * @throws Exception
     *                   If an unexpected result is retrieved when saving $currencyEntity
     */
    public function saveCurrency(Currency $currencyEntity);

    /**
     * Gets a legacy Currency instance by ID.
     *
     * @param int $currencyId
     *
     * @return Currency
     */
    public function getCurrencyById($currencyId);

    /**
     * Get Default currency Iso code.
     */
    public function getDefaultCurrencyIsoCode();
}
