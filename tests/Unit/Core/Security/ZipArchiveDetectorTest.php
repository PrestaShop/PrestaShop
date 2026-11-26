<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\ZipArchiveDetector;

class ZipArchiveDetectorTest extends TestCase
{
    private const DUMMY_FILES_DIR = __DIR__ . '/../../../Resources/dummyFile/';

    /**
     * @dataProvider provideFiles
     */
    public function testIsZipArchive(string $filePath, bool $expected): void
    {
        self::assertSame($expected, (new ZipArchiveDetector())->isZipArchive($filePath));
    }

    public static function provideFiles(): iterable
    {
        yield 'zip archive' => [self::DUMMY_FILES_DIR . 'valid_module.zip', true];
        // Guessed as application/octet-stream by fileinfo since PHP 8.6
        yield 'zip archive detected as Microsoft OOXML' => [self::DUMMY_FILES_DIR . 'ooxml_detected_module.zip', true];
        yield 'tar archive' => [self::DUMMY_FILES_DIR . 'valid_module.tar.gz', false];
        yield 'text file' => [self::DUMMY_FILES_DIR . 'test_text_file.txt', false];
        yield 'image' => [self::DUMMY_FILES_DIR . 'logo.jpg', false];
        yield 'missing file' => [self::DUMMY_FILES_DIR . 'missing_' . uniqid() . '.zip', false];
        yield 'folder' => [self::DUMMY_FILES_DIR, false];
    }

    public function testEmptyFileIsNotZipArchive(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'ps-zip-detector-');

        try {
            self::assertFalse((new ZipArchiveDetector())->isZipArchive($file));
        } finally {
            unlink($file);
        }
    }
}
