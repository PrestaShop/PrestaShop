<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem as PsFileSystem;
use PrestaShop\PrestaShop\Core\Security\Exception\ZipExtractionException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * Extracts zip archives only when every entry stays inside the destination folder.
 *
 * Every entry is checked before anything is written: one entry pointing outside the destination
 * (parent segment, absolute path, drive letter) or stored as a symbolic link makes the whole
 * archive rejected, so a refused archive writes nothing.
 *
 * The archive is extracted into a private folder with a random name in the sandbox, then copied
 * into the destination: a failed extraction leaves the destination untouched.
 */
final class SafeZipExtractor
{
    private const FILE_TYPE_MASK = 0170000;

    private const SYMBOLIC_LINK_TYPE = 0120000;

    private const EXTRACTION_FOLDER_ATTEMPTS = 5;

    /** @var string Folder where archives are extracted and checked before being copied to their destination */
    private $sandboxPath;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(?string $sandboxPath = null, ?Filesystem $filesystem = null)
    {
        // Same sandbox as the rest of the shop when no path is given; the system temp dir only outside a shop
        $sandboxPath = $sandboxPath ?? (defined('_PS_CACHE_DIR_') ? _PS_CACHE_DIR_ . 'sandbox' : sys_get_temp_dir());
        $this->sandboxPath = rtrim($sandboxPath, '/\\') . DIRECTORY_SEPARATOR;
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    /**
     * Extracts the archive into the destination. With a folder, only this top level folder of the archive
     * is copied, into the destination folder of the same name: nothing else from the archive is written.
     *
     * @throws ZipExtractionException when the archive is not extracted, the code gives the reason
     */
    public function extract(string $archivePath, string $destination, ?string $folder = null): void
    {
        if (null !== $folder && !$this->isSafeFolderName($folder)) {
            throw new ZipExtractionException(sprintf('"%s" is not a single folder name.', $folder), ZipExtractionException::INVALID_FOLDER);
        }

        $extractionFolder = $this->createExtractionFolder();

        try {
            $this->extractTo($archivePath, $extractionFolder);

            $source = $extractionFolder;
            $target = rtrim($destination, '/\\');
            if (null !== $folder) {
                $source .= DIRECTORY_SEPARATOR . $folder;
                $target .= DIRECTORY_SEPARATOR . $folder;
                if (!is_dir($source) || is_link($source)) {
                    throw new ZipExtractionException(sprintf('The archive has no "%s" folder.', $folder), ZipExtractionException::MISSING_FOLDER);
                }
            }

            $this->filesystem->mirror(
                $source,
                $target,
                null,
                ['override' => true]
            );
        } catch (IOException $e) {
            // The message of the exception holds the sandbox path, it is only kept as the previous exception
            throw new ZipExtractionException('The archive cannot be copied into its destination.', ZipExtractionException::EXTRACTION_FAILED, $e);
        } finally {
            try {
                $this->filesystem->remove($extractionFolder);
            } catch (IOException $e) {
                // Left in the sandbox, removed with the cache
            }
        }
    }

    private function isSafeEntryName(string $entryName): bool
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

    /**
     * @throws ZipExtractionException
     */
    private function extractTo(string $archivePath, string $folder): void
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== true) {
            throw new ZipExtractionException('The archive cannot be opened.', ZipExtractionException::CANNOT_OPEN_ARCHIVE);
        }

        try {
            // Every entry is checked before anything is written
            for ($index = 0; $index < $zip->numFiles; ++$index) {
                $entryName = $zip->getNameIndex($index);
                if (false === $entryName || !$this->isSafeEntryName($entryName) || $this->isSymbolicLink($zip, $index)) {
                    throw new ZipExtractionException(sprintf('The entry "%s" points outside the destination or is a link.', (string) $entryName), ZipExtractionException::UNSAFE_ENTRY);
                }
            }

            if (!$zip->extractTo($folder)) {
                throw new ZipExtractionException('The archive cannot be extracted.', ZipExtractionException::EXTRACTION_FAILED);
            }
        } finally {
            $zip->close();
        }
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

    /**
     * A single folder name: no separator, no parent or current folder.
     */
    private function isSafeFolderName(string $folder): bool
    {
        return !in_array($folder, ['', '.', '..'], true) && false === strpbrk($folder, "/\\\0");
    }

    /**
     * Creates an empty private folder with a random name in the sandbox.
     * An existing path (folder, file or link) is never reused, another name is tried.
     *
     * @throws ZipExtractionException
     */
    private function createExtractionFolder(): string
    {
        try {
            $this->filesystem->mkdir($this->sandboxPath);
            for ($attempt = 0; $attempt < self::EXTRACTION_FOLDER_ATTEMPTS; ++$attempt) {
                $extractionFolder = $this->sandboxPath . 'extract_' . bin2hex(random_bytes(8));
                // exists() does not see a link whose target is missing, hence is_link()
                if ($this->filesystem->exists($extractionFolder) || is_link($extractionFolder)) {
                    continue;
                }

                $this->filesystem->mkdir($extractionFolder, PsFileSystem::PRIVATE_MODE_FOLDER);

                return $extractionFolder;
            }
        } catch (IOException $e) {
            // The message of the exception holds the sandbox path, it is only kept as the previous exception
            throw new ZipExtractionException('No extraction folder can be created in the sandbox.', ZipExtractionException::SANDBOX_UNAVAILABLE, $e);
        }

        throw new ZipExtractionException('No extraction folder can be created in the sandbox.', ZipExtractionException::SANDBOX_UNAVAILABLE);
    }
}
