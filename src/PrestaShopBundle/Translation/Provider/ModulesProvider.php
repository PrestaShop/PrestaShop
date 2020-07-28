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

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\DomainNormalizer;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\Catalogue\DefaultCatalogueProvider;
use PrestaShopBundle\Translation\Provider\Catalogue\FileTranslatedCatalogueProvider;
use PrestaShopBundle\Translation\Provider\Catalogue\UserTranslatedCatalogueProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Retrieves translations from modules
 */
class ModulesProvider implements ProviderInterface
{
    /**
     * @var string Path where translation files are found
     */
    protected $modulesDirectory;

    /**
     * @var string
     */
    private $translationsDirectory;

    /**
     * @var string domain
     */
    protected $domain;

    /**
     * @var LoaderInterface the translation loader from legacy files
     */
    private $legacyFileLoader;

    /**
     * @var LegacyModuleExtractorInterface the extractor
     */
    private $legacyModuleExtractor;

    /**
     * @var MessageCatalogue[]
     */
    private $defaultCatalogueCache;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @param DatabaseTranslationLoader $databaseLoader
     * @param string $modulesDirectory
     * @param string $translationsDirectory
     * @param LoaderInterface $legacyFileLoader
     * @param LegacyModuleExtractorInterface $legacyModuleExtractor
     * @param string $moduleName
     */
    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $modulesDirectory,
        string $translationsDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        string $moduleName
    ) {
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->databaseLoader = $databaseLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
        $this->moduleName = $moduleName;
    }

    /**
     * @param string $locale
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     */
    public function getDefaultCatalogue(string $locale, bool $empty = true): MessageCatalogueInterface
    {
        try {
            $defaultCatalogue = (new DefaultCatalogueProvider(
                $this->getDefaultResourceDirectory(),
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale, $empty);
        } catch (FileNotFoundException $e) {
            $defaultCatalogue = $this->getCachedDefaultCatalogue($locale);

            if ($empty && $locale !== DefaultCatalogueProvider::DEFAULT_LOCALE) {
                return $this->emptyCatalogue(clone $defaultCatalogue);
            }
        }

        return $defaultCatalogue;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        try {
            $resourceDirectory = implode(DIRECTORY_SEPARATOR, [
                    $this->translationsDirectory,
                    $this->moduleName,
                    'translations',
                ]) . DIRECTORY_SEPARATOR;

            return (new FileTranslatedCatalogueProvider(
                $resourceDirectory,
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $exception) {
            return $this->buildTranslationCatalogueFromLegacyFiles($locale);
        }
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        $translationDomains = ['^' . preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)) . '([A-Z]|$)'];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale);
    }

    /**
     * Builds the catalogue including the translated wordings ONLY
     *
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    private function buildTranslationCatalogueFromLegacyFiles(string $locale): MessageCatalogueInterface
    {
        // the message catalogue needs to be indexed by original wording, but legacy files are indexed by hash
        // therefore, we need to build the default catalogue (by analyzing source code)
        // then cross reference the wordings found in the default catalogue
        // with the hashes found in the module's legacy translation file.

        $legacyFilesCatalogue = new MessageCatalogue($locale);
        $catalogueFromPhpAndSmartyFiles = $this->getDefaultCatalogue($locale, false);

        try {
            $catalogueFromLegacyTranslationFiles = $this->legacyFileLoader->load(
                $this->getDefaultResourceDirectory(),
                $locale
            );
        } catch (UnsupportedLocaleException $exception) {
            // this happens when there no translation file is found for the desired locale
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
     * Replaces dots in the catalogue's domain names
     * and filters out domains not corresponding to the one from this module
     *
     * @param MessageCatalogueInterface $catalogue
     *
     * @return MessageCatalogueInterface
     */
    private function filterDomains(MessageCatalogueInterface $catalogue): MessageCatalogueInterface
    {
        $normalizer = new DomainNormalizer();
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        foreach ($catalogue->getDomains() as $domain) {
            // remove dots
            $newDomain = $normalizer->normalize($domain);

            // add delimiters
            // only add if the domain is relevant to this module
            foreach ($this->getFilenameFilters() as $pattern) {
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

    /**
     * Builds the default catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    private function buildFreshDefaultCatalogue(string $locale): MessageCatalogueInterface
    {
        $defaultCatalogue = new MessageCatalogue($locale);

        try {
            // look up files in the core translations
            $resourceDirectory = implode(DIRECTORY_SEPARATOR, [
                $this->translationsDirectory,
                $this->moduleName,
                'translations',
            ]) . DIRECTORY_SEPARATOR;

            $defaultCatalogue = (new DefaultCatalogueProvider(
                $resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $exception) {
            // there are no xliff files for this module in the core
        }

        try {
            // analyze files and extract wordings
            $additionalDefaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $locale);
            $defaultCatalogue = $this->filterDomains($additionalDefaultCatalogue);
        } catch (UnsupportedLocaleException $exception) {
            // Do nothing as support of legacy files is deprecated
        }

        return $defaultCatalogue;
    }

    /**
     * Returns the cached default catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    private function getCachedDefaultCatalogue(string $locale): MessageCatalogueInterface
    {
        $catalogueCacheKey = $this->moduleName . '|' . $locale;

        if (!isset($this->defaultCatalogueCache[$catalogueCacheKey])) {
            $this->defaultCatalogueCache[$catalogueCacheKey] = $this->buildFreshDefaultCatalogue($locale);
        }

        return $this->defaultCatalogueCache[$catalogueCacheKey];
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters(): array
    {
        return ['#^' . preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)) . '([A-Z]|$)#'];
    }

    /**
     * @return string
     */
    private function getDefaultResourceDirectory(): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->modulesDirectory, $this->moduleName, 'translations']) . DIRECTORY_SEPARATOR;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    private function emptyCatalogue(MessageCatalogueInterface $messageCatalogue): MessageCatalogueInterface
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}
