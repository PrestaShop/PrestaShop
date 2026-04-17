<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
