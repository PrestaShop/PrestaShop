<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * FileRemoval is responsible for deleting import files.
 */
final class FileRemoval
{
    /**
     * @var ImportDirectory
     */
    private $importDirectory;

    public function __construct(ImportDirectory $importDirectory)
    {
        $this->importDirectory = $importDirectory;
    }

    /**
     * Remove file from import directory.
     *
     * @param string $filename
     */
    public function remove($filename)
    {
        $fs = new Filesystem();
        $filename = basename($filename);
        $filesToRemove = [
            $this->importDirectory . $filename,
            $this->importDirectory . 'csvfromexcel/' . $filename,
        ];
        foreach ($filesToRemove as $fileToRemove) {
            if (file_exists($fileToRemove)) {
                $fs->remove($fileToRemove);
            }
        }
    }
}
