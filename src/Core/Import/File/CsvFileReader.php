<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use Generator;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\InvalidResumeCursorException;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRow;
use SplFileInfo;

/**
 * Class CsvFileReader defines a CSV file reader.
 */
final class CsvFileReader implements FileReaderInterface, ResumableFileReaderInterface
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
     *
     * @deprecated since 9.3, kept for the legacy import preview path only —
     *             use readFrom() on working files produced by
     *             CsvImportFileNormalizer instead
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
                $row = array_map(
                    static function (?string $cell): string {
                        return mb_convert_encoding((string) $cell, 'UTF-8', 'ISO-8859-1');
                    },
                    $row
                );
            }

            yield DataRow::createFromArray($row);
        }

        fclose($handle);
    }

    /**
     * {@inheritdoc}
     *
     * The cursor is a byte offset into the file (fseek, O(1) resume). This
     * method is meant for working files produced by CsvImportFileNormalizer:
     * it always reads the canonical CSV dialect and performs no encoding
     * detection (normalized files are UTF-8 without BOM by construction).
     *
     * @throws UnreadableFileException
     * @throws InvalidResumeCursorException
     */
    public function readFrom(SplFileInfo $file, ?string $cursor = null): Generator
    {
        $handle = $this->openForEngineRead($file);

        try {
            if (null !== $cursor) {
                if (!ctype_digit($cursor)) {
                    throw new InvalidResumeCursorException(sprintf('Invalid CSV resume cursor "%s", expected a byte offset', $cursor));
                }
                if (0 !== fseek($handle, (int) $cursor)) {
                    throw new InvalidResumeCursorException(sprintf('Could not seek to byte offset %s in "%s"', $cursor, $file->getPathname()));
                }
            }

            while (false !== ($row = $this->readCanonicalRecord($handle))) {
                // fgetcsv() yields [null] for blank lines; cells must be strings
                $row = array_map(static fn (?string $cell): string => (string) $cell, $row);

                yield (string) ftell($handle) => $row;
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return resource
     *
     * @throws UnreadableFileException
     */
    private function openForEngineRead(SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw new UnreadableFileException(sprintf('Import file "%s" is not readable', $file->getPathname()));
        }

        $handle = fopen($file->getPathname(), 'rb');
        if (false === $handle) {
            throw new UnreadableFileException(sprintf('Could not open import file "%s"', $file->getPathname()));
        }

        return $handle;
    }

    /**
     * @param resource $handle
     *
     * @return array<int, string|null>|false
     */
    private function readCanonicalRecord($handle)
    {
        return fgetcsv($handle, 0, CsvImportFileNormalizer::CSV_DELIMITER, CsvImportFileNormalizer::CSV_ENCLOSURE, CsvImportFileNormalizer::CSV_ESCAPE);
    }
}
