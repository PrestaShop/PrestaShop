<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    ) {
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
     * @param $rootDir
     * @return string
     */
    public function createZipArchive($themeName, $locale, $rootDir = false)
    {
        $archiveParentDirectory = $this->exportCatalogues($themeName, $locale, $rootDir);
        $zipFilename = $this->makeZipFilename($themeName, $locale);
        $this->zipManager->createArchive($zipFilename, $archiveParentDirectory);

        return $zipFilename;
    }

    /**
     * @param $themeName
     * @param $locale
     * @param $rootDir
     * @return string
     */
    public function exportCatalogues($themeName, $locale, $rootDir = false)
    {
        $this->themeProvider->setLocale($locale);
        $this->themeProvider->setThemeName($themeName);

        $mergedTranslations = $this->getCatalogueExtractedFromTemplates($themeName, $locale, $rootDir);
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

        if (
            $this->filesystem->exists($archiveParentDirectory) &&
            $this->ensureFileBelongsToExportDirectory($archiveParentDirectory)
        ) {
            // Clean up previously exported archives
            $this->filesystem->remove($archiveParentDirectory);
        }

        $this->filesystem->mkdir($archiveParentDirectory);

        $this->dumper->dump($mergedTranslations, array(
            'path' => $archiveParentDirectory,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
        ));

        $this->renameCatalogues($locale, $archiveParentDirectory);

        return $archiveParentDirectory;
    }

    /**
     * @param mixed $exportDir
     */
    public function setExportDir($exportDir)
    {
        $this->exportDir = str_replace('/export', DIRECTORY_SEPARATOR . 'export', $exportDir);
    }

    /**
     * @param $filePath
     * @return bool
     * @throws \Exception
     */
    protected function ensureFileBelongsToExportDirectory($filePath)
    {
        $validFileLocation = substr(realpath($filePath), 0, strlen(realpath($this->exportDir))) === realpath($this->exportDir);

        if (!$validFileLocation) {
            throw new \Exception('Invalid file location. This file should belong to the export directory');
        }

        return $validFileLocation;
    }

    /**
     * @param $themeName
     * @param $locale
     * @param $rootDir
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    protected function getCatalogueExtractedFromTemplates($themeName, $locale, $rootDir = false)
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
            ->extract($theme, $locale, $rootDir);

        Flattenizer::flatten($tmpFolderPath . DIRECTORY_SEPARATOR . $locale, $folderPath . DIRECTORY_SEPARATOR . $locale, $locale);

        return $this->themeProvider->getCatalogueFromPaths($folderPath, $locale, '*');
    }

    /**
     * @param $locale
     * @param $archiveParentDirectory
     */
    protected function renameCatalogues($locale, $archiveParentDirectory)
    {
        $finder = Finder::create();

        foreach ($finder->in($archiveParentDirectory . DIRECTORY_SEPARATOR . $locale)->files() as $file) {
            $parentDirectoryParts = explode(DIRECTORY_SEPARATOR, dirname($file));
            $destinationFilenameParts = array(
                $archiveParentDirectory,
                $parentDirectoryParts[count($parentDirectoryParts) - 1] . '.' . $locale . '.xlf'
            );
            $destinationFilename = implode(DIRECTORY_SEPARATOR, $destinationFilenameParts);
            if ($this->filesystem->exists($destinationFilename)) {
                $this->filesystem->remove($destinationFilename);
            }
            $this->filesystem->rename($file->getPathname(), $destinationFilename);
        }

        $this->filesystem->remove($archiveParentDirectory . DIRECTORY_SEPARATOR . $locale);
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
        return $this->cacheDir . DIRECTORY_SEPARATOR . $themeName . '-tmp';
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getFlattenizationFolder($themeName)
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . $themeName;
    }

    /**
     * @param $themeName
     * @return string
     */
    protected function getExportDir($themeName)
    {
        return $this->exportDir . DIRECTORY_SEPARATOR . $themeName;
    }

    /**
     * @param $themeName
     * @param $locale
     * @return string
     */
    protected function makeZipFilename($themeName, $locale)
    {
        if (!file_exists($this->exportDir)) {
            mkdir($this->exportDir);
        }

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
     * @throws \Exception
     */
    protected function makeArchiveParentDirectory($themeName, $locale)
    {
        $zipFilename = $this->makeZipFilename($themeName, $locale);

        return dirname($zipFilename);
    }

    /**
     * @param MessageCatalogue $catalogue
     */
    protected function updateCatalogueMetadata(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->all() as $domain => $messages) {
            $this->ensureCatalogueHasRequiredMetadata($catalogue, $messages, $domain);
        }
    }

    /**
     * @param MessageCatalogue $catalogue
     * @param $messages
     * @param $domain
     */
    protected function ensureCatalogueHasRequiredMetadata(
        MessageCatalogue $catalogue,
        $messages,
        $domain
    ) {
        foreach ($messages as $id => $translation) {
            $metadata = $catalogue->getMetadata($id, $domain);
            if ($this->shouldAddFileMetadata($metadata)) {
                $catalogue->setMetadata($id, $this->parseMetadataNotes($metadata), $domain);
            }
        }
    }

    /**
     * @param array $metadata
     * @return bool
     */
    protected function metadataContainNotes(array $metadata = null)
    {
        return !is_null($metadata) && array_key_exists('notes', $metadata) && is_array($metadata['notes']) &&
            array_key_exists(0, $metadata['notes']) && is_array($metadata['notes'][0]) &&
            array_key_exists('content', $metadata['notes'][0])
        ;
    }

    /**
     * @param $metadata
     * @return bool
     */
    protected function shouldAddFileMetadata($metadata)
    {
        return is_null($metadata) || !array_key_exists('file', $metadata);
    }

    /**
     * @param $locale
     * @param MessageCatalogue $sourceCatalogue
     * @return MessageCatalogue
     */
    protected function addLocaleToDomain($locale, MessageCatalogue $sourceCatalogue)
    {
        $catalogue = new MessageCatalogue($locale, array());
        foreach ($sourceCatalogue->all() as $domain => $messages) {
            $catalogue->add($messages, $domain . '.' . $locale);
        }

        return $catalogue;
    }

    /**
     * @param array|null $metadata
     * @return array
     */
    protected function parseMetadataNotes(array $metadata = null)
    {
        $defaultMetadata = array('file' => '', 'line' => '');

        if (!$this->metadataContainNotes($metadata)) {
            return $defaultMetadata;
        }

        $notes = $metadata['notes'][0]['content'];
        if (1 !== preg_match('/(?<file>\S+):(?<line>\S+)/m', $notes, $matches)) {
            return $defaultMetadata;
        }

        return array(
            'file' => $matches['file'],
            'line' => $matches['line'],
        );
    }
}
