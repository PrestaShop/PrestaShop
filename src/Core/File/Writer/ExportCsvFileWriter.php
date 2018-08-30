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

namespace PrestaShop\PrestaShop\Core\File\Writer;

use Exception;
use PrestaShop\PrestaShop\Core\Export\ExportDirectory;
use PrestaShop\PrestaShop\Core\File\Writer\Exception\FileWritingException;
use SplFileObject;

/**
 * Class CsvFileWriter writes provided data into CSV file and saves it in export directory
 */
final class ExportCsvFileWriter implements FileWriterInterface
{
    /**
     * @var ExportDirectory
     */
    private $exportDirectory;

    /**
     * @param ExportDirectory $exportDirectory
     */
    public function __construct(ExportDirectory $exportDirectory)
    {
        $this->exportDirectory = $exportDirectory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileWritingException
     */
    public function write(
        FileWriterDataInterface $data,
        FileWriterConfigurationInterface $config
    ) {
        $fileName = sprintf('%s.csv', $config->getFileName());
        $filePath = $this->exportDirectory.$fileName;

        try {
            $exportFile = new SplFileObject($filePath, 'w');
        } catch (Exception $e) {
            throw new FileWritingException(
                sprintf('Cannot open export file for writing'),
                FileWritingException::CANNOT_OPEN_FILE_FOR_WRITING
            );
        }

        $exportFile->fputcsv($data->getHeaders(), ';');

        foreach ($data->getRows() as $row) {
            $exportFile->fputcsv($row, ';');
        }

        return $exportFile;
    }
}
