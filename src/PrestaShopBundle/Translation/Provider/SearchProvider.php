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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Able to search translations for a specific translation domains across multiple sources
 */
class SearchProvider implements SearchProviderInterface
{
    /**
     * @var string Path where translation files are found
     */
    protected $resourceDirectory;

    /**
     * @var string Catalogue domain
     */
    protected $domain;

    /**
     * @var string locale
     */
    protected $locale;

    /**
     * @var string[]
     */
    private $filenameFilters;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    public function __construct(
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider,
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory,
        string $modulesDirectory
    ) {
        $this->modulesDirectory = $modulesDirectory;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;
        $this->resourceDirectory = $resourceDirectory;
        $this->databaseLoader = $databaseLoader;
    }

    /**
     * @param string $locale
     *
     * @return SearchProvider
     */
    public function setLocale(string $locale): SearchProvider
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return SearchProvider
     */
    public function setDomain(string $domain): SearchProvider
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @param string $moduleName
     *
     * @return SearchProvider
     */
    public function setModuleName($moduleName): SearchProvider
    {
        $this->externalModuleLegacySystemProvider->setModuleName($moduleName);

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'search';
    }

    /**
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
    {
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }
        if (null === $this->domain) {
            throw new \LogicException('Domain cannot be null. Call setDomain first');
        }

        $messageCatalogue = $this->getDefaultCatalogue();

        // Merge catalogues

        $xlfCatalogue = $this->getFilesystemCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);
        unset($xlfCatalogue);

        $databaseCatalogue = $this->getUserTranslatedCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);
        unset($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        try {
            return (new DefaultCatalogueProvider(
                $this->locale,
                $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->getFilenameFilters()
            ))
                ->getCatalogue($empty);
        } catch (FileNotFoundException $e) {
            return $this->filterCatalogue(
                $this->externalModuleLegacySystemProvider->getDefaultCatalogue($empty)
            );
        }
    }

    /**
     * @return MessageCatalogueInterface
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        try {
            return (new FileTranslatedCatalogueProvider(
                $this->locale,
                $this->resourceDirectory,
                $this->getFilenameFilters()
            ))
                ->getCatalogue();
        } catch (FileNotFoundException $e) {
            return $this->filterCatalogue(
                $this->externalModuleLegacySystemProvider->getFilesystemCatalogue()
            );
        }
    }

    /**
     * @param string|null $theme
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $theme = null): MessageCatalogueInterface
    {
        $translationDomains = ['^' . preg_quote($this->domain) . '([A-Za-z]|$)'];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $this->locale,
            $translationDomains
        ))
            ->getCatalogue($theme);
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters()
    {
        return ['#^' . preg_quote($this->domain, '#') . '([A-Za-z]|\.|$)#'];
    }

    /**
     * Filters the catalogue so that only domains matching the filters are kept
     *
     * @param MessageCatalogueInterface $catalogue
     *
     * @return MessageCatalogueInterface
     */
    private function filterCatalogue(MessageCatalogueInterface $catalogue)
    {
        $allowedDomains = [];

        // return only elements whose domain matches the filters
        foreach ($catalogue->all() as $domain => $messages) {
            foreach ($this->filenameFilters as $filter) {
                if (preg_match($filter, $domain)) {
                    $allowedDomains[$domain] = $messages;
                    break;
                }
            }
        }

        $catalogue = new MessageCatalogue($this->locale, $allowedDomains);

        return $catalogue;
    }
}
