<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Loader;

use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ExtraPropertyTranslationExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Normalizer\DomainNormalizer;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Exposes the label/description wordings declared in the extra property registry as a translation
 * resource ("extra_property" format), so the back-office (FrameworkBundle) translator bakes them
 * into its compiled catalogue — the same way XLF-declared wordings are.
 *
 * The registry domains are dot-separated (e.g. "Modules.Foo.Admin"); they are normalized to the
 * catalogue convention ("ModulesFooAdmin") so they line up with the rest of the catalogue and with
 * Module::isUsingNewTranslationSystem().
 */
class ExtraPropertyTranslationLoader implements LoaderInterface
{
    private DomainNormalizer $domainNormalizer;

    public function __construct(
        private readonly ExtraPropertyTranslationExtractor $extraPropertyTranslationExtractor,
    ) {
        $this->domainNormalizer = new DomainNormalizer();
    }

    /**
     * Lists the normalized catalogue domains the registry contributes for a locale, so callers can
     * register one resource per domain.
     *
     * @return list<string>
     */
    public function getNormalizedDomains(string $locale): array
    {
        $domains = [];
        foreach ($this->extractSafely($locale)->getDomains() as $domain) {
            $domains[$this->domainNormalizer->normalize($domain)] = true;
        }

        return array_keys($domains);
    }

    /**
     * {@inheritdoc}
     *
     * Returns the registry wordings whose normalized domain matches the requested one, as a default
     * catalogue (key == value). Several dotted domains may normalize to the same catalogue domain;
     * all their wordings are merged.
     */
    public function load($resource, $locale, $domain = 'messages'): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);
        $sourceCatalogue = $this->extractSafely($locale);

        foreach ($sourceCatalogue->getDomains() as $sourceDomain) {
            if ($this->domainNormalizer->normalize($sourceDomain) === $domain) {
                $catalogue->add($sourceCatalogue->all($sourceDomain), $domain);
            }
        }

        return $catalogue;
    }

    /**
     * Reads the registry, tolerating an unavailable database: the translation cache is warmed by CLI
     * commands (e.g. cache:clear) that can run without a configured/reachable database, so a connection
     * failure must contribute no wordings rather than break the whole catalogue build.
     */
    private function extractSafely(string $locale): MessageCatalogue
    {
        try {
            return $this->extraPropertyTranslationExtractor->extract($locale);
        } catch (DBALException) {
            return new MessageCatalogue($locale);
        }
    }
}
