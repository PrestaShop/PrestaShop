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

namespace PrestaShop\PrestaShop\Adapter\Converter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PrestaShop\PrestaShop\Core\File\Converter\FileConverterInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ExcelToCsvFileConverter converts excel files to CSV.
 */
final class ExcelToCsvFileConverter implements FileConverterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $csvDirectory;

    /**
     * @var string
     */
    private $excelDirectory;

    /**
     * @param Filesystem $filesystem
     * @param string $csvDirectory path to CSV files directory
     * @param string $excelDirectory path to excel files directory
     */
    public function __construct(Filesystem $filesystem, $csvDirectory, $excelDirectory)
    {
        $this->filesystem = $filesystem;
        $this->csvDirectory = $csvDirectory;
        $this->excelDirectory = $excelDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(SplFileInfo $sourceFile)
    {
        if (preg_match('#(.*?)\.(csv)#is', $sourceFile->getFilename())) {
            // File is already in CSV
            return $sourceFile;
        }

        if (!$this->filesystem->exists($this->excelDirectory)) {
            $this->filesystem->mkdir($this->excelDirectory);
        }

        $destinationFilename = basename($sourceFile->getFilename(), $sourceFile->getExtension()) . '.csv';
        $destinationFilePath = $this->excelDirectory . $destinationFilename;

        if (!$this->filesystem->exists($destinationFilePath)) {
            $excelReader = IOFactory::createReaderForFile($sourceFile->getFilename());
            $excelReader->setReadDataOnly(true);
            $excelFile = $excelReader->load($sourceFile->getFilename() . $destinationFilename);
            $csvWriter = IOFactory::createWriter($excelFile, 'Csv');
            $csvWriter->setSheetIndex(0);
            $csvWriter->setDelimiter(';');
            $csvWriter->save($destinationFilePath);
        }

        return new SplFileInfo($destinationFilePath);
    }
}
