<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Extractor;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Builds a MessageCatalogue from the extra property registry so that the label and description
 * wordings declared by modules (and the core) become translatable from the back office.
 *
 * Each definition contributes up to two wordings (label and description), each stored under the
 * domain declared alongside it (e.g. "Modules.Demoextrafield.Admin"). A wording without a paired
 * domain falls back to the "messages" domain — the same default Symfony uses — instead of being
 * dropped.
 *
 * The returned catalogue is intentionally not filtered by module or type: callers (the catalogue
 * providers) keep only the domains relevant to their context, reusing the same domain filtering
 * already applied to wordings extracted from source code.
 */
class ExtraPropertyTranslationExtractor
{
    /**
     * @var array<string, MessageCatalogue> built catalogues indexed by locale
     */
    private array $catalogueCache = [];

    public function __construct(
        private readonly ExtraPropertyDefinitionRepositoryInterface $definitionRepository,
    ) {
    }

    /**
     * Returns every registered label/description wording, keyed under its declared translation
     * domain. The catalogue is NOT filtered by context: each catalogue provider narrows it to
     * its own domains. Domain names contain separating dots, like the wordings extracted from
     * source code.
     *
     * @param string $locale The locale used for the message catalogue. Note that wordings won't be translated in this locale.
     */
    public function extract(string $locale): MessageCatalogue
    {
        if (!isset($this->catalogueCache[$locale])) {
            $this->catalogueCache[$locale] = $this->buildCatalogue($locale);
        }

        return $this->catalogueCache[$locale];
    }

    private function buildCatalogue(string $locale): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->definitionRepository->getAllDefinitions() as $definition) {
            $this->addWording($catalogue, $definition->getLabelWording(), $definition->getLabelDomain());
            $this->addWording($catalogue, $definition->getDescriptionWording(), $definition->getDescriptionDomain());
        }

        return $catalogue;
    }

    /**
     * Adds a single wording under its domain, using the wording as both translation key and value
     * (default-catalogue convention). No-op when the wording itself is missing; a missing domain
     * falls back to Symfony's default "messages" domain rather than dropping the wording.
     */
    private function addWording(MessageCatalogue $catalogue, ?string $wording, ?string $domain): void
    {
        if (null === $wording || '' === $wording) {
            return;
        }

        // "messages" is Symfony's default translation domain, used here when none is declared.
        $catalogue->set($wording, $wording, null === $domain || '' === $domain ? 'messages' : $domain);
    }
}
