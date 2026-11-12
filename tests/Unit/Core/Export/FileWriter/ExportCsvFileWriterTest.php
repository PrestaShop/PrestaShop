<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Export\FileWriter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Export\Data\ExportableData;
use PrestaShop\PrestaShop\Core\Export\Exception\FileWritingException;
use PrestaShop\PrestaShop\Core\Export\ExportDirectory;
use PrestaShop\PrestaShop\Core\Export\FileWriter\ExportCsvFileWriter;
use Symfony\Component\Filesystem\Filesystem;

class ExportCsvFileWriterTest extends TestCase
{
    private string $adminDir;

    protected function setUp(): void
    {
        $this->adminDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('ps_export_csv_', true);
        mkdir($this->adminDir . DIRECTORY_SEPARATOR . 'export', 0777, true);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->adminDir);
    }

    public function testWriteWritesTitlesAndRowsAsCsv(): void
    {
        $data = new ExportableData(
            ['id', 'name', 'description'],
            [
                [1, 'Hummingbird', 'A "printed" t-shirt; organic'],
                [2, 'Mug', "Two\nlines"],
            ]
        );

        $file = $this->createWriter()->write('export.csv', $data, ';');

        $this->assertSame(
            "id;name;description\n"
            . "1;Hummingbird;\"A \"\"printed\"\" t-shirt; organic\"\n"
            . "2;Mug;\"Two\nlines\"\n",
            file_get_contents($file->getPathname())
        );
    }

    public function testWriteThrowsExceptionWhenFileCannotBeOpened(): void
    {
        $this->expectException(FileWritingException::class);
        $this->expectExceptionCode(FileWritingException::CANNOT_OPEN_FILE_FOR_WRITING);

        $this->createWriter()->write('missing-dir/export.csv', new ExportableData([], []), ';');
    }

    private function createWriter(): ExportCsvFileWriter
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('_PS_ADMIN_DIR_')->willReturn($this->adminDir);

        return new ExportCsvFileWriter(new ExportDirectory($configuration));
    }
}
