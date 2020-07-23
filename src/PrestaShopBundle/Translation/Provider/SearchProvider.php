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
class SearchProvider implements ProviderInterface
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
     * @var string[]
     */
    private $filenameFilters;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @var ExternalLegacyModuleProvider|null
     */
    private $externalModuleLegacySystemProvider;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;
    /**
     * @var string|null
     */
    private $moduleName;
    /**
     * @var string|null
     */
    private $themeName;

    /**
     * @param DatabaseTranslationLoader $databaseLoader
     * @param string $resourceDirectory
     * @param string $modulesDirectory
     * @param string $domain
     * @param ExternalLegacyModuleProvider|null $externalModuleLegacySystemProvider
     * @param string|null $moduleName
     * @param string|null $themeName
     */
    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory,
        string $modulesDirectory,
        string $domain,
        ?ExternalLegacyModuleProvider $externalModuleLegacySystemProvider = null,
        ?string $moduleName = null,
        ?string $themeName = null
    ) {
        $this->modulesDirectory = $modulesDirectory;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;
        $this->resourceDirectory = $resourceDirectory;
        $this->databaseLoader = $databaseLoader;
        $this->domain = $domain;
        $this->moduleName = $moduleName;
        $this->themeName = $themeName;
    }

    /**
     * @param string $locale
     * @param bool $empty
     *
     * @return MessageCatalogueInterface|null
     */
    public function getDefaultCatalogue(string $locale, bool $empty = true): ?MessageCatalogueInterface
    {
        try {
            return (new DefaultCatalogueProvider(
                $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale, $empty);
        } catch (FileNotFoundException $e) {
            if (null !== $this->moduleName && null !== $this->externalModuleLegacySystemProvider) {
                return $this->filterCatalogue(
                    $locale,
                    $this->externalModuleLegacySystemProvider->getDefaultCatalogue($locale, $empty)
                );
            }
        }

        return null;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface|null
     */
    public function getFileTranslatedCatalogue(string $locale): ?MessageCatalogueInterface
    {
        try {
            return (new FileTranslatedCatalogueProvider(
                $this->resourceDirectory,
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $e) {
            if (null !== $this->moduleName && null !== $this->externalModuleLegacySystemProvider) {
                return $this->filterCatalogue(
                    $locale,
                    $this->externalModuleLegacySystemProvider->getFileTranslatedCatalogue($locale)
                );
            }
        }

        return null;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        $translationDomains = ['^' . preg_quote($this->domain) . '([A-Za-z]|$)'];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale, $this->themeName);
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters(): array
    {
        return ['#^' . preg_quote($this->domain, '#') . '([A-Za-z]|\.|$)#'];
    }

    /**
     * Filters the catalogue so that only domains matching the filters are kept
     *
     * @param string $locale
     * @param MessageCatalogueInterface $catalogue
     *
     * @return MessageCatalogueInterface
     */
    private function filterCatalogue(string $locale, MessageCatalogueInterface $catalogue): MessageCatalogueInterface
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

        $catalogue = new MessageCatalogue($locale, $allowedDomains);

        return $catalogue;
    }
}
