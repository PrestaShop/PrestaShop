<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\File;

use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use SplFileInfo;

/**
 * Tells a spreadsheet from a CSV by content, not by name: an Admin API upload reaches the engine
 * under PHP's extension-less temp name, and a client's extension is a claim, not a fact.
 *
 * Every spreadsheet format PhpSpreadsheet reads is either a ZIP container (xlsx, ods) or an OLE2
 * compound file (xls), and both announce themselves in their first bytes. Anything else is read as
 * text with the separator the merchant chose.
 */
final class ImportFileFormatDetector
{
    public const FORMAT_CSV = 'csv';
    public const FORMAT_SPREADSHEET = 'spreadsheet';

    private const ZIP_SIGNATURE = "PK\x03\x04";
    private const OLE2_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    /**
     * @return string one of the FORMAT_* constants
     *
     * @throws UnreadableFileException
     */
    public function detect(SplFileInfo $file): string
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (false === $handle) {
            throw new UnreadableFileException(sprintf('Could not open import file "%s"', $file->getPathname()));
        }

        try {
            $leadingBytes = (string) fread($handle, 8);
        } finally {
            fclose($handle);
        }

        foreach ([self::ZIP_SIGNATURE, self::OLE2_SIGNATURE] as $signature) {
            if (str_starts_with($leadingBytes, $signature)) {
                return self::FORMAT_SPREADSHEET;
            }
        }

        return self::FORMAT_CSV;
    }
}
