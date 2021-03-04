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

namespace PrestaShop\PrestaShop\Adapter\Country;

use Configuration;
use Country;
use Db;
use DbQuery;

/**
 * This class will provide data from DB / ORM about Country
 */
class CountryDataProvider
{
    /**
     * Return available countries.
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active coutries
     * @param bool $contain_states return only country with states
     * @param bool $list_states Include the states list with the returned list
     *
     * @return array Countries and corresponding zones
     */
    public function getCountries($id_lang, $active = false, $contain_states = false, $list_states = true)
    {
        return Country::getCountries($id_lang, $active, $contain_states, $list_states);
    }

    /**
     * Returns list of countries IDs which need DNI
     *
     * @return array
     */
    public function getCountriesIdWhichNeedDni()
    {
        $query = new DbQuery();
        $query
            ->select('c.`id_country`')
            ->from('country', 'c')
            ->where('c.`need_identification_number` = 1')
        ;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return array_map(function ($country) { return $country['id_country']; }, $result);
    }

    /**
     * Returns list of countries IDs which need Postcode
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getCountriesIdWhichNeedPostcode()
    {
        $query = new DbQuery();
        $query
            ->select('c.`id_country`')
            ->from('country', 'c')
            ->where('c.`need_zip_code` = 1')
        ;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return array_map(function ($country) { return $country['id_country']; }, $result);
    }

    /**
     * Returns list of countries IDS which need a state
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getCountriesIdWhichNeedState()
    {
        $query = new DbQuery();
        $query
            ->select('c.`id_country`')
            ->from('country', 'c')
            ->where('c.`contains_states` = 1')
        ;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return array_map(function ($country) { return $country['id_country']; }, $result);
    }

    /**
     * Get Country IsoCode by Id.
     *
     * @param int $id Country Id
     *
     * @return string the related iso code
     */
    public function getIsoCodebyId($id = null)
    {
        $countryId = (null === $id) ? Configuration::get('PS_COUNTRY_DEFAULT') : $id;

        return Country::getIsoById($countryId);
    }

    /**
     * Get country Id by ISO code.
     *
     * @param string $isoCode Country ISO code
     *
     * @return int
     */
    public function getIdByIsoCode($isoCode)
    {
        return Country::getByIso($isoCode);
    }
}
