<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Translation\Exporter\ThemeExporter as TranslationsExporter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class ThemeExporter
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var Filesystem
     */
    protected $fileSystem;
    /**
     * @var LangRepository
     */
    protected $langRepository;
    /**
     * @var TranslationsExporter
     */
    protected $translationsExporter;

    public function __construct(
        ConfigurationInterface $configuration,
        Filesystem $fileSystem,
        LangRepository $langRepository,
        TranslationsExporter $translationsExporter
    ) {
        $this->configuration = $configuration;
        $this->fileSystem = $fileSystem;
        $this->langRepository = $langRepository;
        $this->translationsExporter = $translationsExporter;
    }

    /**
     * @param Theme $theme
     *
     * @return false|string
     */
    public function export(Theme $theme)
    {
        $cacheDir = $this->configuration->get('_PS_CACHE_DIR_') . 'export-' . $theme->getName() . '-' . time() . DIRECTORY_SEPARATOR;

        $this->copyTheme($theme->getDirectory(), $cacheDir);
        $this->copyModuleDependencies((array) $theme->get('dependencies.modules'), $cacheDir);
        $this->copyTranslations($theme, $cacheDir);
        $this->protectDirectory($cacheDir);

        $finalFile = $this->configuration->get('_PS_ALL_THEMES_DIR_') . DIRECTORY_SEPARATOR . $theme->getName() . '.zip';
        $this->createZip($cacheDir, $finalFile);

        $this->fileSystem->remove($cacheDir);

        return realpath($finalFile);
    }

    /**
     * @param string $themeDir
     * @param string $cacheDir
     */
    private function copyTheme(string $themeDir, string $cacheDir): void
    {
        $fileList = Finder::create()
            ->files()
            ->in($themeDir)
            ->exclude(['node_modules']);

        $this->fileSystem->mirror($themeDir, $cacheDir, $fileList);
    }

    /**
     * @param array $moduleList
     * @param string $cacheDir
     */
    private function copyModuleDependencies(array $moduleList, $cacheDir)
    {
        if (empty($moduleList)) {
            return;
        }

        $dependencyDir = $cacheDir . '/dependencies/modules/';
        $this->fileSystem->mkdir($dependencyDir);
        $moduleDir = $this->configuration->get('_PS_MODULE_DIR_');

        foreach ($moduleList as $moduleName) {
            $this->fileSystem->mirror($moduleDir . $moduleName, $dependencyDir . $moduleName);
        }
    }

    /**
     * @param Theme $theme
     * @param string $cacheDir
     */
    protected function copyTranslations(Theme $theme, $cacheDir)
    {
        $translationsDir = $cacheDir . 'translations';

        $this->fileSystem->remove($translationsDir);
        $this->fileSystem->mkdir($translationsDir);

        $languages = $this->langRepository->findAll();
        if (empty($languages)) {
            return;
        }
        $catalogueDir = '';
        foreach ($languages as $lang) {
            $locale = $lang->getLocale();
            $catalogueDir = $this->translationsExporter->exportCatalogues($theme->getName(), $locale);
        }

        $catalogueDirParts = explode(DIRECTORY_SEPARATOR, $catalogueDir);
        array_pop($catalogueDirParts); // Remove locale

        $cataloguesDir = implode(DIRECTORY_SEPARATOR, $catalogueDirParts);
        $this->fileSystem->mirror($cataloguesDir, $translationsDir);
    }

    /**
     * @param string $sourceDir
     * @param string $destinationFileName
     *
     * @return bool
     */
    private function createZip(string $sourceDir, string $destinationFileName): bool
    {
        $zip = new ZipArchive();
        $zip->open($destinationFileName, ZipArchive::CREATE);

        $files = Finder::create()
            ->files()
            ->in($sourceDir)
            ->exclude(['node_modules']);

        foreach ($files as $file) {
            $zip->addFile($file->getRealpath(), $file->getRelativePathName());
        }

        return $zip->close();
    }

    protected function protectDirectory(string $cacheDir): void
    {
        $dirs = Finder::create()
            ->directories()
            ->in($cacheDir)
            ->exclude(['node_modules']);

        $fs = new Filesystem();
        foreach ($dirs as $dir) {
            // Copy file
            $fs->copy(
                $this->configuration->get('_PS_ALL_THEMES_DIR_') . DIRECTORY_SEPARATOR . 'index.php',
                $dir->getRealPath() . DIRECTORY_SEPARATOR . 'index.php'
            );
        }
    }
}
