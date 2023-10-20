<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
