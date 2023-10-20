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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Translation\Locale;

use Exception;

/**
 * Helper to manipulate the locales (IETF language tag) specific to PrestaShop
 *
 * @doc https://en.wikipedia.org/wiki/IETF_language_tag#Syntax_of_language_tags
 */
final class Converter
{
    /**
     * @var string the path to the JSON file responsible of mapping between lang and locale
     */
    private $translationsMappingFile;

    /**
     * @param string $translationsMappingFile
     */
    public function __construct(string $translationsMappingFile)
    {
        $this->translationsMappingFile = $translationsMappingFile;
    }

    /**
     * @param string $locale the locale (like "fr-FR")
     *
     * @return string|bool the legacy PrestaShop locale (like "fr")
     *
     * @throws Exception
     */
    public function toLegacyLocale(string $locale)
    {
        return array_search($locale, $this->getLangToLocalesMapping());
    }

    /**
     * @param string $legacyLocale the legacy PrestaShop locale
     *
     * @return string|bool the locale
     *
     * @throws Exception
     */
    public function toLanguageTag(string $legacyLocale)
    {
        $mappingLocales = $this->getLangToLocalesMapping();

        return isset($mappingLocales[$legacyLocale]) ? $mappingLocales[$legacyLocale] : false;
    }

    /**
     * Get the PrestaShop locale from real locale (like "fr-FR")
     *
     * @param string $locale
     *
     * @return string The PrestaShop locale (like "fr_FR")
     */
    public static function toPrestaShopLocale(string $locale): string
    {
        return str_replace('-', '_', $locale);
    }

    /**
     * Extracted from TranslationService class
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getLangToLocalesMapping()
    {
        $legacyToStandardLocalesJson = file_get_contents($this->translationsMappingFile);
        $legacyToStandardLocales = json_decode($legacyToStandardLocalesJson, true);

        $jsonLastErrorCode = json_last_error();
        if (JSON_ERROR_NONE !== $jsonLastErrorCode) {
            throw new Exception('The legacy to standard locales JSON could not be decoded', $jsonLastErrorCode);
        }

        return $legacyToStandardLocales;
    }
}
