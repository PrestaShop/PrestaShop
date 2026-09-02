<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Extractor;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Parse code content of module, searching for l() calls and retrieve
 * a Message Catalogue with all the keys and translations.
 */
interface LegacyModuleExtractorInterface
{
    /**
     * Extracts the wordings from source code and returns the translation messages.
     * Note that domain names will contain separating dots.
     *
     * @param string $moduleName
     * @param string $locale The locale used for the message catalogue. Note that wordings won't be translated in this locale.
     *
     * @return MessageCatalogue
     */
    public function extract(string $moduleName, string $locale): MessageCatalogue;
}
