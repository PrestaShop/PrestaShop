<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRow;
use SplFileInfo;

/**
 * Class CsvFileReader defines a CSV file reader.
 */
final class CsvFileReader implements FileReaderInterface
{
    /**
     * @var string the data delimiter in the CSV row
     */
    private $delimiter;

    /**
     * @var int
     */
    private $length;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    /**
     * @var FileOpenerInterface
     */
    private $fileOpener;

    /**
     * @param FileOpenerInterface $fileOpener
     * @param string $delimiter
     * @param int $length
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(
        FileOpenerInterface $fileOpener,
        $delimiter = ';',
        $length = 0,
        $enclosure = '"',
        $escape = '\\'
    ) {
        $this->delimiter = $delimiter;
        $this->length = $length;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->fileOpener = $fileOpener;
    }

    /**
     * {@inheritdoc}
     */
    public function read(SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw new UnreadableFileException();
        }

        $convertToUtf8 = !mb_check_encoding(file_get_contents($file), 'UTF-8');
        $handle = $this->fileOpener->open($file);

        while ($row = fgetcsv($handle, $this->length, $this->delimiter, $this->enclosure, $this->escape)) {
            if ($convertToUtf8) {
                $row = array_map('utf8_encode', $row);
            }

            yield DataRow::createFromArray($row);
        }

        fclose($handle);
    }
}
