<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

/**
 * Trait TranslatorLanguageTrait used to check if a language has been loaded and reset a language
 */
trait TranslatorLanguageTrait
{
    /**
     * @param string $locale Locale code for the catalogue to check if loaded
     *
     * @return bool
     */
    public function isLanguageLoaded($locale)
    {
        return !empty($this->catalogues[$locale]);
    }

    /**
     * @param string $locale Locale code for the catalogue to be cleared
     */
    public function clearLanguage($locale)
    {
        unset($this->catalogues[$locale]);
    }
}
