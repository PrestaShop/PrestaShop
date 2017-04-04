<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Country;

use \CountryCore;

/**
 * This class will provide data from DB / ORM about Country
 */
class CountryDataProvider
{
    /**
     * Return available countries
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active coutries
     * @param bool $contain_states return only country with states
     * @param bool $list_states Include the states list with the returned list
     *
     * @return Array Countries and corresponding zones
     */
    public function getCountries($id_lang, $active = false, $contain_states = false, $list_states = true)
    {
        return CountryCore::getCountries($id_lang, $active = false, $contain_states = false, $list_states = true);
    }

    /**
     * Get Country IsoCode by Id
     *
     * @param int $id Country Id
     *
     * @return string the related iso code
     */
     public function getIsoCodebyId($id = null)
     {
         $countryId = (null === $id) ? \Configuration::get('PS_COUNTRY_DEFAULT') : $id;

         return CountryCore::getIsoById($countryId);
     }
}
