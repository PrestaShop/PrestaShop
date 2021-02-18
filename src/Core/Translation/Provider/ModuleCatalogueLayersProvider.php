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

namespace PrestaShop\PrestaShop\Core\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\DomainNormalizer;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Returns the 3 layers of translation catalogues related to the Backoffice interface translations.
 * The default catalogue is in app/Resources/translations/default, in any file starting with "Admin"
 * The file catalogue is in app/Resources/translations/LOCALE, in any file starting with "Admin"
 * The user catalogue is stored in DB, domain starting with Admin and theme is NULL.
 *
 * @see CatalogueLayersProviderInterface to understand the 3 layers.
 */
class ModuleCatalogueLayersProvider implements CatalogueLayersProviderInterface
{
    /**
     * We need a connection to DB to load user translated catalogue.
     *
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    /**
     * This is the directory where Default and FileTranslated translations are stored.
     * For the Backoffice catalogue,
     *   - Default catalogue is within resourceDirectory/default
     *   - FileTranslated catalogue is in resourceDirectory/locale
     *
     * @var string
     */
    private $resourceDirectory;

    /**
     * @var DefaultCatalogueProvider
     */
    private $defaultCatalogueProvider;

    /**
     * @var FileTranslatedCatalogueProvider
     */
    private $fileTranslatedCatalogueProvider;

    /**
     * @var UserTranslatedCatalogueProvider
     */
    private $userTranslatedCatalogueProvider;
    /**
     * @var string
     */
    private $moduleName;
    /**
     * @var string
     */
    private $modulesDirectory;
    /**
     * @var string
     */
    private $translationsDirectory;

    /**
     * @var MessageCatalogue[]
     */
    private $defaultCatalogueCache;
    /**
     * @var LegacyModuleExtractorInterface
     */
    private $legacyModuleExtractor;
    /**
     * @var LoaderInterface
     */
    private $legacyFileLoader;
    /**
     * @var array
     */
    private $filenameFilters;
    /**
     * @var array
     */
    private $translationDomains;

    /**
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param LegacyModuleExtractorInterface $legacyModuleExtractor
     * @param LoaderInterface $legacyFileLoader
     * @param string $modulesDirectory
     * @param string $translationsDirectory
     * @param string $resourceDirectory
     * @param string $moduleName
     * @param array $filenameFilters
     * @param array $translationDomains
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        LoaderInterface $legacyFileLoader,
        string $modulesDirectory,
        string $translationsDirectory,
        string $resourceDirectory,
        string $moduleName,
        array $filenameFilters,
        array $translationDomains
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->moduleName = $moduleName;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->filenameFilters = $filenameFilters;
        $this->translationDomains = $translationDomains;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue(string $locale): MessageCatalogue
    {
        try {
            $defaultCatalogue = $this->getDefaultCatalogueProvider($locale)->getCatalogue($locale);
        } catch (FileNotFoundException $e) {
            var_dump($e->getMessage());
            $defaultCatalogue = $this->getCachedDefaultCatalogue($locale);
        }

        return $defaultCatalogue;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue
    {
        try {
            return $this->getFileTranslatedCatalogueProvider()->getCatalogue($locale);
        } catch (FileNotFoundException $exception) {
            return $this->buildTranslationCatalogueFromLegacyFiles($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getUserTranslatedCatalogueProvider()->getCatalogue($locale);
    }

    private function getDefaultCatalogueProvider(string $locale): DefaultCatalogueProvider
    {
        if (null === $this->defaultCatalogueProvider) {
            $this->defaultCatalogueProvider = new DefaultCatalogueProvider(
                $this->translationsDirectory . DIRECTORY_SEPARATOR . $locale,
                $this->filenameFilters
            );
        }

        return $this->defaultCatalogueProvider;
    }

    private function getFileTranslatedCatalogueProvider(): FileTranslatedCatalogueProvider
    {
        if (null === $this->fileTranslatedCatalogueProvider) {
            $this->fileTranslatedCatalogueProvider = new FileTranslatedCatalogueProvider(
                $this->translationsDirectory,
                $this->filenameFilters
            );
        }

        return $this->fileTranslatedCatalogueProvider;
    }

    private function getUserTranslatedCatalogueProvider(): UserTranslatedCatalogueProvider
    {
        if (null === $this->userTranslatedCatalogueProvider) {
            $this->userTranslatedCatalogueProvider = new UserTranslatedCatalogueProvider(
                $this->databaseTranslationLoader,
                $this->translationDomains
            );
        }

        return $this->userTranslatedCatalogueProvider;
    }

    /**
     * Builds the catalogue including the translated wordings ONLY
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function buildTranslationCatalogueFromLegacyFiles(string $locale): MessageCatalogue
    {
        // the message catalogue needs to be indexed by original wording, but legacy files are indexed by hash
        // therefore, we need to build the default catalogue (by analyzing source code)
        // then cross reference the wordings found in the default catalogue
        // with the hashes found in the module's legacy translation file.

        $legacyFilesCatalogue = new MessageCatalogue($locale);
        $catalogueFromPhpAndSmartyFiles = $this->getDefaultCatalogue($locale);

        try {
            $catalogueFromLegacyTranslationFiles = $this->legacyFileLoader->load(
                $this->getBuiltInModuleDirectory(),
                $locale
            );
        } catch (UnsupportedLocaleException $exception) {
            // this happens when there is no translation file found for the desired locale
            return $catalogueFromPhpAndSmartyFiles;
        }

        foreach ($catalogueFromPhpAndSmartyFiles->all() as $currentDomain => $items) {
            foreach (array_keys($items) as $translationKey) {
                $legacyKey = md5($translationKey);

                if ($catalogueFromLegacyTranslationFiles->has($legacyKey, $currentDomain)) {
                    $legacyFilesCatalogue->set(
                        $translationKey,
                        $catalogueFromLegacyTranslationFiles->get($legacyKey, $currentDomain),
                        // use current domain and not module domain, otherwise we'd lose the third part from the domain
                        $currentDomain
                    );
                }
            }
        }

        return $legacyFilesCatalogue;
    }

    /**
     * @return string
     */
    private function getBuiltInModuleDirectory(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->modulesDirectory,
            $this->moduleName,
            'translations',
        ]) . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the cached default catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function getCachedDefaultCatalogue(string $locale): MessageCatalogue
    {
        $catalogueCacheKey = $this->moduleName . '|' . $locale;

        if (!isset($this->defaultCatalogueCache[$catalogueCacheKey])) {
            $this->defaultCatalogueCache[$catalogueCacheKey] = $this->buildFreshDefaultCatalogue($locale);
        }

        return $this->defaultCatalogueCache[$catalogueCacheKey];
    }

    /**
     * Builds the default catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function buildFreshDefaultCatalogue(string $locale): MessageCatalogue
    {
        $defaultCatalogue = new MessageCatalogue($locale);

        try {
            // look up files in the core translations
            $defaultCatalogue = (new DefaultCatalogueProvider(
                $this->translationsDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->filenameFilters
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $exception) {
            // there are no xliff files for this module in the core
        }

        try {
            // analyze files and extract wordings
            /** @var MessageCatalogue $additionalDefaultCatalogue */
            $additionalDefaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $locale);
            $defaultCatalogue = $this->filterDomains($additionalDefaultCatalogue);
        } catch (UnsupportedLocaleException $exception) {
            // Do nothing as support of legacy files is deprecated
        }

        return $defaultCatalogue;
    }

    /**
     * Replaces dots in the catalogue's domain names
     * and filters out domains not corresponding to the one from this module
     *
     * @param MessageCatalogue $catalogue
     *
     * @return MessageCatalogue
     */
    private function filterDomains(MessageCatalogue $catalogue): MessageCatalogue
    {
        $normalizer = new DomainNormalizer();
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        foreach ($catalogue->getDomains() as $domain) {
            // remove dots
            $newDomain = $normalizer->normalize($domain);

            // add delimiters
            // only add if the domain is relevant to this module
            foreach ($this->filenameFilters as $pattern) {
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
