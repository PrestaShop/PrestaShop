<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Language\Pack\Import;

use PrestaShop\PrestaShop\Core\Language\Pack\Import\Exception\InvalidTranslationPackException;
use ZipArchive;

/**
 * Checks that an archive really is a translation pack before anything extracts it.
 *
 * A pack as published for a shop holds a single directory named after its locale and nothing but
 * XLF catalogues inside it. Everything else is refused here, so an upload cannot put a file of
 * another kind into the translations directory.
 */
class TranslationPackValidator
{
    private const CATALOGUE_EXTENSION = 'xlf';

    /**
     * Room enough for any published pack - the largest shipped one holds under two hundred
     * catalogues - while keeping an upload from expanding without bound on extraction.
     */
    private const MAX_ENTRIES = 5000;
    private const MAX_UNCOMPRESSED_BYTES = 268435456;

    /**
     * @return string the locale the pack holds, taken from its single top level directory
     *
     * @throws InvalidTranslationPackException
     */
    public function validate(string $archivePath): string
    {
        if (!is_file($archivePath) || !is_readable($archivePath)) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" cannot be read.', $archivePath),
                InvalidTranslationPackException::NOT_READABLE
            );
        }

        $archive = new ZipArchive();
        // CHECKCONS makes the extension verify the archive's own consistency records rather than
        // trusting the extension or the declared mime type of the upload.
        if (true !== $archive->open($archivePath, ZipArchive::CHECKCONS)) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" is not a readable zip archive.', $archivePath),
                InvalidTranslationPackException::NOT_AN_ARCHIVE
            );
        }

        try {
            return $this->readLocale($archive, $archivePath);
        } finally {
            $archive->close();
        }
    }

    /**
     * @throws InvalidTranslationPackException
     */
    private function readLocale(ZipArchive $archive, string $archivePath): string
    {
        $locale = null;
        $catalogueCount = 0;
        $uncompressedBytes = 0;

        if ($archive->numFiles > self::MAX_ENTRIES) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" holds %d entries, more than the %d allowed.', $archivePath, $archive->numFiles, self::MAX_ENTRIES),
                InvalidTranslationPackException::TOO_LARGE
            );
        }

        for ($index = 0; $index < $archive->numFiles; ++$index) {
            $entry = (string) $archive->getNameIndex($index);
            $isDirectory = str_ends_with($entry, '/');
            $segments = explode('/', trim($entry, '/'));

            $this->rejectUnsafeEntry($entry, $segments, $archivePath);

            if (!$isDirectory) {
                if (2 !== count($segments) || !$this->isCatalogue($segments[1])) {
                    throw new InvalidTranslationPackException(
                        sprintf('Translation pack "%s" holds "%s", which is not an XLF catalogue in a locale directory.', $archivePath, $entry),
                        InvalidTranslationPackException::UNEXPECTED_ENTRY
                    );
                }
                ++$catalogueCount;

                // Names alone say nothing about what extraction will write, so the declared sizes
                // are added up before anything is unpacked.
                $uncompressedBytes += (int) ($archive->statIndex($index)['size'] ?? 0);
                if ($uncompressedBytes > self::MAX_UNCOMPRESSED_BYTES) {
                    throw new InvalidTranslationPackException(
                        sprintf('Translation pack "%s" unpacks to more than the %d bytes allowed.', $archivePath, self::MAX_UNCOMPRESSED_BYTES),
                        InvalidTranslationPackException::TOO_LARGE
                    );
                }
            }

            if (null !== $locale && $locale !== $segments[0]) {
                throw new InvalidTranslationPackException(
                    sprintf('Translation pack "%s" mixes the locales "%s" and "%s".', $archivePath, $locale, $segments[0]),
                    InvalidTranslationPackException::MIXED_LOCALES
                );
            }
            $locale = $segments[0];
        }

        if (0 === $catalogueCount || null === $locale) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" holds no XLF catalogue.', $archivePath),
                InvalidTranslationPackException::EMPTY_ARCHIVE
            );
        }

        // The locale becomes a directory name under the translations directory, so its shape is
        // settled here and its existence as a shop language is checked by the caller. The pattern
        // is the one Validate::isLocale() applies, so a pack this accepts is never turned away by
        // the installation step afterwards.
        if (!preg_match('/^[a-z]{2}-[A-Z]{2}$/', $locale)) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" names its directory "%s", which is not a locale.', $archivePath, $locale),
                InvalidTranslationPackException::MALFORMED_LOCALE
            );
        }

        return $locale;
    }

    /**
     * @param string[] $segments
     *
     * @throws InvalidTranslationPackException
     */
    private function rejectUnsafeEntry(string $entry, array $segments, string $archivePath): void
    {
        // ZipArchive::extractTo() flattens a traversing name rather than following it, so this is
        // not the only thing standing between an upload and the filesystem - but an archive that
        // carries such a name is not a translation pack and there is no reason to open it further.
        if ('' === $entry
            || str_starts_with($entry, '/')
            || str_contains($entry, '\\')
            || in_array('..', $segments, true)
            || in_array('.', $segments, true)
            || in_array('', $segments, true)
        ) {
            throw new InvalidTranslationPackException(
                sprintf('Translation pack "%s" holds the unsafe entry "%s".', $archivePath, $entry),
                InvalidTranslationPackException::UNEXPECTED_ENTRY
            );
        }
    }

    private function isCatalogue(string $filename): bool
    {
        return self::CATALOGUE_EXTENSION === strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
