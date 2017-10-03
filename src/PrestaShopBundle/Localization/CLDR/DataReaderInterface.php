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

namespace PrestaShopBundle\Localization\CLDR;

/**
 * Interface DataReaderInterface
 *
 * Defines how a CLDR data reader should behave
 *
 * @package PrestaShopBundle\Localization\CLDR
 */
interface DataReaderInterface
{
    /**
     * Get locale data by code (either language code or IETF locale tag)
     *
     * @param string $localeCode The wanted locale code
     *
     * @return array The locale data
     */
    public function getLocaleByCode($localeCode);

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode    The currency code
     * @param string $localeCode The output locale code (in which language do you want the currency data ?)
     *
     * @return array The currency data
     */
    public function getCurrencyByIsoCode($isoCode, $localeCode);
}
