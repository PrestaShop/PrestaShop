<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use Symfony\Component\Finder\Finder;

/**
 * Class responsible for finding import files.
 */
final class FileFinder
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
     * Get import file names in import directory.
     *
     * @return array|string[]
     */
    public function getImportFileNames()
    {
        if (!$this->importDirectory->isReadable()) {
            return [];
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->importDirectory->getDir())
            ->depth('0')
            ->notName('/^index\.php/i');

        $fileNames = [];

        foreach ($finder as $file) {
            $fileNames[] = $file->getFilename();
        }

        return $fileNames;
    }
}
