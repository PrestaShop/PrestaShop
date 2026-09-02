<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
