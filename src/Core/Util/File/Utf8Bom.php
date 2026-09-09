<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\File;

/**
 * The UTF-8 byte order mark, and the one place that knows its byte sequence.
 *
 * WHY it is written to back office CSV exports: the files are UTF-8 but carry no
 * encoding declaration a spreadsheet can see. Excel on Windows therefore reads them in
 * the system codepage and mangles every non-ASCII character - a currency symbol becomes
 * mojibake. The BOM is the only in-band signal Excel honours; an HTTP charset header
 * does not survive the download.
 *
 * WHY it is also skipped on import: PrestaShop is not the only producer of the files it
 * reads. Excel's own "CSV UTF-8" writes a BOM, so a reader that does not skip one puts
 * those three bytes into the first column of the header row and the column stops
 * matching.
 */
final class Utf8Bom
{
    public const SEQUENCE = "\xEF\xBB\xBF";

    /**
     * Move an open handle past a leading BOM, leaving it at the first real byte.
     *
     * Rewinds either way, so it is safe to call on a handle that has already been read.
     *
     * Deliberately typed as mixed rather than resource: the legacy import path passes
     * whatever it was handed, and a no-op on a non-resource is the useful behaviour
     * there. The guard below is the contract.
     *
     * @param mixed $handle
     */
    public static function skip($handle): void
    {
        if (!is_resource($handle)) {
            return;
        }

        rewind($handle);

        if (fread($handle, strlen(self::SEQUENCE)) !== self::SEQUENCE) {
            rewind($handle);
        }
    }
}
