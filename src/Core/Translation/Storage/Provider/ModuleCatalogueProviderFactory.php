<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use Symfony\Component\Translation\Loader\LoaderInterface;

class ModuleCatalogueProviderFactory
{
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

    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        LoaderInterface $legacyFileLoader,
        string $modulesDirectory,
        string $translationsDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
    }

    public function getModuleCatalogueProvider(ModuleProviderDefinition $providerDefinition): CatalogueLayersProviderInterface
    {
        return new ModuleCatalogueLayersProvider(
            $this->databaseTranslationLoader,
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDirectory,
            $this->translationsDirectory,
            $providerDefinition->getModuleName(),
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );
    }
}
