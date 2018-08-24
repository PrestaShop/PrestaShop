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

namespace PrestaShop\PrestaShop\Adapter\Converter;

use PHPExcel_IOFactory;
use PrestaShop\PrestaShop\Core\File\Converter\FileConverterInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ExcelToCsvFileConverter defines an excel to CSV file converter
 */
class ExcelToCsvFileConverter implements FileConverterInterface
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

        $destinationFilename = basename($sourceFile->getFilename(), $sourceFile->getExtension()).'.csv';
        $destinationFilePath = $this->excelDirectory.$destinationFilename;

        if (!$this->filesystem->exists($destinationFilePath)) {
            $excelReader = PHPExcel_IOFactory::createReaderForFile($sourceFile->getFilename());
            $excelReader->setReadDataOnly(true);
            $excelFile = $excelReader->load($sourceFile->getFilename().$destinationFilename);
            $csvWriter = PHPExcel_IOFactory::createWriter($excelFile, 'CSV');
            $csvWriter->setSheetIndex(0);
            $csvWriter->setDelimiter(';');
            $csvWriter->save($destinationFilePath);
        }

        return new SplFileInfo($destinationFilePath);
    }
}
