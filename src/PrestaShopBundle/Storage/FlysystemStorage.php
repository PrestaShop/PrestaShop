<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Storage;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use PrestaShop\PrestaShop\Core\Storage\Exception\StorageOperationException;
use PrestaShop\PrestaShop\Core\Storage\StorageInterface;
use Throwable;

/**
 * Storage backed by League Flysystem, whose adapter is configured in the application
 * configuration. Flysystem exceptions are wrapped so that they never leak out of the storage.
 */
class FlysystemStorage implements StorageInterface
{
    public function __construct(
        private readonly FilesystemOperator $filesystem
    ) {
    }

    public function fileExists(string $path): bool
    {
        return $this->run(fn () => $this->filesystem->fileExists($path), 'check if file "%s" exists', $path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->run(fn () => $this->filesystem->directoryExists($path), 'check if directory "%s" exists', $path);
    }

    public function read(string $path): string
    {
        return $this->run(fn () => $this->filesystem->read($path), 'read file "%s"', $path);
    }

    public function readStream(string $path)
    {
        return $this->run(fn () => $this->filesystem->readStream($path), 'read file "%s"', $path);
    }

    public function write(string $path, string $contents): void
    {
        $this->run(fn () => $this->filesystem->write($path, $contents), 'write file "%s"', $path);
    }

    public function writeStream(string $path, $contents): void
    {
        $this->run(fn () => $this->filesystem->writeStream($path, $contents), 'write file "%s"', $path);
    }

    public function delete(string $path): void
    {
        $this->run(fn () => $this->filesystem->delete($path), 'delete file "%s"', $path);
    }

    public function createDirectory(string $path): void
    {
        $this->run(fn () => $this->filesystem->createDirectory($path), 'create directory "%s"', $path);
    }

    public function deleteDirectory(string $path): void
    {
        $this->run(fn () => $this->filesystem->deleteDirectory($path), 'delete directory "%s"', $path);
    }

    public function copy(string $source, string $destination): void
    {
        $this->run(
            fn () => $this->filesystem->copy($source, $destination),
            sprintf('copy file "%%s" to "%s"', $destination),
            $source
        );
    }

    public function move(string $source, string $destination): void
    {
        $this->run(
            fn () => $this->filesystem->move($source, $destination),
            sprintf('move file "%%s" to "%s"', $destination),
            $source
        );
    }

    public function listFiles(string $path, bool $recursive = false): array
    {
        return $this->run(
            fn () => $this->filesystem
                ->listContents($path, $recursive)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray(),
            'list the files of directory "%s"',
            $path
        );
    }

    public function fileSize(string $path): int
    {
        return $this->run(fn () => $this->filesystem->fileSize($path), 'get the size of file "%s"', $path);
    }

    public function mimeType(string $path): string
    {
        return $this->run(fn () => $this->filesystem->mimeType($path), 'get the mime type of file "%s"', $path);
    }

    public function lastModified(string $path): int
    {
        return $this->run(fn () => $this->filesystem->lastModified($path), 'get the last modification date of file "%s"', $path);
    }

    public function publicUrl(string $path): string
    {
        return $this->run(fn () => $this->filesystem->publicUrl($path), 'get the public URL of file "%s"', $path);
    }

    /**
     * @param string $description what the operation does, with a single "%s" placeholder for the path
     *
     * @return mixed
     *
     * @throws StorageOperationException
     */
    private function run(callable $operation, string $description, string $path)
    {
        try {
            return $operation();
        } catch (Throwable $exception) {
            throw new StorageOperationException(
                sprintf('Storage was unable to ' . $description, $path),
                0,
                $exception
            );
        }
    }
}
