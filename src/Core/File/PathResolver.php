<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\File;

use PrestaShop\PrestaShop\Core\File\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;

/**
 * Resolves request-selected files under an expected filesystem directory.
 */
class PathResolver
{
    /**
     * @throws FileNotFoundException
     * @throws InvalidFileException
     */
    public function resolveFileUnderDirectory(string $directory, string $fileName): string
    {
        $baseDirectory = realpath($directory);
        if (false === $baseDirectory) {
            throw new FileNotFoundException(sprintf('Directory "%s" does not exist.', $directory));
        }

        $filePath = realpath($baseDirectory . DIRECTORY_SEPARATOR . $fileName);
        if (false === $filePath || !is_file($filePath)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exist.', $fileName));
        }

        $baseDirectory = rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($filePath, $baseDirectory)) {
            throw new InvalidFileException(sprintf('File "%s" is outside the allowed directory.', $fileName));
        }

        return $filePath;
    }
}
