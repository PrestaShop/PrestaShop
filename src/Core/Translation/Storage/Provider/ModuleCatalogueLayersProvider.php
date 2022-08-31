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

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnsupportedLocaleException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Normalizer\DomainNormalizer;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\DefaultCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\FileTranslatedCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\UserTranslatedCatalogueFinder;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Returns the 3 layers of translation catalogues related to the Module translations.
 * The default catalogue is searched in app/Resources/translations/default, in any file starting with "ModulesMODULENAME"
 * If not found, default catalogue is extracted for module's templates
 * The file catalogue is searched in app/Resources/translations/LOCALE, in any file starting with "ModulesMODULENAME"
 * If not found, we scan the directory modules/MODULENAME/translations/LOCALE
 * The user catalogue is stored in DB, domain starting with ModulesMODULENAME and theme is NULL.
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
     * @var DefaultCatalogueFinder
     */
    private $defaultCatalogueFinder;

    /**
     * @var FileTranslatedCatalogueFinder
     */
    private $fileTranslatedCatalogueFinder;

    /**
     * @var FileTranslatedCatalogueFinder
     */
    private $builtInFileTranslatedCatalogueFinder;

    /**
     * @var UserTranslatedCatalogueFinder
     */
    private $userTranslatedCatalogueFinder;

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
     * @var array<int, string>
     */
    private $filenameFilters;

    /**
     * @var array<int, string>
     */
    private $translationDomains;

    /**
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param LegacyModuleExtractorInterface $legacyModuleExtractor
     * @param LoaderInterface $legacyFileLoader
     * @param string $modulesDirectory
     * @param string $translationsDirectory
     * @param string $moduleName
     * @param array<int, string> $filenameFilters
     * @param array<int, string> $translationDomains
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        LoaderInterface $legacyFileLoader,
        string $modulesDirectory,
        string $translationsDirectory,
        string $moduleName,
        array $filenameFilters,
        array $translationDomains
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
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
        // There are 2 kind of modules : Native (built with core) and Non-Native (the modules installed by user himself)
        // For Native modules, translations are in the default core translations directory
        // For non native modules, the catalogue is built from templates

        // First we search in translation directory in case the module is native
        try {
            $defaultCatalogue = $this->getDefaultCatalogueFinder()->getCatalogue($locale);
        } catch (TranslationFilesNotFoundException $e) {
            $defaultCatalogue = new MessageCatalogue($locale);
        }

        // Then we extract the catalogue from the module's templates and add it to the initial default catalogue, this way
        // even native modules will display wordings that may not be present in the XLF files
        $extractedCatalogue = $this->getDefaultCatalogueExtractedFromTemplates($locale);

        // We merge both catalogues
        foreach ($extractedCatalogue->getDomains() as $domain) {
            $defaultCatalogue->add($extractedCatalogue->all($domain), $domain);
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
        try { // First we search in the module's translation directory
            return $this->getModuleBuiltInFileTranslatedCatalogueFinder()->getCatalogue($locale);
        } catch (TranslationFilesNotFoundException $exception) {
            // If no translation file was found in the module, No Exception
            // we search in the Core's files
        }
        try {
            return $this->getCoreFileTranslatedCatalogueFinder()->getCatalogue($locale);
        } catch (TranslationFilesNotFoundException $exception) {
            // And finally if no translation was found in the Core files, we search in the legacy files
            return $this->buildTranslationCatalogueFromLegacyFiles($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getUserTranslatedCatalogueFinder()->getCatalogue($locale);
    }

    /**
     * @return DefaultCatalogueFinder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getDefaultCatalogueFinder(): DefaultCatalogueFinder
    {
        if (null === $this->defaultCatalogueFinder) {
            $this->defaultCatalogueFinder = new DefaultCatalogueFinder(
                $this->translationsDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->filenameFilters
            );
        }

        return $this->defaultCatalogueFinder;
    }

    /**
     * @return FileTranslatedCatalogueFinder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getCoreFileTranslatedCatalogueFinder(): FileTranslatedCatalogueFinder
    {
        if (null === $this->fileTranslatedCatalogueFinder) {
            $this->fileTranslatedCatalogueFinder = new FileTranslatedCatalogueFinder(
                $this->translationsDirectory,
                $this->filenameFilters
            );
        }

        return $this->fileTranslatedCatalogueFinder;
    }

    /**
     * @return UserTranslatedCatalogueFinder
     */
    private function getUserTranslatedCatalogueFinder(): UserTranslatedCatalogueFinder
    {
        if (null === $this->userTranslatedCatalogueFinder) {
            $this->userTranslatedCatalogueFinder = new UserTranslatedCatalogueFinder(
                $this->databaseTranslationLoader,
                $this->translationDomains
            );
        }

        return $this->userTranslatedCatalogueFinder;
    }

    /**
     * @return FileTranslatedCatalogueFinder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getModuleBuiltInFileTranslatedCatalogueFinder(): FileTranslatedCatalogueFinder
    {
        if (null === $this->builtInFileTranslatedCatalogueFinder) {
            $this->builtInFileTranslatedCatalogueFinder = new FileTranslatedCatalogueFinder(
                implode(DIRECTORY_SEPARATOR, [
                    $this->modulesDirectory,
                    $this->moduleName,
                    'translations',
                ]),
                $this->filenameFilters
            );
        }

        return $this->builtInFileTranslatedCatalogueFinder;
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
     * Returns the translations directory within the module files
     *
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
    private function getDefaultCatalogueExtractedFromTemplates(string $locale): MessageCatalogue
    {
        $catalogueCacheKey = $this->moduleName . '|' . $locale;

        if (!isset($this->defaultCatalogueCache[$catalogueCacheKey])) {
            $this->defaultCatalogueCache[$catalogueCacheKey] = $this->buildFreshDefaultCatalogueFromTemplates($locale);
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
    private function buildFreshDefaultCatalogueFromTemplates(string $locale): MessageCatalogue
    {
        $defaultCatalogue = new MessageCatalogue($locale);
        try {
            // analyze template files and extract wordings
            /** @var MessageCatalogue $additionalDefaultCatalogue */
            $additionalDefaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $locale);
            $defaultCatalogue = $this->convertDomainsAndFilterCatalogue($additionalDefaultCatalogue);
        } catch (UnsupportedLocaleException $exception) {
            // Do nothing as support of legacy files is deprecated
        }

        return $defaultCatalogue;
    }

    /**
     * Replaces dots in the catalogue's domain names
     * and filters out domains not corresponding to the one from this module
     *
     * When extracted from templates, the domain names are in format Modules.MODULENAME.DOMAIN.DOMAIN
     * The required catalogue domains format is something like ModulesModulenameDomain... : Camelcased with max 3 levels
     *
     * @param MessageCatalogue $catalogue
     *
     * @return MessageCatalogue
     */
    private function convertDomainsAndFilterCatalogue(MessageCatalogue $catalogue): MessageCatalogue
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
