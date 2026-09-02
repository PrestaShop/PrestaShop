<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Factory;

use Symfony\Component\Translation\MessageCatalogueInterface;

interface TranslationsFactoryInterface
{
    public const DEFAULT_LOCALE = 'en_US';

    /**
     * Generates extract of global Catalogue, using domain's identifiers.
     *
     * @param string $identifier Domain identifier
     * @param string $locale Locale identifier
     *
     * @return MessageCatalogueInterface
     *
     * @throws ProviderNotFoundException
     */
    public function createCatalogue($identifier, $locale = self::DEFAULT_LOCALE);

    /**
     * Generates Translation tree in Back Office.
     *
     * @param string $domainIdentifier Domain identifier
     * @param string $locale Locale identifier
     * @param null $theme
     * @param null $search
     *
     * @return array Translation tree structure
     *
     * @throws ProviderNotFoundException
     */
    public function createTranslationsArray($domainIdentifier, $locale = self::DEFAULT_LOCALE, $theme = null, $search = null);
}
