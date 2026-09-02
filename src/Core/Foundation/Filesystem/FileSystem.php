<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Filesystem;

use SplFileInfo;

class FileSystem
{
    /**
     * Default mode for directories
     */
    public const DEFAULT_MODE_FOLDER = 0755;

    /**
     * Default mode for files
     */
    public const DEFAULT_MODE_FILE = 0644;

    /**
     * Replaces directory separators with the system's native one
     * and trims the trailing separator.
     */
    public function normalizePath($path)
    {
        return rtrim(
            str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path),
            DIRECTORY_SEPARATOR
        );
    }

    private function joinTwoPaths($a, $b)
    {
        return $this->normalizePath($a) . DIRECTORY_SEPARATOR . $this->normalizePath($b);
    }

    /**
     * Joins an arbitrary number of paths, normalizing them along the way.
     */
    public function joinPaths()
    {
        if (func_num_args() < 2) {
            throw new Exception('joinPaths requires at least 2 arguments.');
        }
        if (func_num_args() === 2) {
            $arg_O = func_get_arg(0);
            $arg_1 = func_get_arg(1);

            return $this->joinTwoPaths($arg_O, $arg_1);
        }

        $func_args = func_get_args();
        $arg_0 = func_get_arg(0);

        return $this->joinPaths(
            $arg_0,
            call_user_func_array(
                [$this,
                    'joinPaths', ],
                array_slice($func_args, 1)
            )
        );
    }

    /**
     * Performs a depth first listing of directory entries.
     * Throws exception if $path is not a file.
     * If $path is a file and not a directory, just gets the file info for it
     * and return it in an array.
     *
     * @param string $path
     *
     * @return SplFileInfo[] Array of SplFileInfo object indexed by file path
     */
    public function listEntriesRecursively($path)
    {
        if (!file_exists($path)) {
            throw new Exception(sprintf('No such file or directory: %s', $path));
        }

        if (!is_dir($path)) {
            throw new Exception(sprintf('%s is not a directory', $path));
        }

        $entries = [];

        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $newPath = $this->joinPaths($path, $entry);
            $info = new SplFileInfo($newPath);

            $entries[$newPath] = $info;

            if ($info->isDir()) {
                $entries = array_merge(
                    $entries,
                    $this->listEntriesRecursively($newPath)
                );
            }
        }

        return $entries;
    }

    /**
     * Filter used by listFilesRecursively.
     */
    private function matchOnlyFiles(SplFileInfo $info)
    {
        return $info->isFile();
    }

    /**
     * Same as listEntriesRecursively but returns only files.
     */
    public function listFilesRecursively($path)
    {
        return array_filter(
            $this->listEntriesRecursively($path),
            [$this, 'matchOnlyFiles']
        );
    }
}
