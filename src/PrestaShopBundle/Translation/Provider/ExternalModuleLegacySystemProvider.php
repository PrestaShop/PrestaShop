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

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\DomainNormalizer;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Exception\UnsupportedModuleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Be able to retrieve information from legacy translation files
 */
class ExternalModuleLegacySystemProvider implements ProviderInterface
{
    public const DEFAULT_LOCALE = 'en-US';

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string the resource directory
     */
    protected $resourceDirectory;

    /**
     * @var string the Catalogue locale
     */
    protected $locale;

    /**
     * @var string the Catalogue domain
     */
    protected $domain;

    /**
     * @var ModuleProvider Module provider
     */
    private $moduleProvider;

    /**
     * @var LoaderInterface Translation loader from legacy files
     */
    private $legacyFileLoader;

    /**
     * @var LegacyModuleExtractorInterface Extractor
     */
    private $legacyModuleExtractor;

    /**
     * @var string Module name
     */
    private $moduleName;

    /**
     * @var MessageCatalogue[]
     */
    private $defaultCatalogueCache;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        $resourceDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        ModuleProvider $moduleProvider
    ) {
        $this->moduleProvider = $moduleProvider;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories(): array
    {
        return [$this->getResourceDirectory()];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain)
    {
        throw new InvalidArgumentException(__CLASS__ . ' does not allow calls to setDomain()');
    }

    /**
     * Get the PrestaShop locale from real locale.
     *
     * @return string The PrestaShop locale
     *
     * @deprecated since 1.7.6, to be removed in the next major
     */
    public function getPrestaShopLocale(): string
    {
        @trigger_error(
            '`ExternalModuleLegacySystemProvider::getPrestaShopLocale` function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return Converter::toPrestaShopLocale($this->locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue(): MessageCatalogue
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $translatedCatalogue = $this->buildTranslationCatalogueFromLegacyFiles();
        $messageCatalogue->addCatalogue($translatedCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogue
    {
        $defaultCatalogue = $this->getCachedDefaultCatalogue();

        if ($empty && $this->locale !== self::DEFAULT_LOCALE) {
            return $this->emptyCatalogue(clone $defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getXliffCatalogue(): MessageCatalogue
    {
        try {
            $translationCatalogue = $this->moduleProvider
                ->setModuleName($this->moduleName)
                ->setLocale($this->locale)
                ->getXliffCatalogue()
            ;
        } catch (FileNotFoundException $exception) {
            $translationCatalogue = $this->buildTranslationCatalogueFromLegacyFiles();
        }

        return $translationCatalogue;
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param string|null $themeName
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue(string $themeName = null): MessageCatalogue
    {
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            if (!($this->getDatabaseLoader() instanceof DatabaseTranslationLoader)) {
                continue;
            }
            $domainCatalogue = $this->getDatabaseLoader()->load($this->locale, $translationDomain, $themeName);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * @return string Path to app/Resources/translations/{locale}
     */
    public function getResourceDirectory(): string
    {
        return $this->getDefaultResourceDirectory();
    }

    /**
     * @return DatabaseTranslationLoader
     */
    public function getDatabaseLoader(): DatabaseTranslationLoader
    {
        return $this->databaseLoader;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogue $messageCatalogue
     *
     * @return MessageCatalogue Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogue $messageCatalogue): MessageCatalogue
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }

    /**
     * @param array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths(array $paths, string $locale, string $pattern = null): MessageCatalogue
    {
        return (new TranslationFinder())->getCatalogueFromPaths($paths, $locale, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return ['#^' . preg_quote($this->domain) . '([A-Z]|$)#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        return ['^' . preg_quote($this->domain) . '([A-Z]|$)'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'external_legacy_module';
    }

    /**
     * {@inheritdoc}
     */
    public function setModuleName(string $moduleName): self
    {
        if (empty($moduleName)) {
            UnsupportedModuleException::moduleNotProvided(self::getIdentifier());
        }

        $this->moduleName = $moduleName;

        // ugly hack, I know
        $this->domain = DomainHelper::buildModuleBaseDomain($moduleName);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory(): string
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->moduleName . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR;
    }

    /**
     * Builds the catalogue including the translated wordings ONLY
     *
     * @return MessageCatalogue
     */
    private function buildTranslationCatalogueFromLegacyFiles(): MessageCatalogue
    {
        // the message catalogue needs to be indexed by original wording, but legacy files are indexed by hash
        // therefore, we need to build the default catalogue (by analyzing source code)
        // then cross reference the wordings found in the default catalogue
        // with the hashes found in the module's legacy translation file.

        $legacyFilesCatalogue = new MessageCatalogue($this->locale);
        $catalogueFromPhpAndSmartyFiles = $this->getDefaultCatalogue(false);

        try {
            $catalogueFromLegacyTranslationFiles = $this->legacyFileLoader->load(
                $this->getDefaultResourceDirectory(),
                $this->locale
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
     * @param MessageCatalogue $catalogue
     *
     * @return MessageCatalogue
     */
    private function filterDomains(MessageCatalogue $catalogue): MessageCatalogue
    {
        $normalizer = new DomainNormalizer();
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        // add delimiter to
        $validTranslationDomains = $this->getFilters();

        foreach ($catalogue->getDomains() as $domain) {
            // remove dots
            $newDomain = $normalizer->normalize($domain);

            // only add if the domain is relevant to this module
            foreach ($validTranslationDomains as $pattern) {
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
     * @return MessageCatalogue
     */
    private function buildFreshDefaultCatalogue(): MessageCatalogue
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);

        try {
            // look up files in the core translations
            $defaultCatalogue = $this->moduleProvider
                ->setModuleName($this->moduleName)
                ->setLocale($this->locale)
                ->getDefaultCatalogue();
        } catch (FileNotFoundException $exception) {
            // there are no xliff files for this module in the core
        }

        try {
            // analyze files and extract wordings
            $additionalDefaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $this->locale);
            $defaultCatalogue = $this->filterDomains($additionalDefaultCatalogue);
        } catch (UnsupportedLocaleException $exception) {
            // Do nothing as support of legacy files is deprecated
        }

        return $defaultCatalogue;
    }

    /**
     * Returns the cached default catalogue
     *
     * @return MessageCatalogue
     */
    private function getCachedDefaultCatalogue(): MessageCatalogue
    {
        $catalogueCacheKey = $this->moduleName . '|' . $this->locale;

        if (!isset($this->defaultCatalogueCache[$catalogueCacheKey])) {
            $this->defaultCatalogueCache[$catalogueCacheKey] = $this->buildFreshDefaultCatalogue();
        }

        return $this->defaultCatalogueCache[$catalogueCacheKey];
    }
}
