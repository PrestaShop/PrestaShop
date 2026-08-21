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

    /**
     * @var list<string>
     */
    private array $outsideDirectories = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentRoot = sys_get_temp_dir() . '/' . uniqid('ps_import_root_', true);
        mkdir($this->contentRoot);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove([$this->contentRoot, ...$this->outsideDirectories]);
        $this->outsideDirectories = [];
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
        $outsideFile = $this->createFileOutsideTheAllowedRoots();

        $this->expectException(FileDownloadException::class);
        $this->expectExceptionMessage('outside the allowed import locations');
        $this->confinedToInjectedRootsOnly([$this->contentRoot])->download($outsideFile);
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
     * inside an allowed root but traverses out of it must be rejected.
     */
    public function testTraversalOutOfAnInjectedRootIsRejected(): void
    {
        $outsideFile = $this->createFileOutsideTheAllowedRoots();
        $allowedRoot = $this->contentRoot;
        // starts inside the allowed root, climbs back out to the escape target
        $traversingPath = $allowedRoot . '/../' . basename(dirname($outsideFile)) . '/' . basename($outsideFile);
        $this->assertFileExists($traversingPath);

        $this->expectException(FileDownloadException::class);
        $this->expectExceptionMessage('outside the allowed import locations');
        $this->confinedToInjectedRootsOnly([$allowedRoot])->download($traversingPath);
    }

    /**
     * A downloader whose ONLY allowed roots are the injected ones.
     *
     * The production class always allows the system temp dir on top (it is
     * where its own fetched copies land), which makes every temp path an
     * unusable escape target — and using a repo file instead made these two
     * tests fail for anyone whose checkout lives under /tmp or $TMPDIR, since
     * the repo was then inside an always-allowed root. Narrowing the roots is
     * what the protected method is for, and it keeps the assertions independent
     * of where the checkout happens to sit.
     *
     * @param list<string> $allowedLocalRoots
     */
    private function confinedToInjectedRootsOnly(array $allowedLocalRoots): FileDownloader
    {
        return new class(new Filesystem(), $allowedLocalRoots) extends FileDownloader {
            protected function getAllowedLocalRoots(): array
            {
                return $this->allowedLocalRoots;
            }
        };
    }

    /**
     * A readable file that is outside contentRoot, in its own sibling directory
     * so a '../' traversal can reach it.
     */
    private function createFileOutsideTheAllowedRoots(): string
    {
        $outsideDirectory = dirname($this->contentRoot) . '/' . uniqid('ps_import_outside_', true);
        mkdir($outsideDirectory);
        $this->outsideDirectories[] = $outsideDirectory;

        $outsideFile = $outsideDirectory . '/secret.txt';
        file_put_contents($outsideFile, 'must never be reachable');

        return $outsideFile;
    }
}
