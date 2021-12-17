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

namespace PrestaShop\PrestaShop\Core\Translation\Export;

use Exception;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationCatalogueBuilder;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShopBundle\Utils\ZipManager;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * This class is responsible of building translation catalogues exports.
 * It uses TranslationCatalogueBuilder to get the catalogues content, dump them in files and return them to caller in the desired format.
 */
class TranslationCatalogueExporter
{
    private const EXPORT_ZIP_FILENAME = '%s/translations_export_%s.zip';

    /**
     * @var TranslationCatalogueBuilder
     */
    private $translationCatalogueBuilder;
    /**
     * @var ProviderDefinitionFactory
     */
    private $providerDefinitionFactory;
    /**
     * @var XliffFileDumper
     */
    private $dumper;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $exportDir;
    /**
     * @var ZipManager
     */
    private $zipManager;

    public function __construct(
        TranslationCatalogueBuilder $translationCatalogueBuilder,
        ProviderDefinitionFactory $providerDefinitionFactory,
        XliffFileDumper $dumper,
        Filesystem $filesystem,
        ZipManager $zipManager,
        string $exportDir
    ) {
        $this->translationCatalogueBuilder = $translationCatalogueBuilder;
        $this->providerDefinitionFactory = $providerDefinitionFactory;
        $this->dumper = $dumper;
        $this->filesystem = $filesystem;
        $this->exportDir = $exportDir;
        $this->zipManager = $zipManager;
    }

    /**
     * Exports the translation catalogues depending on the selections provided in the given locale.
     * Catalogues are written in XLF files and stored in a folder with the locale name.
     * This folder is then compressed in a zip file. The returned value is the path of the created zip file.
     *
     * The expected format for $selections parameter is
     * [
     *   [
     *     'type' => string, // Translation type, allowed in ProviderDefinitionInterface::ALLOWED_EXPORT_TYPES
     *     'selected' => string|null // Must be defined if translation type is themes or modules, otherwise NULL
     *   ],
     *   ...
     * ]
     *
     * @param array $selections
     * @param string $locale
     *
     * @return string The zip file with catalogues exported
     *
     * @throws TranslationFilesNotFoundException
     * @throws UnexpectedTranslationTypeException
     * @throws IOException
     */
    public function export(array $selections, string $locale): string
    {
        $this->validateParameters($selections);

        if (!$this->filesystem->exists($this->exportDir)) {
            $this->filesystem->mkdir($this->exportDir);
        }

        $zipFilename = sprintf(self::EXPORT_ZIP_FILENAME, $this->exportDir, $locale);
        $path = dirname($zipFilename);

        // Clean export folder
        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);
        $dumpOptions = [
            'path' => $path,
            'default_locale' => $locale,
            'root_dir' => _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
        ];

        foreach ($selections as $selection) {
            $providerDefinition = $this->providerDefinitionFactory->build($selection['type'], $selection['selected']);

            // Get the catalogue Map
            $catalogue = $this->translationCatalogueBuilder->getRawCatalogue($providerDefinition, $locale, []);

            // Transform into messageCatalogue object
            $messageCatalogue = $this->transformCatalogueMapToMessageCatalogue($catalogue, $locale);

            // Dump catalogue into XLF files
            $this->dumper->dump($messageCatalogue, $dumpOptions);
        }

        // Rename files to add locale in it
        $this->renameCatalogues($locale, $path);

        $this->zipManager->createArchive($zipFilename, $path);

        return $zipFilename;
    }

    /**
     * @param array $selections Translation types to export
     *
     * @throws UnexpectedTranslationTypeException
     * @throws Exception
     */
    private function validateParameters(
        array $selections
    ): void {
        foreach ($selections as $selection) {
            if (!in_array($selection['type'], ProviderDefinitionInterface::ALLOWED_EXPORT_TYPES, true)) {
                throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
            }

            if (
                null === $selection['selected']
                && (in_array($selection['type'], [ProviderDefinitionInterface::TYPE_MODULES, ProviderDefinitionInterface::TYPE_THEMES], true))
            ) {
                throw new Exception(sprintf('Selected value cannot be null for type %s.', $selection['type']));
            }
        }
    }

    private function transformCatalogueMapToMessageCatalogue(Catalogue $catalogue, string $locale): MessageCatalogue
    {
        $messageCatalogue = new MessageCatalogue($locale);
        foreach ($catalogue->getDomains() as $domain) {
            $domainName = $domain->getDomainName();
            foreach ($domain->getMessages() as $message) {
                $messageCatalogue->set($message->getKey(), $message->getTranslation(), $domainName);
                $messageCatalogue->setMetadata($message->getKey(), ['file' => '', 'line' => ''], $domainName);
            }
        }

        return $messageCatalogue;
    }

    protected function renameCatalogues(string $locale, string $path): void
    {
        $finder = Finder::create();

        foreach ($finder->in($path . DIRECTORY_SEPARATOR . $locale)->files() as $file) {
            $filenameParts = explode('.', $file->getFilename());
            unset($filenameParts[count($filenameParts) - 1]); // Remove the extension
            /*
             * The destination file name will have the format
             * DIRECTORY/ab-AB/FullDomainName.ab-AB.xlf
             */
            $destinationFilename = sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%s' . DIRECTORY_SEPARATOR . '%s.%s.%s',
                $path,
                $locale,
                implode('.', $filenameParts),
                $locale,
                $file->getExtension()
            );
            if ($this->filesystem->exists($destinationFilename)) {
                $this->filesystem->remove($destinationFilename);
            }
            $this->filesystem->rename($file->getPathname(), $destinationFilename);
        }
    }
}
