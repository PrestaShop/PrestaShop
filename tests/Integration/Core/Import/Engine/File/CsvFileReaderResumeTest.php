<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\InvalidResumeCursorException;
use PrestaShop\PrestaShop\Core\Import\File\CsvFileReader;
use PrestaShop\PrestaShop\Core\Import\File\FileOpenerInterface;
use SplFileInfo;

class CsvFileReaderResumeTest extends TestCase
{
    private string $workingFile;

    private CsvFileReader $reader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workingFile = sys_get_temp_dir() . '/' . uniqid('reader_', true) . '.csv';
        // canonical-dialect working file incl. an enclosed multi-line record
        file_put_contents($this->workingFile, "name;desc\nA;\"first\nproduct\"\nB;second\nC;third\n");

        $fileOpener = new class() implements FileOpenerInterface {
            public function open(SplFileInfo $file)
            {
                return fopen($file->getPathname(), 'rb');
            }
        };
        $this->reader = new CsvFileReader($fileOpener);
    }

    protected function tearDown(): void
    {
        @unlink($this->workingFile);
        parent::tearDown();
    }

    public function testBatchedResumeYieldsTheSameRowsAsAFullRead(): void
    {
        $fullRead = $this->readAll(null);
        $this->assertSame([['name', 'desc'], ['A', "first\nproduct"], ['B', 'second'], ['C', 'third']], array_column($fullRead, 'values'));

        // consume two rows, then resume from the second row's cursor
        $firstBatch = array_slice($fullRead, 0, 2);
        $resumed = $this->readAll(end($firstBatch)['cursor']);

        $this->assertSame(
            array_column(array_slice($fullRead, 2), 'values'),
            array_column($resumed, 'values'),
            'Resuming from a cursor must yield exactly the remaining rows'
        );

        // every cursor is resumable, including across the multi-line record
        foreach ($fullRead as $index => $row) {
            $resumedRows = $this->readAll($row['cursor']);
            $this->assertSame(array_column(array_slice($fullRead, $index + 1), 'values'), array_column($resumedRows, 'values'));
        }
    }

    public function testInvalidCursorIsRejected(): void
    {
        $this->expectException(InvalidResumeCursorException::class);
        iterator_to_array($this->reader->readFrom(new SplFileInfo($this->workingFile), 'not-a-byte-offset'));
    }

    public function testLegacyReadStillWorks(): void
    {
        $rows = [];
        foreach ($this->reader->read(new SplFileInfo($this->workingFile)) as $dataRow) {
            $values = [];
            foreach ($dataRow as $cell) {
                $values[] = $cell->getValue();
            }
            $rows[] = $values;
        }

        $this->assertSame([['name', 'desc'], ['A', "first\nproduct"], ['B', 'second'], ['C', 'third']], $rows);
    }

    /**
     * @return list<array{cursor: string, values: array<int, string>}>
     */
    private function readAll(?string $cursor): array
    {
        $rows = [];
        foreach ($this->reader->readFrom(new SplFileInfo($this->workingFile), $cursor) as $rowCursor => $record) {
            $rows[] = ['cursor' => $rowCursor, 'values' => $record];
        }

        return $rows;
    }
}
