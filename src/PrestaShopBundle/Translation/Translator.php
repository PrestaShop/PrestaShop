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
     * Registers the extra property registry wordings as "extra_property" resources before the
     * catalogue is built, so they are baked into the compiled (cached) catalogue like any other
     * resource. The addResource() override above also pairs each with a "db" resource, so admin
     * translations of those wordings are loaded alongside their source values.
     */
    protected function initializeCatalogue(string $locale): void
    {
        // Register the registry wordings (and, via the addResource() override, their paired "db"
        // resource) so they are baked into the compiled catalogue. Only real locales are handled:
        // - addResource() resets ALL catalogues when given a fallback locale (Symfony behaviour),
        //   which would corrupt the catalogue being built as initializeCatalogue() recurses into
        //   fallbacks ("en", "default");
        // - the "default" pseudo-locale is not a database language, so its paired "db" resource would
        //   make SqlTranslationLoader throw.
        // This is enough: each real-locale catalogue carries the source wordings (key == value)
        // directly, so trans() resolves them without ever needing the fallback catalogue.
        if (null !== $this->extraPropertyTranslationLoader && !in_array($locale, $this->getFallbackLocales(), true)) {
            foreach ($this->extraPropertyTranslationLoader->getNormalizedDomains($locale) as $domain) {
                $this->addResource('extra_property', 'extra_property', $locale, $domain);
            }
        }

        parent::initializeCatalogue($locale);
    }
}
