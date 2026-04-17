<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Define contract to retrieve translations.
 */
interface ProviderInterface
{
    /**
     * @return string[] List of directories to parse
     */
    public function getDirectories();

    /**
     * Returns a list of patterns for catalogue domain filtering (including XLF file lookup)
     *
     * @return string[]
     */
    public function getFilters();

    /**
     * Returns a list of patterns for translation domains to get from database.
     *
     * @return string[] List of Mysql compatible regexes (no regex delimiter)
     */
    public function getTranslationDomains();

    /**
     * @return string Locale used to build the MessageCatalogue
     */
    public function getLocale();

    /**
     * @return MessageCatalogueInterface A provider must return a MessageCatalogue
     */
    public function getMessageCatalogue();

    /**
     * @return string Unique identifier
     */
    public function getIdentifier();
}
