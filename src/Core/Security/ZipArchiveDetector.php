<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use ZipArchive;

/**
 * Tells whether a file is a zip archive by opening it, whatever its name or guessed MIME type.
 *
 * The MIME type alone is not reliable: since PHP 8.6, fileinfo guesses application/octet-stream
 * (Microsoft OOXML) for some valid zip archives.
 */
final class ZipArchiveDetector
{
    public function isZipArchive(string $filePath): bool
    {
        // An empty file is opened as an empty archive
        if (!is_file($filePath) || 0 === filesize($filePath)) {
            return false;
        }

        $zip = new ZipArchive();
        if (true !== $zip->open($filePath, ZipArchive::RDONLY)) {
            return false;
        }
        $zip->close();

        return true;
    }
}
