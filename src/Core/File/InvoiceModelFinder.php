<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class InvoiceModelFinder finds invoice model files.
 */
final class InvoiceModelFinder implements FileFinderInterface
{
    /**
     * @var array
     */
    private $invoiceModelDirectories;

    /**
     * @param array $invoiceModelDirectories
     */
    public function __construct(array $invoiceModelDirectories)
    {
        $this->invoiceModelDirectories = $invoiceModelDirectories;
    }

    /**
     * Finds all invoice model files.
     *
     * @return array
     */
    public function find()
    {
        $directories = $this->invoiceModelDirectories;
        $filesystem = new Filesystem();

        foreach ($directories as $key => $directory) {
            if (!$filesystem->exists($directory)) {
                unset($directories[$key]);
            }
        }

        $finder = new Finder();
        $finder->files()
            ->in($directories)
            ->name('invoice-*.tpl');

        $fileNames = [];

        foreach ($finder as $file) {
            $fileNames[] = $file->getFilename();
        }

        return $fileNames;
    }
}
