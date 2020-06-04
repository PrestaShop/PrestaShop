<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class UserTranslatedCatalogueProvider implements TranslationCatalogueProviderInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var array
     */
    private $translationDomains = [''];

    /**
     * @var string
     */
    private $locale;

    public function __construct(DatabaseTranslationLoader $databaseLoader)
    {
        $this->databaseLoader = $databaseLoader;
    }

    /**
     * @param string|null $theme
     *
     * @return $this
     */
    public function setTheme(?string $theme): UserTranslatedCatalogueProvider
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @param string|null $locale
     *
     * @return $this
     */
    public function setLocale(?string $locale): UserTranslatedCatalogueProvider
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Returns a list of patterns used to choose which wordings will be imported from database.
     * Patterns from this list will be run against translation domains.
     *
     * @return string[] List of Mysql compatible regexes (no regex delimiter)
     */
    protected function getTranslationDomains(): array
    {
        return $this->translationDomains;
    }

    /**
     * @param array $translationDomains
     *
     * @return UserTranslatedCatalogueProvider
     */
    public function setTranslationDomains(array $translationDomains): UserTranslatedCatalogueProvider
    {
        $this->translationDomains = $translationDomains;

        return $this;
    }

    /**
     * @return MessageCatalogueInterface
     */
    public function getCatalogue(): MessageCatalogueInterface
    {
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }

        $catalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            $domainCatalogue = $this->databaseLoader->load(
                null,
                $this->locale,
                $translationDomain,
                $this->theme
            );

            if ($domainCatalogue instanceof MessageCatalogue) {
                $catalogue->addCatalogue($domainCatalogue);
            }
        }

        return $catalogue;
    }
}
