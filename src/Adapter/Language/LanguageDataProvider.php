<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param int|bool $shopId
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
