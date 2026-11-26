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

        // SplFileObject::fputcsv() is deprecated since PHP 8.6, each line is formatted by fputcsv() in a buffer instead
        $lineBuffer = fopen('php://memory', 'w+');
        $this->writeLine($exportFile, $lineBuffer, $data->getTitles(), $separator);

        foreach ($data->getRows() as $row) {
            $this->writeLine($exportFile, $lineBuffer, $row, $separator);
        }

        fclose($lineBuffer);

        return $exportFile;
    }

    /**
     * @param resource $lineBuffer
     */
    private function writeLine(SplFileObject $exportFile, $lineBuffer, array $fields, string $separator): void
    {
        ftruncate($lineBuffer, 0);
        rewind($lineBuffer);
        fputcsv($lineBuffer, $fields, $separator, '"', '');
        rewind($lineBuffer);
        $exportFile->fwrite(stream_get_contents($lineBuffer));
    }
}
