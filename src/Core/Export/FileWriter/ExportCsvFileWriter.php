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

        $exportFile->fputcsv($this->sanitizeRow($data->getTitles()), $separator, '"', '');

        foreach ($data->getRows() as $row) {
            $exportFile->fputcsv($this->sanitizeRow($row), $separator, '"', '');
        }

        return $exportFile;
    }

    /**
     * Neutralizes spreadsheet formula injection in a row of data, following the
     * same policy as CsvResponse: a value starting with =, +, -, @, a tab or a
     * carriage return is prefixed with a single quote so spreadsheet
     * applications read it as text instead of executing it.
     *
     * @param array $row
     *
     * @return array
     */
    private function sanitizeRow(array $row): array
    {
        return array_map(function ($value) {
            if (null === $value) {
                return '';
            }
            $value = (string) $value;
            if (isset($value[0]) && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                $value = "'" . $value;
            }

            return $value;
        }, $row);
    }
}
