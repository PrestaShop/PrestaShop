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

namespace PrestaShop\PrestaShop\Adapter\Language;

use Exception;
use Language;

/**
 * Class LanguageDataProvider is responsible for providing language data from legacy part.
 */
class LanguageDataProvider
{
    /**
     * Returns languages data.
     *
     * @param bool $active
     * @param bool $shopId
     * @param bool $onlyIds
     *
     * @return array
     */
    public function getLanguages($active = true, $shopId = false, $onlyIds = false)
    {
        return Language::getLanguages($active, $shopId, $onlyIds);
    }

    /**
     * Returns language code by iso code.
     *
     * @param string $isoCode - ISO 3166-2 alpha-2 format code
     *
     * @return false|string|null
     */
    public function getLanguageCodeByIso($isoCode)
    {
        return Language::getLanguageCodeByIso($isoCode);
    }

    /**
     * Gets language details from json file.
     *
     * @param string $locale
     *
     * @return array
     *
     * @throws Exception
     */
    public function getLanguageDetails($locale)
    {
        $result = Language::getJsonLanguageDetails($locale);

        if (false === $result) {
            return [];
        }

        return $result;
    }

    /**
     * Gets the files list for given language, including files from modules.
     *
     * @param string $isoFrom
     * @param string $themeFrom
     * @param string $isoTo
     * @param string $themeTo
     *
     * @return array
     */
    public function getFilesList(
        $isoFrom,
        $themeFrom,
        $isoTo,
        $themeTo
    ) {
        return Language::getFilesList($isoFrom, $themeFrom, $isoTo, $themeTo, false, false, true);
    }
}
