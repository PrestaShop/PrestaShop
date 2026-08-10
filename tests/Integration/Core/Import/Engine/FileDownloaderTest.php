<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\FileDownloader;
use Tests\Resources\DummyFileUploader;

class FileDownloaderTest extends TestCase
{
    private FileDownloader $downloader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->downloader = new FileDownloader();
    }

    public function testLocalPathIsCopiedToATemporaryFile(): void
    {
        $sourcePath = DummyFileUploader::getDummyFilePath('logo.jpg');

        $temporaryPath = $this->downloader->download($sourcePath);

        $this->assertFileExists($temporaryPath);
        $this->assertSame(md5_file($sourcePath), md5_file($temporaryPath));
        @unlink($temporaryPath);
    }

    public function testMissingLocalFileIsRejected(): void
    {
        $this->expectException(FileDownloadException::class);
        $this->downloader->download('/nowhere/does-not-exist.jpg');
    }

    public function testUnsupportedSchemeIsRejected(): void
    {
        $this->expectException(FileDownloadException::class);
        $this->downloader->download('file:///etc/hosts');
    }

    public function testLocalFileOutsideTheAllowedLocationsIsRejected(): void
    {
        // exists and is readable, but lives outside the shop dir and the
        // system temp dir — the two roots local imports are confined to
        $this->expectException(FileDownloadException::class);
        $this->expectExceptionMessage('outside the allowed import locations');
        $this->downloader->download('/etc/hosts');
    }

    public function testLocalFileBiggerThanTheSizeCapIsRejected(): void
    {
        $sourcePath = tempnam(sys_get_temp_dir(), 'ps_import_test');
        file_put_contents($sourcePath, str_repeat('a', 10));

        // the cap is a protected const precisely so it can be overridden
        $downloader = new class() extends FileDownloader {
            protected const MAX_FILE_SIZE_BYTES = 5;
        };

        try {
            $this->expectException(FileDownloadException::class);
            $this->expectExceptionMessage('maximum import file size');
            $downloader->download($sourcePath);
        } finally {
            @unlink($sourcePath);
        }
    }
}
