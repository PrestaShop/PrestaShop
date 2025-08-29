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

namespace PrestaShop\PrestaShop\Core\Export\FileWriter;

use Exception;
use PrestaShop\PrestaShop\Core\Export\Data\ExportableDataInterface;
use PrestaShop\PrestaShop\Core\Export\Exception\FileWritingException;
use PrestaShop\PrestaShop\Core\Export\ExportDirectory;
use SplFileInfo;
use SplFileObject;

/**
 * Class ExportCsvFileWriter writes provided data into CSV file and saves it in export directory.
 */
final class ExportCsvFileWriter implements FileWriterInterface
{
    private ExportDirectory $exportDirectory;

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
    public function write(string $fileName, ExportableDataInterface $data, $separator = ';'): SplFileInfo|SplFileObject
    {
        $filePath = $this->exportDirectory . $fileName;

        try {
            $exportFile = new SplFileObject($filePath, 'w');
        } catch (Exception) {
            throw new FileWritingException(
                'Cannot open export file for writing',
                FileWritingException::CANNOT_OPEN_FILE_FOR_WRITING
            );
        }

        $exportFile->fputcsv($data->getTitles(), $separator, '"', '');

        foreach ($data->getRows() as $row) {
            $exportFile->fputcsv($row, $separator, '"', '');
        }

        return $exportFile;
    }
}
