<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\File;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\MalformedImportFileException;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use SplFileInfo;
use Throwable;

/**
 * Normalizes an uploaded import file once, at run start, into a run-scoped
 * working file using one canonical CSV dialect. All downstream reading
 * (cursor-resumable batches) then needs no per-run dialect or encoding
 * handling: the user-chosen CSV separator is consumed here and never again.
 *
 * Normalization guarantees on the working file:
 * - canonical dialect (CSV_* constants), UTF-8, no BOM
 * - 1:1 record mapping with the source (blank lines preserved), so row
 *   indexes are stable between the source and the working file
 *
 * Replaces the per-batch whole-file encoding checks and the deprecated
 * utf8_encode() of the legacy path, and fixes the legacy Excel->CSV
 * converter's forced ';' separator and stale filename-keyed cache (the
 * caller provides a fresh target path per run, nothing is cached).
 */
final class ImportFileNormalizer
{
    /**
     * Canonical CSV dialect of every working file.
     */
    public const CSV_DELIMITER = ';';
    public const CSV_ENCLOSURE = '"';
    /** Empty escape: RFC 4180 quote-doubling only, no backslash escaping */
    public const CSV_ESCAPE = '';

    private const UTF8_BOM = "\xEF\xBB\xBF";
    private const UTF16_BOMS = ["\xFE\xFF", "\xFF\xFE"];

    /**
     * @param SplFileInfo $sourceFile the uploaded file (CSV or spreadsheet)
     * @param string $targetPath where to write the working file (fresh path per run)
     * @param string $sourceCsvDelimiter CSV separator of the SOURCE file (ignored for spreadsheets)
     *
     * @throws UnreadableFileException
     * @throws MalformedImportFileException
     */
    public function normalize(SplFileInfo $sourceFile, string $targetPath, string $sourceCsvDelimiter = self::CSV_DELIMITER): SplFileInfo
    {
        if (!$sourceFile->isReadable()) {
            throw new UnreadableFileException(sprintf('Import file "%s" is not readable', $sourceFile->getPathname()));
        }

        if (preg_match('/\.csv$/i', $sourceFile->getFilename())) {
            $this->normalizeCsv($sourceFile, $targetPath, $sourceCsvDelimiter);
        } else {
            $this->convertSpreadsheet($sourceFile, $targetPath);
        }

        return new SplFileInfo($targetPath);
    }

    private function normalizeCsv(SplFileInfo $sourceFile, string $targetPath, string $sourceCsvDelimiter): void
    {
        $source = fopen($sourceFile->getPathname(), 'rb');
        if (false === $source) {
            throw new UnreadableFileException(sprintf('Could not open import file "%s"', $sourceFile->getPathname()));
        }

        try {
            $this->skipByteOrderMark($source, $sourceFile->getPathname());

            $target = fopen($targetPath, 'wb');
            if (false === $target) {
                throw new MalformedImportFileException(sprintf('Could not open working file "%s" for writing', $targetPath));
            }

            try {
                while (false !== ($row = fgetcsv($source, 0, $sourceCsvDelimiter, self::CSV_ENCLOSURE, '\\'))) {
                    if ($this->isBlankRecord($row)) {
                        // preserve blank lines so row indexes stay aligned with the source
                        fwrite($target, "\n");
                        continue;
                    }

                    $row = array_map(
                        static function (?string $cell): string {
                            $cell = (string) $cell;

                            return mb_check_encoding($cell, 'UTF-8') ? $cell : mb_convert_encoding($cell, 'UTF-8', 'ISO-8859-1');
                        },
                        $row
                    );

                    if (false === fputcsv($target, $row, self::CSV_DELIMITER, self::CSV_ENCLOSURE, self::CSV_ESCAPE)) {
                        throw new MalformedImportFileException(sprintf('Could not write to working file "%s"', $targetPath));
                    }
                }
            } finally {
                fclose($target);
            }
        } finally {
            fclose($source);
        }
    }

    private function convertSpreadsheet(SplFileInfo $sourceFile, string $targetPath): void
    {
        try {
            $reader = IOFactory::createReaderForFile($sourceFile->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($sourceFile->getPathname());

            /** @var CsvWriter $writer */
            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
            $writer->setSheetIndex(0);
            $writer->setDelimiter(self::CSV_DELIMITER);
            $writer->setEnclosure(self::CSV_ENCLOSURE);
            $writer->setUseBOM(false);
            $writer->save($targetPath);
        } catch (Throwable $e) {
            throw new MalformedImportFileException(sprintf('Could not convert spreadsheet "%s" to CSV: %s', $sourceFile->getFilename(), $e->getMessage()), 0, $e);
        }
    }

    /**
     * @param resource $handle
     */
    private function skipByteOrderMark($handle, string $pathname): void
    {
        $leadingBytes = (string) fread($handle, 3);

        foreach (self::UTF16_BOMS as $utf16Bom) {
            if (str_starts_with($leadingBytes, $utf16Bom)) {
                throw new MalformedImportFileException(sprintf('Import file "%s" is UTF-16 encoded, which is not supported; please provide a UTF-8 or ISO-8859-1 file', $pathname));
            }
        }

        if (self::UTF8_BOM !== $leadingBytes) {
            rewind($handle);
        }
    }

    /**
     * fgetcsv() returns [null] for a blank line.
     *
     * @param array<int, string|null> $row
     */
    private function isBlankRecord(array $row): bool
    {
        return [null] === $row;
    }
}
