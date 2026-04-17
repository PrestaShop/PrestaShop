<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * This interface is the contract for Catalogue providers.
 * A provider must furnish translations for the 3 layers we have :
 *   - Default catalogue : It the base wording, in english, and stored in filesystem or extracted from templates.
 *   - File translated : It's the translation in a specific language. It's stored in filesystem and given by language pack or a module developer.
 *   - Database or User translated : It's the translation made by the user himself. It's done from the Admin and stored in DB.
 *
 * Example: for string "Warning"
 * - default catalogue contains the string "Warning"
 * - if French language pack is used, the file catalogue will contain the string "Attention"
 * - if the BackOffice admin has decided to translate this string in a different way, the user catalogue will contain the BackOffice admin input
 */
interface CatalogueLayersProviderInterface
{
    public const DEFAULT_LOCALE = 'en-US';

    /**
     * Gets the default catalogue : It the base wording, in english, and stored in filesystem or extracted from templates.
     * This 'locale' parameter won't determinate the content of the catalogue returned
     * but it will be the identifier for the MessageCatalogue object so can easily merge other translations.
     * As the default language in templates is english, the default catalogue will have the same values for translationKey and translationValue
     * We only keep the translation keys in the returned Catalogue.
     *
     * @param string $locale the language for which you need translations
     *
     * @return MessageCatalogue
     */
    public function getDefaultCatalogue(string $locale): MessageCatalogue;

    /**
     * Gets the file translated catalogue : it's the translations in a specific language.
     * It's stored in filesystem and given by language pack or a module developer.
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue;

    /**
     * Gets the User modified catalogue : It's the translations made by the user himself.
     * It's done from the Admin and stored in DB.
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue;
}
