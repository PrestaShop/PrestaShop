<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Language\Export;

use PrestaShop\PrestaShop\Adapter\Archive\TarArchive;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Archive\ArchiveConfig;
use PrestaShop\PrestaShop\Core\Archive\ArchiveInterface;
use PrestaShop\PrestaShop\Core\Export\Config\FileExporterConfig;
use PrestaShop\PrestaShop\Core\Export\FileExporterInterface;
use PrestaShop\PrestaShop\Core\Language\Export\Config\LanguageExporterConfigInterface;

/**
 * Class LanguageExporter is responsible for exporting language data
 */
final class LanguageExporter implements LanguageExporterInterface
{
    /**
     * @var FileExporterInterface
     */
    private $fileExporter;

    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;
    /**
     * @var TarArchive
     */
    private $archive;

    /**
     * LanguageExporter constructor.
     *
     * @param FileExporterInterface $fileExporter
     * @param LanguageDataProvider $languageDataProvider
     * @param ArchiveInterface $tarArchive
     */
    public function __construct(
        FileExporterInterface $fileExporter,
        LanguageDataProvider $languageDataProvider,
        ArchiveInterface $tarArchive
    ) {
        $this->languageDataProvider = $languageDataProvider;
        $this->fileExporter = $fileExporter;
        $this->archive = $tarArchive;
    }

    /**
     * @inheritDoc
     */
    public function export(LanguageExporterConfigInterface $config)
    {
        $isoCode = $config->getIsoCode();
        $archiveName = $this->getArchiveName($isoCode);

        $filesList = $this->languageDataProvider->getFilesList(
            $isoCode,
            $config->getThemeName(),
            $isoTo = false,
            $themeTo = false,
            $select = false,
            $check = false,
            $modules = true
        );

        $archiveConfig = new ArchiveConfig($archiveName, $filesList);

        if ($this->archive->create($archiveConfig)) {
            $exporterConfig = new FileExporterConfig(
                $archiveName,
                $isoCode.'.gzip',
                true
            );

            $this->fileExporter->export($exporterConfig);
        }
    }

    /**
     * Gets an archive name which is being created during the time of export execution
     *
     * @param string $isoCode
     *
     * @return string
     */
    private function getArchiveName($isoCode)
    {
        return _PS_TRANSLATIONS_DIR_.'/export/'.$isoCode.'.gzip';
    }
}
