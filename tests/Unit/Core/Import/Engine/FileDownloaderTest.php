<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\FileDownloader;
use Symfony\Component\Filesystem\Filesystem;

class FileDownloaderTest extends TestCase
{
    private string $contentRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentRoot = sys_get_temp_dir() . '/' . uniqid('ps_import_root_', true);
        mkdir($this->contentRoot);
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->contentRoot . '/*') as $file) {
            @unlink((string) $file);
        }
        @rmdir($this->contentRoot);
        parent::tearDown();
    }

    public function testAFileInsideAnInjectedRootIsAccepted(): void
    {
        $sourcePath = $this->contentRoot . '/file.txt';
        file_put_contents($sourcePath, 'content');

        $temporaryPath = (new FileDownloader(new Filesystem(), [$this->contentRoot]))->download($sourcePath);

        $this->assertFileExists($temporaryPath);
        $this->assertSame('content', file_get_contents($temporaryPath));
        @unlink($temporaryPath);
    }

    /**
     * The point of the confinement: a readable file OUTSIDE the injected content
     * directories (in production: app/config/parameters.php, .env under the shop
     * root) must be rejected — a fetched file can become a downloadable virtual
     * product file.
     */
    public function testAReadableFileOutsideTheInjectedRootsIsRejected(): void
    {
        $configurationFile = __DIR__ . '/../../../bootstrap.php';
        $this->assertFileExists($configurationFile);

        $this->expectException(FileDownloadException::class);
        $this->expectExceptionMessage('outside the allowed import locations');
        (new FileDownloader(new Filesystem(), [$this->contentRoot]))->download($configurationFile);
    }

    /**
     * The system temp dir is always allowed on top of the injected roots: it is
     * where the downloader's own fetched copies land, and where callers stage
     * files (the legacy upload flow copies into it too).
     */
    public function testTheSystemTempDirIsAllowedEvenWithNoInjectedRoots(): void
    {
        $sourcePath = tempnam(sys_get_temp_dir(), 'ps_import_unit');
        file_put_contents($sourcePath, 'content');

        try {
            $temporaryPath = (new FileDownloader(new Filesystem()))->download($sourcePath);

            $this->assertFileExists($temporaryPath);
            $this->assertSame('content', file_get_contents($temporaryPath));
            @unlink($temporaryPath);
        } finally {
            @unlink($sourcePath);
        }
    }

    /**
     * realpath() resolves ../ BEFORE the prefix comparison: a path that starts
     * inside an allowed root but traverses out of it must be rejected. The
     * escape target is a repo file, because escaping toward the system temp dir
     * would land in an always-allowed root and prove nothing.
     */
    public function testTraversalOutOfAnInjectedRootIsRejected(): void
    {
        $allowedRoot = __DIR__;
        $sourcePath = $allowedRoot . '/../../../bootstrap.php';
        $this->assertFileExists($sourcePath);

        $this->expectException(FileDownloadException::class);
        $this->expectExceptionMessage('outside the allowed import locations');
        (new FileDownloader(new Filesystem(), [$allowedRoot]))->download($sourcePath);
    }
}
