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
     * @var callable|null registers the translation resources of one locale on this translator
     */
    private $languageLoader;

    /**
     * @var array<string, bool> locales this translator has already been asked to register
     */
    private $requestedLanguages = [];

    /**
     * @param callable $languageLoader called with a locale code, to register that locale's resources
     */
    public function setLanguageLoader(callable $languageLoader): void
    {
        $this->languageLoader = $languageLoader;
    }

    /**
     * Register the resources of a locale this translator never loaded.
     *
     * WHY: resources are registered for the translator's own locale only, so a wording explicitly
     * asked for in another language found no catalogue and came back as the wording itself - the
     * $locale argument of trans() silently did nothing outside the Symfony container.
     *
     * @param string $locale Locale code to make available
     */
    public function loadLanguageOnDemand(string $locale): void
    {
        if (null === $this->languageLoader
            || isset($this->requestedLanguages[$locale])
            || $this->isLanguageLoaded($locale)
        ) {
            return;
        }

        // WHY marked before the call rather than after: loading registers resources, which Symfony
        // answers by dropping the cached catalogue, so a locale that resolves to nothing must not
        // rebuild every catalogue again on each following lookup.
        $this->requestedLanguages[$locale] = true;
        ($this->languageLoader)($locale);
    }

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
