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

namespace PrestaShopBundle\Translation\Exporter;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use PrestaShopBundle\Translation\Provider\Factory\ProviderFactory;
use PrestaShopBundle\Translation\Provider\TranslationFinder;
use PrestaShopBundle\Translation\Provider\Type\ThemesType;
use PrestaShopBundle\Utils\ZipManager;
use PrestaShopException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Exports a theme's translations
 */
class ThemeExporter
{
    /**
     * @var ThemeExtractorInterface the theme extractor
     */
    private $themeExtractor;

    /**
     * @var ZipManager the zip manager
     */
    private $zipManager;

    /**
     * @var ThemeRepository the theme repository
     */
    private $themeRepository;

    /**
     * @var XliffFileDumper the Xliff dumper
     */
    private $dumper;

    /**
     * @var Filesystem the Filesystem
     */
    private $filesystem;

    /**
     * @var string the export directory path
     */
    public $exportDir;
    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    public function __construct(
        ThemeExtractorInterface $themeExtractor,
        ThemeRepository $themeRepository,
        ProviderFactory $providerFactory,
        XliffFileDumper $dumper,
        ZipManager $zipManager,
        Filesystem $filesystem
    ) {
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->dumper = $dumper;
        $this->zipManager = $zipManager;
        $this->filesystem = $filesystem;
        $this->providerFactory = $providerFactory;
    }

    /**
     * Extracts the theme's translations in a particular locale and bundles them in a zip file
     *
     * @param string $themeName Theme name
     * @param string $locale Locale for the exported catalogue
     * @param string|false $rootDir Path to use as root for the translation metadata
     *
     * @return string Full path to the zip file
     */
    public function createZipArchive($themeName, $locale, $rootDir = false)
    {
        $archiveParentDirectory = $this->exportCatalogues($themeName, $locale, $rootDir);
        $zipFilename = $this->makeZipFilename($themeName, $locale);
        $this->zipManager->createArchive($zipFilename, $archiveParentDirectory);

        return $zipFilename;
    }

    /**
     * Extracts the theme's translations in a particular locale as XLIFF files in a temporary directory
     *
     * @param string $themeName Theme name
     * @param string $locale Locale for the exported catalogue
     * @param string|false $rootDir Path to use as root for the translation metadata
     *
     * @return string The directory where the files have been exported
     */
    public function exportCatalogues(string $themeName, string $locale, $rootDir = false): string
    {
        $mergedTranslations = $this->getCatalogueExtractedFromTemplates($themeName, $locale, $rootDir);

        $themeProvider = $this->providerFactory->build(new ThemesType($themeName));
        try {
            $themeCatalogue = $themeProvider->getFileTranslatedCatalogue($locale);
        } catch (FileNotFoundException $exception) {
            // if the theme doesn't have translation files (eg. the default theme)
            $themeCatalogue = new MessageCatalogue($locale);
        }
        $databaseCatalogue = $themeProvider->getUserTranslatedCatalogue($locale);

        $mergedTranslations->addCatalogue($themeCatalogue);
        $mergedTranslations->addCatalogue($databaseCatalogue);

        $this->updateCatalogueMetadata($mergedTranslations);

        $archiveDirectory = $this->getExportDir($themeName);

        if ($this->ensureFileBelongsToExportDirectory($archiveDirectory)) {
            // Clean up previously exported archives
            $this->filesystem->remove($archiveDirectory);
        }

        $this->filesystem->mkdir($archiveDirectory);

        $this->dumper->dump($mergedTranslations, [
            'path' => $archiveDirectory,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
        ]);

        $this->renameCatalogues($locale, $archiveDirectory);

        return $archiveDirectory;
    }

    /**
     * @param string $exportDir
     */
    public function setExportDir(string $exportDir): void
    {
        $this->exportDir = str_replace('/export', DIRECTORY_SEPARATOR . 'export', $exportDir);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function ensureFileBelongsToExportDirectory(string $filePath): bool
    {
        if (!$this->filesystem->exists($filePath)) {
            return false;
        }

        $validFileLocation = substr(realpath($filePath), 0, strlen(realpath($this->exportDir))) === realpath($this->exportDir);

        if (!$validFileLocation) {
            throw new Exception('Invalid file location. This file should belong to the export directory');
        }

        return true;
    }

    /**
     * @param string $themeName
     * @param string $locale
     * @param string|bool $rootDir
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException|PrestaShopException
     */
    protected function getCatalogueExtractedFromTemplates(
        string $themeName,
        string $locale,
        $rootDir = false
    ): MessageCatalogue {
        $theme = $this->themeRepository->getInstanceByName($themeName);

        $storageFilesPath = $this->themeExtractor->getStorageFilesPath($theme);
        $temporaryFilesPath = $this->themeExtractor->getTemporaryFilesPath($theme);

        $this->filesystem->remove($temporaryFilesPath);

        $tmpExtractPath = $temporaryFilesPath . DIRECTORY_SEPARATOR . $locale;

        $this->filesystem->mkdir($tmpExtractPath);

        $catalogue = $this->themeExtractor
            ->extract($theme, $locale, $rootDir);

        $options = [
            'path' => $temporaryFilesPath,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
        ];

        $this->dumper->dump($catalogue, $options);

        Flattenizer::flatten(
            $tmpExtractPath,
            $storageFilesPath . DIRECTORY_SEPARATOR . ThemeExtractorInterface::DEFAULT_LOCALE,
            $locale
        );

        return (new TranslationFinder())->getCatalogueFromPaths($tmpExtractPath, $locale, '*');
    }

    /**
     * @param string $locale
     * @param string $archiveParentDirectory
     */
    protected function renameCatalogues(string $locale, string $archiveParentDirectory): void
    {
        $finder = Finder::create();

        /** @var SplFileInfo $file */
        foreach ($finder->in($archiveParentDirectory . DIRECTORY_SEPARATOR . $locale)->files() as $file) {
            $destinationFilename = preg_replace('#(\.xlf)$#', ".$locale\$1", $file->getPathname());
            if ($this->filesystem->exists($destinationFilename)) {
                $this->filesystem->remove($destinationFilename);
            }
            $this->filesystem->rename($file->getPathname(), $destinationFilename);
        }
    }

    /**
     * @param string $themeName
     */
    public function cleanArtifacts(string $themeName): void
    {
        $theme = $this->themeRepository->getInstanceByName($themeName);

        $this->filesystem->remove($this->themeExtractor->getStorageFilesPath($theme));
        $this->filesystem->remove($this->themeExtractor->getTemporaryFilesPath($theme));
    }

    /**
     * @param string $themeName
     *
     * @return string
     */
    protected function getExportDir(string $themeName): string
    {
        return $this->exportDir . DIRECTORY_SEPARATOR . $themeName;
    }

    /**
     * @param string $themeName
     * @param string $locale
     *
     * @return string
     */
    protected function makeZipFilename(string $themeName, string $locale): string
    {
        if (!file_exists($this->exportDir)) {
            mkdir($this->exportDir);
        }

        $zipFilenameParts = [
            $this->exportDir,
            $themeName,
            $locale,
            $themeName . '.' . $locale . '.zip',
        ];

        return implode(DIRECTORY_SEPARATOR, $zipFilenameParts);
    }

    /**
     * @param MessageCatalogue $catalogue
     */
    protected function updateCatalogueMetadata(MessageCatalogue $catalogue): void
    {
        foreach ($catalogue->all() as $domain => $messages) {
            $this->ensureCatalogueHasRequiredMetadata($catalogue, $messages, $domain);
        }
    }

    /**
     * @param MessageCatalogue $catalogue
     * @param array $messages
     * @param string $domain
     */
    protected function ensureCatalogueHasRequiredMetadata(
        MessageCatalogue $catalogue,
        array $messages,
        string $domain
    ): void {
        foreach (array_keys($messages) as $id) {
            $metadata = $catalogue->getMetadata($id, $domain);
            if ($this->shouldAddFileMetadata($metadata)) {
                $catalogue->setMetadata($id, $this->parseMetadataNotes($metadata), $domain);
            }
        }
    }

    /**
     * @param array|null $metadata
     *
     * @return bool
     */
    protected function metadataContainNotes(array $metadata = null): bool
    {
        return null !== $metadata && array_key_exists('notes', $metadata) && is_array($metadata['notes']) &&
            array_key_exists(0, $metadata['notes']) && is_array($metadata['notes'][0]) &&
            array_key_exists('content', $metadata['notes'][0]);
    }

    /**
     * @param array|null $metadata
     *
     * @return bool
     */
    protected function shouldAddFileMetadata(array $metadata = null): bool
    {
        return null === $metadata || !array_key_exists('file', $metadata);
    }

    /**
     * @param array|null $metadata
     *
     * @return array
     */
    protected function parseMetadataNotes(array $metadata = null): array
    {
        $defaultMetadata = ['file' => '', 'line' => ''];

        if (!$this->metadataContainNotes($metadata)) {
            return $defaultMetadata;
        }

        $notes = $metadata['notes'][0]['content'];
        if (1 !== preg_match('/(?<file>\S+):(?<line>\S+)/m', $notes, $matches)) {
            return $defaultMetadata;
        }

        return [
            'file' => $matches['file'],
            'line' => $matches['line'],
        ];
    }
}
