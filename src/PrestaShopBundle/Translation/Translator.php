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
     */
    public function addResource($format, $resource, $locale, $domain = null): void
    {
        parent::addResource($format, $resource, $locale, $domain);
        parent::addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
    }

    /**
     * {@inheritdoc}
     *
     * Adds the extra property registry wordings to the catalogue being built, so they are baked
     * into the compiled (cached) catalogue like any other resource (dumpCatalogue() calls this
     * method and dumps $this->catalogues[$locale] right after).
     *
     * Two things are done for the registry domains, in this order:
     *  - their "db" resources are registered BEFORE the catalogue is built, so admin translations
     *    of those wordings (ps_translation) load like every other domain's and legitimately
     *    override the sources;
     *  - the wordings themselves are merged AFTER the catalogue is built, and NON-overwriting: a
     *    wording equal to a key another resource already defines (core or module XLF, an admin
     *    translation) resolves to that existing translation instead of replacing it with its
     *    untranslated source. Resources load in registration order and these would come last,
     *    so registering them as resources let a definition author un-translate core strings
     *    shop-wide by declaring a core domain; merging by hand closes that.
     *
     * Only real locales are handled: addResource() resets ALL catalogues when given a fallback
     * locale (Symfony behaviour), which would corrupt the catalogue being built as this method
     * recurses into fallbacks ("en", "default"), and the "default" pseudo-locale is not a
     * database language, so its "db" resource would make SqlTranslationLoader throw. Each
     * real-locale catalogue carries the source wordings (key == value) directly, so trans()
     * resolves them without ever needing the fallback catalogue.
     */
    protected function initializeCatalogue(string $locale): void
    {
        $handlesRegistryWordings = null !== $this->extraPropertyTranslationLoader
            && !in_array($locale, $this->getFallbackLocales(), true);

        $domains = $handlesRegistryWordings ? $this->extraPropertyTranslationLoader->getNormalizedDomains($locale) : [];
        foreach ($domains as $domain) {
            // parent:: on purpose — the addResource() override above would pair this "db" resource
            // with a second, identical "db" resource.
            parent::addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
        }

        parent::initializeCatalogue($locale);

        if ([] === $domains) {
            return;
        }
        $catalogue = $this->catalogues[$locale];
        foreach ($domains as $domain) {
            $wordings = $this->extraPropertyTranslationLoader->load('extra_property', $locale, $domain)->all($domain);
            foreach ($wordings as $id => $message) {
                if (!$catalogue->defines((string) $id, $domain)) {
                    $catalogue->set((string) $id, $message, $domain);
                }
            }
        }
    }
}
