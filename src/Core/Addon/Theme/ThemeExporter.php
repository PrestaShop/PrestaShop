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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

        $finalFile = $this->configuration->get('_PS_ALL_THEMES_DIR_') . DIRECTORY_SEPARATOR . $theme->getName() . '.zip';
        $this->createZip($cacheDir, $finalFile);

        $this->fileSystem->remove($cacheDir);

        return realpath($finalFile);
    }

    /**
     * @param string $themeDir
     * @param string $cacheDir
     */
    private function copyTheme($themeDir, $cacheDir)
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
    private function createZip($sourceDir, $destinationFileName)
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
}
