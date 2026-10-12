<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use ZipArchive;

/**
 * Extracts zip archives only when every entry stays inside the destination folder.
 *
 * Every entry is checked before anything is written: one entry pointing outside the destination
 * (parent segment, absolute path, drive letter) or stored as a symbolic link makes the whole
 * archive rejected, so a refused archive writes nothing.
 */
final class SafeZipExtractor
{
    private const FILE_TYPE_MASK = 0170000;

    private const SYMBOLIC_LINK_TYPE = 0120000;

    /**
     * @return bool true when the archive was checked and extracted, false otherwise
     */
    public function extract(string $archivePath, string $destination): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== true) {
            return false;
        }

        try {
            return $this->hasOnlySafeEntries($zip) && $zip->extractTo($destination);
        } finally {
            $zip->close();
        }
    }

    private function hasOnlySafeEntries(ZipArchive $zip): bool
    {
        for ($index = 0; $index < $zip->numFiles; ++$index) {
            $entryName = $zip->getNameIndex($index);
            if (false === $entryName || !$this->isSafeEntryName($entryName) || $this->isSymbolicLink($zip, $index)) {
                return false;
            }
        }

        return true;
    }

    public function isSafeEntryName(string $entryName): bool
    {
        if ('' === $entryName || false !== strpos($entryName, "\0")) {
            return false;
        }

        // Absolute path, on any system
        if (in_array($entryName[0], ['/', '\\'], true) || preg_match('/^[a-zA-Z]:/', $entryName)) {
            return false;
        }

        foreach (preg_split('#[/\\\\]#', $entryName) as $segment) {
            if ('..' === $segment) {
                return false;
            }
        }

        return true;
    }

    private function isSymbolicLink(ZipArchive $zip, int $index): bool
    {
        $operatingSystem = 0;
        $attributes = 0;
        if (!$zip->getExternalAttributesIndex($index, $operatingSystem, $attributes)) {
            return false;
        }

        return ZipArchive::OPSYS_UNIX === $operatingSystem
            && (($attributes >> 16) & self::FILE_TYPE_MASK) === self::SYMBOLIC_LINK_TYPE;
    }
}
