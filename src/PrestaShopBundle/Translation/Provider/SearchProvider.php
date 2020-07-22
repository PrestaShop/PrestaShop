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
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @param ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider
     * @param DatabaseTranslationLoader $databaseLoader
     * @param string $resourceDirectory
     * @param string $modulesDirectory
     */
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
     * @param string $domain
     * @param string|null $module
     * @param bool $empty
     *
     * @return MessageCatalogueInterface|null
     */
    public function getDefaultCatalogue(
        string $locale,
        string $domain,
        ?string $module = null,
        bool $empty = true
    ): ?MessageCatalogueInterface {
        try {
            return (new DefaultCatalogueProvider(
                $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->getFilenameFilters($domain)
            ))
                ->getCatalogue($locale, $empty);
        } catch (FileNotFoundException $e) {
            if (null !== $module) {
                return $this->filterCatalogue(
                    $locale,
                    $this->externalModuleLegacySystemProvider->getDefaultCatalogue($locale, $module, $empty)
                );
            }
        }

        return null;
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param string|null $module
     *
     * @return MessageCatalogueInterface|null
     */
    public function getFileTranslatedCatalogue(
        string $locale,
        string $domain,
        ?string $module = null
    ): ?MessageCatalogueInterface {
        try {
            return (new FileTranslatedCatalogueProvider(
                $this->resourceDirectory,
                $this->getFilenameFilters($domain)
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $e) {
            if (null !== $module) {
                return $this->filterCatalogue(
                    $locale,
                    $this->externalModuleLegacySystemProvider->getFileTranslatedCatalogue($locale, $module)
                );
            }
        }

        return null;
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param string|null $theme
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(
        string $locale,
        string $domain,
        ?string $theme = null
    ): MessageCatalogueInterface {
        $translationDomains = ['^' . preg_quote($domain) . '([A-Za-z]|$)'];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale, $theme);
    }

    /**
     * @param string $domain
     *
     * @return string[]
     */
    private function getFilenameFilters(string $domain): array
    {
        return ['#^' . preg_quote($domain, '#') . '([A-Za-z]|\.|$)#'];
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
