<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ThemeExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\AbstractCoreProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModulesProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
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
     * @var ModuleCatalogueProviderFactory
     */
    private $moduleCatalogueProviderFactory;

    /**
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param LegacyModuleExtractorInterface $legacyModuleExtractor
     * @param LoaderInterface $legacyFileLoader
     * @param ThemeExtractor $themeExtractor
     * @param ThemeRepository $themeRepository
     * @param Filesystem $filesystem
     * @param string $themesDirectory
     * @param string $modulesDirectory
     * @param string $translationsDirectory
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
        string $translationsDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themesDirectory = $themesDirectory;
        $this->moduleCatalogueProviderFactory = new ModuleCatalogueProviderFactory(
            $this->databaseTranslationLoader,
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDirectory,
            $this->translationsDirectory
        );
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
            return $this->moduleCatalogueProviderFactory->getModuleCatalogueProvider($providerDefinition);
        } elseif ($providerDefinition instanceof AbstractCoreProviderDefinition) {
            return $this->getCoreCatalogueProvider($providerDefinition);
        } elseif ($providerDefinition instanceof ThemeProviderDefinition) {
            return $this->getThemeCatalogueProvider($providerDefinition);
        }

        // This should never be thrown if every Type has his Provider defined in constructor
        throw new UnexpectedTranslationTypeException(sprintf('Could not fetch provider for given definition type "%s"', $type));
    }

    /**
     * @param ProviderDefinitionInterface $providerDefinition
     *
     * @return CatalogueLayersProviderInterface
     */
    private function getCoreCatalogueProvider(ProviderDefinitionInterface $providerDefinition): CatalogueLayersProviderInterface
    {
        if (!isset($this->providers[$providerDefinition->getType()])) {
            $this->providers[$providerDefinition->getType()] = new CoreCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->translationsDirectory,
                $providerDefinition->getFilenameFilters(),
                $providerDefinition->getTranslationDomains()
            );
        }

        return $this->providers[$providerDefinition->getType()];
    }

    /**
     * @param ThemeProviderDefinition $providerDefinition
     *
     * @return CatalogueLayersProviderInterface
     */
    private function getThemeCatalogueProvider(ThemeProviderDefinition $providerDefinition): CatalogueLayersProviderInterface
    {
        if (!isset($this->providers[$providerDefinition->getType()])) {
            $coreFrontProviderDefinition = new FrontofficeProviderDefinition();
            $modulesProviderDefinition = new ModulesProviderDefinition();
            $coreFrontProvider = new CoreCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->translationsDirectory,
                array_merge(
                    $coreFrontProviderDefinition->getFilenameFilters(),
                    $modulesProviderDefinition->getFilenameFilters()
                ),
                array_merge(
                    $coreFrontProviderDefinition->getTranslationDomains(),
                    $modulesProviderDefinition->getTranslationDomains()
                )
            );

            $this->providers[$providerDefinition->getType()] = new ThemeCatalogueLayersProvider(
                $this->moduleCatalogueProviderFactory,
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
