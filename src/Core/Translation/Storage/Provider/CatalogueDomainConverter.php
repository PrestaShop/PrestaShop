<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Storage\Normalizer\DomainNormalizer;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Normalizes the domain names of a source catalogue (removing dots, e.g. "Modules.Foo.Admin"
 * becomes "ModulesFooAdmin") and keeps only the domains matching one of the given filename
 * filter patterns.
 *
 * Shared by the module and core catalogue providers so the same per-context domain filtering is
 * applied to wordings gathered from any source (source code templates, the extra property
 * registry, …): when wordings are extracted, the domain names are in format
 * Modules.MODULENAME.DOMAIN.DOMAIN, while the catalogue domains must be camelcased with the dots
 * removed (ModulesModulenameDomain…).
 */
class CatalogueDomainConverter
{
    /**
     * @param MessageCatalogue $catalogue source catalogue, with dot-separated domain names
     * @param array<int, string> $filenameFilters regex patterns; a normalized domain is kept only when it matches one of them
     *
     * @return MessageCatalogue a new catalogue with normalized domains, restricted to the matching ones
     */
    public function normalizeAndFilter(MessageCatalogue $catalogue, array $filenameFilters): MessageCatalogue
    {
        $normalizer = new DomainNormalizer();
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        foreach ($catalogue->getDomains() as $domain) {
            // remove dots
            $newDomain = $normalizer->normalize($domain);

            // add delimiters
            // only add if the domain is relevant to one of the desired filters
            foreach ($filenameFilters as $pattern) {
                if (preg_match($pattern, $newDomain)) {
                    $newCatalogue->add(
                        $catalogue->all($domain),
                        $newDomain
                    );
                    break;
                }
            }
        }

        return $newCatalogue;
    }
}
