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
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/**
 * Normalizes an uploaded import file once, at run start, into a run-scoped
 * working file using one canonical CSV dialect. All downstream reading
 * (cursor-resumable batches) then needs no per-run dialect or encoding
 * handling: the user-chosen CSV separator is consumed here and never again.
 *
 * Normalization guarantees on the working file:
 * - canonical dialect (CSV_* constants), UTF-8, no BOM
 * - the configured skip rows (header lines, already-processed leading rows)
 *   are stripped here, once: the working file contains DATA RECORDS ONLY,
 *   so the engine, the run context and the importers never deal with a
 *   skip count. Row indexes are 0-based data-record indexes; presenters
 *   add the run's skip count back when they need source-file line numbers.
 * - apart from that shift, 1:1 record mapping with the source (blank lines
 *   preserved)
 *
 * Replaces the per-batch whole-file encoding checks and the deprecated
 * utf8_encode() of the legacy path, and fixes the legacy Excel->CSV
 * converter's forced ';' separator and stale filename-keyed cache (the
 * caller provides a fresh target path per run, nothing is cached).
 */
class CsvImportFileNormalizer
{
    /**
     * Canonical CSV dialect of every working file.
     */
    public const CSV_DELIMITER = ';';
    public const CSV_ENCLOSURE = '"';
    /** Empty escape: RFC 4180 quote-doubling only, no backslash escaping */
    public const CSV_ESCAPE = '';

    protected const UTF8_BOM = "\xEF\xBB\xBF";
    protected const UTF16_BOMS = ["\xFE\xFF", "\xFF\xFE"];

    public function __construct(
        protected readonly Filesystem $filesystem,
    ) {
    }

    /**
     * @param SplFileInfo $sourceFile the uploaded file (CSV or spreadsheet)
     * @param string $targetPath where to write the working file (fresh path per run)
     * @param string $sourceCsvDelimiter CSV separator of the SOURCE file (ignored for spreadsheets)
     * @param int $skipRows leading records to strip (header lines, already-imported leading rows)
     *
     * @throws UnreadableFileException
     * @throws MalformedImportFileException
     */
    public function normalize(SplFileInfo $sourceFile, string $targetPath, string $sourceCsvDelimiter = self::CSV_DELIMITER, int $skipRows = 0): NormalizedImportFile
    {
        if (!$sourceFile->isReadable()) {
            throw new UnreadableFileException(sprintf('Import file "%s" is not readable', $sourceFile->getPathname()));
        }

        if (preg_match('/\.csv$/i', $sourceFile->getFilename())) {
            $dataRecordCount = $this->normalizeCsv($sourceFile, $targetPath, $sourceCsvDelimiter, $skipRows);
        } else {
            $dataRecordCount = $this->convertSpreadsheet($sourceFile, $targetPath, $skipRows);
        }

        return new NormalizedImportFile(new SplFileInfo($targetPath), $dataRecordCount);
    }

    /**
     * @return int number of data records written to the working file
     *
     * @throws UnreadableFileException
     * @throws MalformedImportFileException
     */
    protected function normalizeCsv(SplFileInfo $sourceFile, string $targetPath, string $sourceCsvDelimiter, int $skipRows): int
    {
        $source = fopen($sourceFile->getPathname(), 'rb');
        if (false === $source) {
            throw new UnreadableFileException(sprintf('Could not open import file "%s"', $sourceFile->getPathname()));
        }

        $dataRecordCount = 0;

        try {
            $this->skipByteOrderMark($source, $sourceFile->getPathname());

            $target = fopen($targetPath, 'wb');
            if (false === $target) {
                throw new MalformedImportFileException(sprintf('Could not open working file "%s" for writing', $targetPath));
            }

            try {
                while (false !== ($row = fgetcsv($source, 0, $sourceCsvDelimiter, self::CSV_ENCLOSURE, '\\'))) {
                    if ($skipRows > 0) {
                        // blank records count too: the skip is a physical position in the source
                        --$skipRows;
                        continue;
                    }
                    if ($this->isBlankRecord($row)) {
                        // preserve blank lines so row indexes stay aligned with the source
                        fwrite($target, "\n");
                        ++$dataRecordCount;
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
                    ++$dataRecordCount;
                }
            } finally {
                fclose($target);
            }
        } finally {
            fclose($source);
        }

        return $dataRecordCount;
    }

    /**
     * @return int number of data records written to the working file
     *
     * @throws MalformedImportFileException
     */
    protected function convertSpreadsheet(SplFileInfo $sourceFile, string $targetPath, int $skipRows): int
    {
        // always convert to an intermediate file, then reuse the CSV pass:
        // it strips the skip rows record-accurately AND counts the records
        $conversionTarget = $targetPath . '.spreadsheet.tmp';

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
            $writer->save($conversionTarget);
        } catch (Throwable $e) {
            throw new MalformedImportFileException(sprintf('Could not convert spreadsheet "%s" to CSV: %s', $sourceFile->getFilename(), $e->getMessage()), 0, $e);
        }

        try {
            return $this->normalizeCsv(new SplFileInfo($conversionTarget), $targetPath, self::CSV_DELIMITER, $skipRows);
        } finally {
            $this->filesystem->remove($conversionTarget);
        }
    }

    /**
     * @param resource $handle
     *
     * @throws MalformedImportFileException
     */
    protected function skipByteOrderMark($handle, string $pathname): void
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
    protected function isBlankRecord(array $row): bool
    {
        return [null] === $row;
    }
}
