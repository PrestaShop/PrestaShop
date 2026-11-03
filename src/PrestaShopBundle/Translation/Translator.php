<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use PrestaShopBundle\Translation\Loader\ExtraPropertyTranslationLoader;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * Replacement for the original Symfony FrameworkBundle translator
 */
class Translator extends BaseTranslator implements TranslatorInterface
{
    use PrestaShopTranslatorTrait;
    use TranslatorLanguageTrait;

    private ?ExtraPropertyTranslationLoader $extraPropertyTranslationLoader = null;

    /**
     * Injected via OverrideTranslatorServiceCompilerPass (the FrameworkBundle builds this service
     * with a fixed constructor signature, so the dependency is set through a method call instead).
     */
    public function setExtraPropertyTranslationLoader(ExtraPropertyTranslationLoader $extraPropertyTranslationLoader): void
    {
        $this->extraPropertyTranslationLoader = $extraPropertyTranslationLoader;
    }

    /**
     * {@inheritdoc}
     *
     * Completes the catalogue being built, so the additions are baked into the compiled (cached)
     * catalogue (dumpCatalogue() calls this method and dumps $this->catalogues[$locale] right after):
     *  - the database translations (ps_translation) are merged once, after every resource is loaded,
     *    so they override the sources of every domain whenever the resources were registered;
     *  - the extra property registry wordings are merged last, NON-overwriting: a wording equal to a
     *    key another resource already defines (core or module XLF, an admin translation) resolves to
     *    that existing translation instead of replacing it with its untranslated source, so a
     *    definition author cannot un-translate core strings shop-wide by declaring a core domain.
     *
     * Only real locales are handled: the "default" pseudo-locale is not a database language. Each
     * real-locale catalogue carries the source wordings (key == value) directly, so trans()
     * resolves them without ever needing the fallback catalogue.
     */
    protected function initializeCatalogue(string $locale): void
    {
        parent::initializeCatalogue($locale);

        if (in_array($locale, $this->getFallbackLocales(), true)) {
            return;
        }

        $this->addDatabaseTranslations($locale);

        if (null === $this->extraPropertyTranslationLoader) {
            return;
        }
        $catalogue = $this->catalogues[$locale];
        foreach ($this->extraPropertyTranslationLoader->getNormalizedDomains($locale) as $domain) {
            $wordings = $this->extraPropertyTranslationLoader->load('extra_property', $locale, $domain)->all($domain);
            foreach ($wordings as $id => $message) {
                if (!$catalogue->defines((string) $id, $domain)) {
                    $catalogue->set((string) $id, $message, $domain);
                }
            }
        }
    }
}
