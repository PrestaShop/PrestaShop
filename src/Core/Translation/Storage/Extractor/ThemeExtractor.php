<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Extractor;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueLayersProviderInterface;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extract all theme translations from Theme templates.
 *
 * xliff is the default file format, you can add custom File dumpers.
 */
class ThemeExtractor
{
    /**
     * @var SmartyExtractor the Smarty Extractor
     */
    private $smartyExtractor;

    public function __construct(SmartyExtractor $smartyExtractor)
    {
        $this->smartyExtractor = $smartyExtractor;
    }

    /**
     * @param Theme $theme
     * @param string|null $locale
     *
     * @return MessageCatalogue
     *
     * @throws Exception
     */
    public function extract(Theme $theme, ?string $locale = null): MessageCatalogue
    {
        if (null === $locale) {
            $locale = CatalogueLayersProviderInterface::DEFAULT_LOCALE;
        }

        $catalogue = new MessageCatalogue($locale);

        $this->smartyExtractor->extract(
            rtrim($theme->getDirectory(), '/'),
            $catalogue
        );

        return $this->normalize($catalogue);
    }

    /**
     * Normalizes domains in a catalogue by removing dots
     *
     * @param MessageCatalogue $catalogue
     *
     * @return MessageCatalogue
     */
    private function normalize(MessageCatalogue $catalogue): MessageCatalogue
    {
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        foreach ($catalogue->all() as $domain => $messages) {
            $newDomain = $this->normalizeDomain($domain);
            $newCatalogue->add($messages, $newDomain);
        }

        foreach ($catalogue->getMetadata('', '') as $domain => $metadata) {
            $newDomain = $this->normalizeDomain($domain);
            foreach ($metadata as $key => $value) {
                $newCatalogue->setMetadata((string) $key, $value, $newDomain);
            }
        }

        return $newCatalogue;
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    private function normalizeDomain(string $domain): string
    {
        return strtr($domain, ['.' => '']);
    }
}
