<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\File\ImportFileFormatDetector;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use SplFileInfo;

/**
 * The name of the file says nothing: an API upload has no extension, and a client may lie.
 */
class ImportFileFormatDetectorTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            @unlink($file);
        }
        $this->temporaryFiles = [];

        parent::tearDown();
    }

    public function testTextIsCsvWhateverItsName(): void
    {
        $detector = new ImportFileFormatDetector();

        $this->assertSame(ImportFileFormatDetector::FORMAT_CSV, $detector->detect($this->createFile("name;price\nChair;12.5\n", '.csv')));
        $this->assertSame(ImportFileFormatDetector::FORMAT_CSV, $detector->detect($this->createFile("name;price\nChair;12.5\n", '')), 'PHP names an upload phpXXXXXX, no extension');
        $this->assertSame(ImportFileFormatDetector::FORMAT_CSV, $detector->detect($this->createFile("name,price\n", '.xlsx')), 'A misnamed CSV is still a CSV');
        $this->assertSame(ImportFileFormatDetector::FORMAT_CSV, $detector->detect($this->createFile('', '.csv')), 'An empty file is read as text and rejected later for holding no record');
    }

    public function testAZipContainerIsASpreadsheet(): void
    {
        // xlsx and ods are ZIP archives
        $file = $this->createFile("PK\x03\x04\x14\x00\x06\x00" . str_repeat("\x00", 24), '');

        $this->assertSame(ImportFileFormatDetector::FORMAT_SPREADSHEET, (new ImportFileFormatDetector())->detect($file));
    }

    public function testAnOle2CompoundFileIsASpreadsheet(): void
    {
        // the legacy xls container
        $file = $this->createFile("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\x00", 24), '.csv');

        $this->assertSame(ImportFileFormatDetector::FORMAT_SPREADSHEET, (new ImportFileFormatDetector())->detect($file), 'Even when it claims to be a CSV');
    }

    public function testAFileThatCannotBeOpenedIsReported(): void
    {
        $this->expectException(UnreadableFileException::class);

        @(new ImportFileFormatDetector())->detect(new SplFileInfo(sys_get_temp_dir() . '/import-format-detector-missing-' . uniqid()));
    }

    private function createFile(string $content, string $extension): SplFileInfo
    {
        $path = sys_get_temp_dir() . '/import-format-detector-' . uniqid() . $extension;
        file_put_contents($path, $content);
        $this->temporaryFiles[] = $path;

        return new SplFileInfo($path);
    }
}
