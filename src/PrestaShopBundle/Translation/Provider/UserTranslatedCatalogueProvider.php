<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Gets catalogue translated by the user himself ans stored in the database.
 */
class UserTranslatedCatalogueProvider implements TranslationCatalogueProviderInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var array
     */
    private $translationDomains;

    /**
     * @param DatabaseTranslationLoader $databaseLoader
     * @param array $translationDomains
     */
    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        array $translationDomains
    ) {
        $this->databaseLoader = $databaseLoader;
        $this->translationDomains = $translationDomains;
    }

    /**
     * @param string $locale
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getCatalogue(string $locale, ?string $themeName = null): MessageCatalogueInterface
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->translationDomains as $translationDomain) {
            $domainCatalogue = $this->databaseLoader->load(
                null,
                $locale,
                $translationDomain,
                $themeName
            );

            if ($domainCatalogue instanceof MessageCatalogue) {
                $catalogue->addCatalogue($domainCatalogue);
            }
        }

        return $catalogue;
    }

    /**
     * @param string $locale
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $locale, ?string $themeName = null): MessageCatalogueInterface
    {
        return $this->getCatalogue($locale, $themeName);
    }
}
