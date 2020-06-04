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

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\DomainNormalizer;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Exception\UnsupportedModuleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Be able to retrieve information from legacy translation files
 */
class ExternalModuleLegacySystemProvider implements SearchProviderInterface
{
    /**
     * @var string Path where translation files are found
     */
    protected $resourceDirectory;

    /**
     * @var string locale
     */
    protected $locale;

    /**
     * @var string domain
     */
    protected $domain;

    /**
     * @var SearchProviderInterface|ModuleProvider the module provider
     */
    private $moduleProvider;

    /**
     * @var LoaderInterface the translation loader from legacy files
     */
    private $legacyFileLoader;

    /**
     * @var LegacyModuleExtractorInterface the extractor
     */
    private $legacyModuleExtractor;

    /**
     * @var string the module name
     */
    private $moduleName;

    /**
     * @var MessageCatalogue[]
     */
    private $defaultCatalogueCache;

    /**
     * @var string[]
     */
    private $filenameFilters;
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        ModuleProvider $moduleProvider
    ) {
        $this->moduleProvider = $moduleProvider;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * @param string $locale
     *
     * @return ExternalModuleLegacySystemProvider
     */
    public function setLocale(string $locale): ExternalModuleLegacySystemProvider
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'external_legacy_module';
    }

    /**
     * @param string $moduleName
     *
     * @return ExternalModuleLegacySystemProvider
     */
    public function setModuleName(string $moduleName): self
    {
        if (null === $this->moduleName || empty($this->moduleName)) {
            UnsupportedModuleException::moduleNotProvided(self::getIdentifier());
        }

        $this->moduleName = $moduleName;

        // ugly hack, I know
        $this->domain = DomainHelper::buildModuleBaseDomain($moduleName);

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return ExternalModuleLegacySystemProvider
     */
    public function setDomain(string $domain): ExternalModuleLegacySystemProvider
    {
        throw new InvalidArgumentException(__CLASS__ . ' does not allow calls to setDomain()');
    }

    /**
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $translatedCatalogue = $this->buildTranslationCatalogueFromLegacyFiles($this->locale);
        $messageCatalogue->addCatalogue($translatedCatalogue);

        $databaseCatalogue = $this->getUserTranslatedCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        try {
            $defaultCatalogue = (new DefaultCatalogueProvider())
                ->setFilenameFilters($this->getFilenameFilters())
                ->setDirectory($this->getDefaultResourceDirectory())
                ->setLocale($this->locale)
                ->getCatalogue($empty);
        } catch (FileNotFoundException $e) {
            $defaultCatalogue = $this->getCachedDefaultCatalogue();

            if ($empty && $this->locale !== DefaultCatalogueProvider::DEFAULT_LOCALE) {
                return $this->emptyCatalogue(clone $defaultCatalogue);
            }
        }

        return $defaultCatalogue;
    }

    /**
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        try {
            $translationCatalogue = $this->moduleProvider
                ->setModuleName($this->moduleName)
                ->getFilesystemCatalogue()
            ;
        } catch (FileNotFoundException $exception) {
            $translationCatalogue = $this->buildTranslationCatalogueFromLegacyFiles($this->locale);
        }

        return $translationCatalogue;
    }

    /**
     * @param string|null $theme
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $theme = null): MessageCatalogueInterface
    {
        $translationDomains = ['^' . preg_quote($this->domain) . '([A-Z]|$)'];

        return (new UserTranslatedCatalogueProvider($this->databaseLoader))
            ->setTranslationDomains($translationDomains)
            ->setLocale($this->locale)
            ->setTheme($theme)
            ->getCatalogue();
    }

    /**
     * Builds the catalogue including the translated wordings ONLY
     *
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    private function buildTranslationCatalogueFromLegacyFiles(string $locale)
    {
        // the message catalogue needs to be indexed by original wording, but legacy files are indexed by hash
        // therefore, we need to build the default catalogue (by analyzing source code)
        // then cross reference the wordings found in the default catalogue
        // with the hashes found in the module's legacy translation file.

        $legacyFilesCatalogue = new MessageCatalogue($locale);
        $catalogueFromPhpAndSmartyFiles = $this->getDefaultCatalogue(false);

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

    /**
     * Builds the default catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function buildFreshDefaultCatalogue(string $locale): MessageCatalogueInterface
    {
        $defaultCatalogue = new MessageCatalogue($locale);

        try {
            $this->moduleProvider->setLocale($locale);
            // look up files in the core translations
            $defaultCatalogue = $this->moduleProvider
                ->setModuleName($this->moduleName)
                ->getDefaultCatalogue($locale);
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
     * @return MessageCatalogueInterface
     */
    private function getCachedDefaultCatalogue(): MessageCatalogueInterface
    {
        $catalogueCacheKey = $this->moduleName . '|' . $this->locale;

        if (!isset($this->defaultCatalogueCache[$catalogueCacheKey])) {
            $this->defaultCatalogueCache[$catalogueCacheKey] = $this->buildFreshDefaultCatalogue($this->locale);
        }

        return $this->defaultCatalogueCache[$catalogueCacheKey];
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters()
    {
        return ['#^' . preg_quote($this->domain) . '([A-Z]|$)#'];
    }

    private function getDefaultResourceDirectory()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->resourceDirectory, $this->moduleName, 'translations']) . DIRECTORY_SEPARATOR;
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
