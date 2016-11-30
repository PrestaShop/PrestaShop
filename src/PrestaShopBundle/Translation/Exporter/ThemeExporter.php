<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Exporter;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use PrestaShopBundle\Utils\ZipManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeExporter
{
    private $themeExtractor;

    private $themeProvider;

    private $zipManager;

    private $themeRepository;

    private $dumper;

    private $filesystem;

    public $cacheDir;

    public $exportDir;

    public function __construct(
        ThemeExtractor $themeExtractor,
        ThemeProvider $themeProvider,
        ThemeRepository $themeRepository,
        XliffFileDumper $dumper,
        ZipManager $zipManager,
        Filesystem $filesystem
    )
    {
        $this->themeExtractor = $themeExtractor;
        $this->themeExtractor
            ->setThemeProvider($themeProvider);

        $this->themeProvider = $themeProvider;
        $this->themeRepository = $themeRepository;
        $this->dumper = $dumper;
        $this->zipManager = $zipManager;
        $this->filesystem = $filesystem;
    }

    /**
     * @param $themeName
     * @param $locale
     * @return string
     */
    public function createZipArchive($themeName, $locale)
    {
        $archiveParentDirectory = $this->exportCatalogues($themeName, $locale);
        $zipFilename = $this->makeZipFilename($themeName, $locale);
        $this->zipManager->createArchive($zipFilename, $archiveParentDirectory);

        return $zipFilename;
    }

    /**
     * @param $themeName
     * @param $locale
     * @return string
     */
    public function exportCatalogues($themeName, $locale)
    {
        $this->themeProvider->setLocale($locale);
        $this->themeProvider->setThemeName($themeName);

        $mergedTranslations = $this->getCatalogueExtractedFromTemplates($themeName, $locale);
        try {
            $themeCatalogue = $this->themeProvider->getThemeCatalogue();
        } catch (\Exception $exception) {
            $themeCatalogue = new MessageCatalogue($locale, array());
        }
        $databaseCatalogue = $this->themeProvider->getDatabaseCatalogue($themeName);
        $databaseCatalogue = $this->addLocaleToDomain($locale, $databaseCatalogue);

        $mergedTranslations->addCatalogue($themeCatalogue);
        $mergedTranslations->addCatalogue($databaseCatalogue);

        $this->updateCatalogueMetadata($mergedTranslations);

        $archiveParentDirectory = $this->makeArchiveParentDirectory($themeName, $locale);

        // Clean up previously exported archives
        $this->filesystem->remove($archiveParentDirectory);
        $this->filesystem->mkdir($archiveParentDirectory);

        $this->dumper->dump($mergedTranslations, array(
            'path' => $archiveParentDirectory,
            'default_locale' => $locale
        ));

        $this->renameCatalogues($locale, $archiveParentDirectory);

        return $archiveParentDirectory;
    }

    /**
     * @param $themeName
     * @param $locale
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    protected function getCatalogueExtractedFromTemplates($themeName, $locale)
    {
        $tmpFolderPath = $this->getTemporaryExtractionFolder($themeName);
        $folderPath = $this->getFlattenizationFolder($themeName);

        $this->filesystem->remove($folderPath);
        $this->filesystem->remove($tmpFolderPath);

        $this->filesystem->mkdir($folderPath);
        $this->filesystem->mkdir($tmpFolderPath);

        $theme = $this->themeRepository->getInstanceByName($themeName);
        $this->themeExtractor
            ->setOutputPath($tmpFolderPath)
            ->extract($theme, $locale);

        Flattenizer::flatten($tmpFolderPath . '/' . $locale, $folderPath . '/' . $locale, $locale);

        return $this->themeProvider->getCatalogueFromPaths($folderPath, $locale, '*');
    }

    /**
     * @param $locale
     * @param $archiveParentDirectory
     */
    protected function renameCatalogues($locale, $archiveParentDirectory)
    {
        $finder = Finder::create();

        foreach ($finder->in($archiveParentDirectory . '/' . $locale)->files() as $file) {
            $parentDirectoryParts = explode('/', dirname($file));
            $destinationFilenameParts = array(
                $archiveParentDirectory,
                $parentDirectoryParts[count($parentDirectoryParts) - 1] . '.' . $locale . '.xlf'
            );
            $destinationFilename = implode('/', $destinationFilenameParts);
            if ($this->filesystem->exists($destinationFilename)) {
                $this->filesystem->remove($destinationFilename);
            }
            $this->filesystem->rename($file->getPathname(), $destinationFilename);
        }

        $this->filesystem->remove($archiveParentDirectory . '/' . $locale);
    }

    public function cleanArtifacts($themeName)
    {
        $this->filesystem->remove($this->getFlattenizationFolder($themeName));
        $this->filesystem->remove($this->getTemporaryExtractionFolder($themeName));
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getTemporaryExtractionFolder($themeName)
    {
        return $this->cacheDir . '/' . $themeName . '-tmp';
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getFlattenizationFolder($themeName)
    {
        return $this->cacheDir . '/' . $themeName;
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getExportDir($themeName)
    {
        return $this->exportDir . '/' . $themeName;
    }

    /**
     * @param $themeName
     * @param $locale
     * @return string
     */
    protected function makeZipFilename($themeName, $locale)
    {
        $zipFilenameParts = array(
            $this->exportDir,
            $themeName,
            $locale,
            $themeName . '.' . $locale . '.zip'
        );

        return implode(DIRECTORY_SEPARATOR, $zipFilenameParts);
    }

    /**
     * @param $themeName
     * @param $locale
     * @return string
     */
    protected function makeArchiveParentDirectory($themeName, $locale)
    {
        $zipFilename = $this->makeZipFilename($themeName, $locale);

        return dirname($zipFilename);
    }

    /**
     * @param MessageCatalogue $mergedTranslations
     */
    protected function updateCatalogueMetadata(MessageCatalogue $mergedTranslations)
    {
        foreach ($mergedTranslations->all() as $domain => $messages) {
            foreach ($messages as $translationKey => $translationValue) {
                $metadata = $mergedTranslations->getMetadata($translationKey, $domain);
                if (is_null($metadata) || !array_key_exists('file', $metadata)) {
                    $mergedTranslations->setMetadata($translationKey, array('line' => '', 'file' => ''), $domain);
                }
            }
        }
    }

    /**
     * @param $locale
     * @param MessageCatalogue $databaseCatalogue
     * @return MessageCatalogue
     */
    protected function addLocaleToDomain($locale, MessageCatalogue $databaseCatalogue)
    {
        $catalogue = new MessageCatalogue($locale, array());
        foreach ($databaseCatalogue->all() as $domain => $messages) {
            $catalogue->add($messages, $domain . '.' . $locale);
        }

        return $catalogue;
    }
}
