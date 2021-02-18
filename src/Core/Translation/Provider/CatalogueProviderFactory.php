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

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\AbstractCoreProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\ThemeProviderDefinition;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * This factory will return the provider matching the given 'type'.
 * If the type given doesn't match one of the known types, an exception will be thrown.
 */
class CatalogueProviderFactory
{
    /**
     * @var CatalogueLayersProviderInterface[]
     */
    private $providers = [];
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;
    /**
     * @var string
     */
    private $resourceDirectory;
    /**
     * @var LegacyModuleExtractorInterface
     */
    private $legacyModuleExtractor;
    /**
     * @var LoaderInterface
     */
    private $legacyFileLoader;
    /**
     * @var string
     */
    private $modulesDirectory;
    /**
     * @var string
     */
    private $translationsDirectory;
    /**
     * @var ThemeExtractor
     */
    private $themeExtractor;
    /**
     * @var ThemeRepository
     */
    private $themeRepository;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $themesDirectory;

    /**
     * @TODO We keep for now the dependency to PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader
     *   We will create, in the core, a TranslationRepositoryInterface and inject to DatabaseTransloader the LangRepositoryInterface and TranslationRepositoryInterface as dependencies
     *   to be fully independent from PrestaShopBundle
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        LoaderInterface $legacyFileLoader,
        ThemeExtractor $themeExtractor,
        ThemeRepository $themeRepository,
        Filesystem $filesystem,
        string $themesDirectory,
        string $modulesDirectory,
        string $translationsDirectory,
        string $resourceDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themesDirectory = $themesDirectory;
    }

    /**
     * @param ProviderDefinitionInterface $providerDefinition
     *
     * @return CatalogueLayersProviderInterface
     *
     * @throws UnexpectedTranslationTypeException
     */
    public function getProvider(ProviderDefinitionInterface $providerDefinition): CatalogueLayersProviderInterface
    {
        $type = $providerDefinition->getType();
        if (!in_array($type, ProviderDefinitionInterface::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException(sprintf('Unexpected type %s', $type));
        }

        if ($providerDefinition instanceof ModuleProviderDefinition) {
            return $this->getModuleCatalogueProvider($providerDefinition);
        } elseif ($providerDefinition instanceof AbstractCoreProviderDefinition) {
            return $this->getCoreCatalogueProvider($providerDefinition);
        } elseif ($providerDefinition instanceof ThemeProviderDefinition) {
            return $this->getThemeCatalogueProvider($providerDefinition);
        }

        // This should never be thrown if every Type has his Provider defined in constructor
        throw new UnexpectedTranslationTypeException(sprintf('Unexpected type %s', $type));
    }

    private function getCoreCatalogueProvider(ProviderDefinitionInterface $providerDefinition): CatalogueLayersProviderInterface
    {
        if (!array_key_exists($providerDefinition->getType(), $this->providers)) {
            $this->providers[$providerDefinition->getType()] = new CoreCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->resourceDirectory,
                $providerDefinition->getFilenameFilters(),
                $providerDefinition->getTranslationDomains()
            );
        }

        return $this->providers[$providerDefinition->getType()];
    }

    private function getModuleCatalogueProvider(ModuleProviderDefinition $providerDefinition): CatalogueLayersProviderInterface
    {
        if (!array_key_exists($providerDefinition->getType(), $this->providers)) {
            $this->providers[$providerDefinition->getType()] = new ModuleCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->legacyModuleExtractor,
                $this->legacyFileLoader,
                $this->modulesDirectory,
                $this->translationsDirectory,
                $this->resourceDirectory,
                $providerDefinition->getModuleName(),
                $providerDefinition->getFilenameFilters(),
                $providerDefinition->getTranslationDomains()
            );
        }

        return $this->providers[$providerDefinition->getType()];
    }

    private function getThemeCatalogueProvider(ThemeProviderDefinition $providerDefinition): CatalogueLayersProviderInterface
    {
        if (!array_key_exists($providerDefinition->getType(), $this->providers)) {
            $coreFrontProviderDefinition = new FrontofficeProviderDefinition();
            $coreFrontProvider = new CoreCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->resourceDirectory,
                $coreFrontProviderDefinition->getFilenameFilters(),
                $coreFrontProviderDefinition->getTranslationDomains()
            );

            $this->providers[$providerDefinition->getType()] = new ThemeCatalogueLayersProvider(
                $coreFrontProvider,
                $this->databaseTranslationLoader,
                $this->themeExtractor,
                $this->themeRepository,
                $this->filesystem,
                $this->themesDirectory,
                $providerDefinition->getThemeName()
            );
        }

        return $this->providers[$providerDefinition->getType()];
    }
}
