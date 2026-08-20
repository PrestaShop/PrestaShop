<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine\File;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\MalformedImportFileException;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class CsvImportFileNormalizerTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $temporaryFiles = [];

    private CsvImportFileNormalizer $normalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->normalizer = new CsvImportFileNormalizer(new Filesystem());
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->temporaryFiles);
        $this->temporaryFiles = [];
        parent::tearDown();
    }

    public function testCommaDelimitedLatin1FileWithBomIsNormalized(): void
    {
        // BOM + comma dialect + ISO-8859-1 accents + blank line + multi-line cell
        $source = $this->createFile('src_', '.csv', "\xEF\xBB\xBFname,desc\n\"Caf\xE9\",\"line1\nline2\"\n\nlast,row\n");
        $target = $this->reserveFilePath('work_', '.csv');

        $normalizedFile = $this->normalizer->normalize(new SplFileInfo($source), $target, ',');

        // 4 records (no skip requested): header, data row, preserved blank line, last row
        $this->assertSame(4, $normalizedFile->dataRecordCount);
        $content = (string) file_get_contents($target);
        $this->assertTrue(mb_check_encoding($content, 'UTF-8'));
        $this->assertStringNotContainsString("\xEF\xBB\xBF", $content);
        $this->assertSame("name;desc\nCafé;\"line1\nline2\"\n\nlast;row\n", $content);
    }

    public function testUtf8FileIsPreserved(): void
    {
        $source = $this->createFile('src_', '.csv', "name;desc\nÉlégant;déjà vu\n");
        $target = $this->reserveFilePath('work_', '.csv');

        $this->normalizer->normalize(new SplFileInfo($source), $target);

        $this->assertSame("name;desc\nÉlégant;\"déjà vu\"\n", str_replace('"Élégant"', 'Élégant', (string) file_get_contents($target)));
    }

    public function testSpreadsheetIsConvertedThroughTheGivenTargetPath(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['name', 'price'], ['Excel product', '12.5']]);
        $source = $this->reserveFilePath('src_', '.xlsx');
        (new Xlsx($spreadsheet))->save($source);

        $target = $this->reserveFilePath('work_', '.csv');
        $normalizedFile = $this->normalizer->normalize(new SplFileInfo($source), $target);
        $this->assertSame(2, $normalizedFile->dataRecordCount);
        $firstContent = (string) file_get_contents($target);
        $this->assertStringContainsString('name;price', str_replace('"', '', $firstContent));
        $this->assertStringContainsString('Excel product', $firstContent);

        // re-normalizing an updated source with the SAME name to a FRESH target
        // must produce the new content (the legacy converter cached by filename)
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Updated product');
        (new Xlsx($spreadsheet))->save($source);
        $secondTarget = $this->reserveFilePath('work_', '.csv');
        $this->normalizer->normalize(new SplFileInfo($source), $secondTarget);
        $this->assertStringContainsString('Updated product', (string) file_get_contents($secondTarget));
    }

    public function testSkipRowsAreStrippedAtNormalization(): void
    {
        // header + blank line both count toward the skip (physical position)
        $source = $this->createFile('src_', '.csv', "name;desc\n\nfirst;row\nsecond;row\n");
        $target = $this->reserveFilePath('work_', '.csv');

        $normalizedFile = $this->normalizer->normalize(new SplFileInfo($source), $target, ';', 2);

        $this->assertSame(2, $normalizedFile->dataRecordCount);
        $this->assertSame("first;row\nsecond;row\n", (string) file_get_contents($target), 'The working file must contain data records only');
    }

    public function testUtf16FileIsRejected(): void
    {
        $source = $this->createFile('src_', '.csv', "\xFF\xFE" . mb_convert_encoding("name;desc\n", 'UTF-16LE'));

        $this->expectException(MalformedImportFileException::class);
        $this->normalizer->normalize(new SplFileInfo($source), $this->reserveFilePath('work_', '.csv'));
    }

    private function createFile(string $prefix, string $suffix, string $content): string
    {
        $path = $this->reserveFilePath($prefix, $suffix);
        file_put_contents($path, $content);

        return $path;
    }

    private function reserveFilePath(string $prefix, string $suffix): string
    {
        $path = sys_get_temp_dir() . '/' . uniqid($prefix, true) . $suffix;
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
