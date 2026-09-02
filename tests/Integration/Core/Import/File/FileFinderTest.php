<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Core\Import\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Import\File\FileFinder;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use Symfony\Component\Filesystem\Filesystem;

class FileFinderTest extends TestCase
{
    /** @var string */
    protected $adminDirectory;

    /** @var string */
    protected $importSubDirectory;

    /** @var FileFinder */
    protected $filefinder;

    /** @var ImportDirectory */
    protected $importDirectory;

    /** @var Filesystem */
    protected $fs;

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->adminDirectory = sys_get_temp_dir() . '/' . uniqid();
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('set')
            ->with('_PS_ADMIN_DIR_', $this->adminDirectory);
        $configuration->method('get')->willReturnMap([['_PS_ADMIN_DIR_', $this->adminDirectory]]);
        $this->importDirectory = new ImportDirectory($configuration);
        $this->importSubDirectory = $this->importDirectory->getDir() . 'csvfromexcel/';
        $this->filefinder = new FileFinder($this->importDirectory);
        $this->fs->mkdir([$this->adminDirectory, $this->importDirectory->getDir(), $this->importSubDirectory]);
    }

    public function testGetImportFileNames(): void
    {
        $this->assertCount(0, $this->filefinder->getImportFileNames());

        $importedFileName = 'imported_file.csv';
        $importedSubdirFileName = 'imported_file_subdir.csv';
        $indexPhpFile = 'index.php';

        $this->fs->touch([
            $this->importDirectory->getDir() . $importedFileName,
            $this->importDirectory->getDir() . $indexPhpFile,
            $this->importSubDirectory . $importedSubdirFileName,
        ]
        );

        $this->assertCount(1, $this->filefinder->getImportFileNames());
        $this->assertEquals($importedFileName, $this->filefinder->getImportFileNames()[0]);
    }

    protected function tearDown(): void
    {
        $this->fs->remove($this->adminDirectory);
    }
}
