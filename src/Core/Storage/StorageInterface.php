<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Storage;

use PrestaShop\PrestaShop\Core\Storage\Exception\StorageException;

/**
 * Reads and writes files on a storage backend, which may or may not be the local disk.
 *
 * Paths are always relative to the storage root and use "/" as separator, whatever the
 * backend is. Implementations must not expose their underlying library in their signatures,
 * so that the backend can be replaced without touching the calling code.
 */
interface StorageInterface
{
    public function fileExists(string $path): bool;

    public function directoryExists(string $path): bool;

    /**
     * @throws StorageException
     */
    public function read(string $path): string;

    /**
     * Useful for big files, as the whole content is never loaded in memory.
     *
     * @return resource
     *
     * @throws StorageException
     */
    public function readStream(string $path);

    /**
     * Creates the file, and any missing parent directory, or replaces it when it already exists.
     *
     * @throws StorageException
     */
    public function write(string $path, string $contents): void;

    /**
     * @param resource $contents
     *
     * @throws StorageException
     */
    public function writeStream(string $path, $contents): void;

    /**
     * Deleting a file that does not exist is not an error.
     *
     * @throws StorageException
     */
    public function delete(string $path): void;

    /**
     * @throws StorageException
     */
    public function createDirectory(string $path): void;

    /**
     * Deletes the directory and everything it contains.
     *
     * @throws StorageException
     */
    public function deleteDirectory(string $path): void;

    /**
     * @throws StorageException
     */
    public function copy(string $source, string $destination): void;

    /**
     * @throws StorageException
     */
    public function move(string $source, string $destination): void;

    /**
     * Lists the files of a directory, without the directories themselves.
     *
     * @return string[] paths relative to the storage root
     *
     * @throws StorageException
     */
    public function listFiles(string $path, bool $recursive = false): array;

    /**
     * @return int size in bytes
     *
     * @throws StorageException
     */
    public function fileSize(string $path): int;

    /**
     * @throws StorageException
     */
    public function mimeType(string $path): string;

    /**
     * @return int timestamp
     *
     * @throws StorageException
     */
    public function lastModified(string $path): int;

    /**
     * URL the file can be served from, which allows serving files from a bucket or a CDN
     * instead of the shop itself.
     *
     * @throws StorageException when the storage has no public URL configured
     */
    public function publicUrl(string $path): string;
}
