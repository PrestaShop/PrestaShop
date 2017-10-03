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

namespace PrestaShopBundle\Localization\DataSource;

use PrestaShopBundle\Localization\CLDR\LocaleData;
use PrestaShopBundle\Localization\Exception\Exception;

/**
 * Interface DataSourceInterface
 *
 * Defines how a Locale data source should behave
 *
 * @package PrestaShopBundle\Localization\DataSource
 */
interface DataSourceInterface
{
    /**
     * Get locale data by internal database identifier
     *
     * @param int $id
     *
     * @return LocaleData The locale data
     */
    public function getLocaleById($id);

    /**
     * Get locale data by code (either language code or IETF locale tag)
     *
     * @param string $code
     *
     * @return LocaleData The locale data
     */
    public function getLocaleByCode($code);

    /**
     * Create a new locale in data source
     *
     * @param LocaleData $localeData
     *
     * @return int The id of newly created locale
     */
    public function createLocale(LocaleData $localeData);

    /**
     * Update an existing locale in data source
     *
     * @param LocaleData $localeData
     *
     * @return LocaleData The saved item
     */
    public function updateLocale(LocaleData $localeData);

    /**
     * Delete an existing locale in data source
     *
     * @param LocaleData $localeData
     *
     * @throws Exception If delete was unsuccessful
     */
    public function deleteLocale(LocaleData $localeData);
}
