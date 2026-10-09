<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Trait TranslatorLanguageTrait used to check if a language has been loaded and reset a language
 */
trait TranslatorLanguageTrait
{
    /**
     * The "db" loader returns the translations of every domain at once, so they are merged once
     * the catalogue is built instead of being registered as one resource per domain, which ran
     * the same query once per domain.
     */
    protected function addDatabaseTranslations(string $locale): void
    {
        $databaseLoader = $this->getLoaders()['db'] ?? null;
        if (null === $databaseLoader) {
            return;
        }

        // A catalogue without any file resource was not loaded for this locale (Context::getTranslatorFromLocale()
        // probes a new translator, trans() may target another locale): it must stay empty, not be cached DB-only
        if ([] === $this->catalogues[$locale]->getResources()) {
            return;
        }

        try {
            $this->catalogues[$locale]->addCatalogue($databaseLoader->load($locale . '.db', $locale));
        } catch (NotFoundResourceException) {
            // Locale without a language in database, like a fallback locale or the installer one
        }
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
